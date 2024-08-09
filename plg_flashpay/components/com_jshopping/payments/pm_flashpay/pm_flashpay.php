<?php
/**
 * @version      5.0.0 15.09.2018
 * @author       MAXXmarketing GmbH
 * @package      Jshopping
 * @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
 * @license      GNU/GPL
 */

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

defined('_JEXEC') or die();

class pm_flashpay extends PaymentRoot {

    private $curlopt_sslversion = 6;

    function __construct() {
        $jshopConfig = JSFactory::getConfig();

        JSFactory::loadExtLanguageFile('pm_flashpay');
    }

    //function call in admin
    function showAdminFormParams($params){
        $array_params = array('testmode',
            't_host',
            't_host_status',
            't_shop_id',
            't_shop_secret_key',
            'p_host',
            'p_host_status',
            'p_shop_id',
            'p_shop_secret_key',
            'sendfiscal',
            'fiscal_email',
            'transaction_end_status',
            'transaction_pending_status',
            'transaction_failed_status',
        );
        foreach ($array_params as $key){
            if (!isset($params[$key])) $params[$key] = '';
        }
        if (!isset($params['address_override'])) $params['address_override'] = 0;

        $orders = \JSFactory::getModel('orders');
        include(dirname(__FILE__)."/adminparamsform.php");
    }

    function checkTransaction($pmconfigs, $order, $act) {
        if ($pmconfigs['testmode']) {
            $host = $pmconfigs['t_host_status'];
            $project_id = $pmconfigs['t_shop_id'];
            $project_secret = $pmconfigs['t_shop_secret_key'];
        } else {
            $host = $pmconfigs['p_host_status'];
            $project_id = $pmconfigs['p_shop_id'];
            $project_secret = $pmconfigs['p_shop_secret_key'];
        }

        $req = [
            'project_id' => $project_id,
            'order_id' => $order->order_id
        ];

        $ch = curl_init($host . '/v1/status');
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($req));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_SSLVERSION, $this->curlopt_sslversion);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'X-signature: '.hash_hmac('sha256', json_encode($req), $project_secret)
        ));

        if (!($res = json_decode(curl_exec($ch),true))) {
            \JSHelper::saveToLog("payment.log", "FlashPay failed: " . curl_error($ch) . '(' . curl_errno($ch) . ')');
            curl_close($ch);
            exit;
        } else {
            curl_close($ch);
        }

        $order->order_total = $res['operation']['amount'];

        $payment_status = trim($res['operation']['status']);
        $transaction = $res['transaction_id'];
        $transaction_data = array('transaction_id' => $res['transaction_id'], 'currency' => $res['operation']['currency'], 'amount' => $res['operation']['amount'], 'status' => $res['operation']['status']);

        $errors = 0;

        if ($res['operation']['amount'] != $order->order_total) {
            \JSHelper::saveToLog("payment.log","Status pending. Order ID ".$order->order_id.". Amount mismatch.".$this->fixOrderTotal($order));
            $errors += 1;
        }

        if ($res['operation']['currency'] != $order->currency_code_iso) {
            \JSHelper::saveToLog("payment.log","Status pending. Order ID ".$order->order_id.". Currency mismatch.");
            $errors += 1;
        }

        if ($errors > 0) {
            return array(0, 'Error to verify order. Order ID: '.$order->order_id);
        }

        if ($payment_status !== 'Decline') {
            if ($payment_status !== 'Success') {
                \JSHelper::saveToLog("payment.log", "Status processing. Order ID " . $order->order_id);
                return array(2, "Status processing. Order ID " . $order->order_id, $transaction, $transaction_data);
            } else {
                return array(1, '', $transaction, $transaction_data);
            }
        }  else {
            return array(3, "Status $payment_status. Order ID " . $order->order_id, $transaction, $transaction_data);
        }

    }

    function showEndForm($pmconfigs, $order) {
        $pm_method = $this->getPmMethod();

        if ($pmconfigs['testmode']) {
            $host = $pmconfigs['t_host'];
            $project_id = $pmconfigs['t_shop_id'];
            $project_secret = $pmconfigs['t_shop_secret_key'];
        } else {
            $host = $pmconfigs['p_host'];
            $project_id = $pmconfigs['p_shop_id'];
            $project_secret = $pmconfigs['p_shop_secret_key'];
        }

        $uri = \JURI::getInstance();
        $liveurlhost = $uri->toString(array("scheme", 'host', 'port'));

        $user      = \Joomla\CMS\User\User::getInstance($order->user_id);

        $inputs     = [
            'project_id'            => $project_id,
            'order_id'              => $order->order_id,
            'payment_method_type'   => 'card',
            'payment'    => [
                'amount'   => $this->fixOrderTotal($order),
                'currency' => $order->currency_code_iso,
            ],
            'customer' => array_filter([
                'id'         => $order->user_id,
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'phone'      => $user->get('phone'),
                'email'      => $user->get('email'),
                'first_name' => $user->get('first_name') ?? null,
                'last_name'  => $user->get('last_name') ?? null,
            ]),
            'return_url' => $liveurlhost
        ];

        if ($pmconfigs['sendfiscal']) {
            $inputs['fiscal_data'] = [];

            foreach ($order->getAllItems() as $product) {

                $items[] = [
                    'Name'          => mb_substr($product->product_name, 0, 64, 'UTF-8'),
                    'Price'         => (int)(string)($product->product_item_price * 100),
                    'Quantity'      => (int)$product->product_quantity,
                    'Amount'        => (int)$product->product_item_price * (int)$product->product_quantity * 100,
                    'PaymentMethod' => 'full_payment',
                    'PaymentObject' => 'service',
                    'Tax'           => (int)(string)($product->product_tax),
                ];

                // FFD 1.2
                foreach ($items as $key => $item) {
                    $items[$key]['MeasurementUnit'] = 'pc';
                }
            }

            $isShipping = false;
            if ((int)$order->order_shipping > 0) {
                $shippingMethod = $order->getShipping();

                $items[] = [
                    'Name'          => mb_substr($shippingMethod->getName(), 0, 64,  'UTF-8'),
                    'Price'         => (int)($order->order_tax * 100),
                    'Quantity'      => 1,
                    'Amount'        => (int)($order->order_tax * 100),
                    'PaymentMethod' => 'full_payment',
                    'PaymentObject' => 'service',
                    'Tax'           => (int)(string)($order->shipping_tax),
                ];

                // FFD 1.2
                foreach ($items as $key => $item) {
                    $items[$key]['MeasurementUnit'] = 'pc';
                }

                $isShipping = true;
            }


            $items = $this->balanceAmountForItems($isShipping, $items, $order->order_total);

            $fiscalEmail = trim(mb_substr($pmconfigs['fiscal_email'], 0, 64, 'UTF-8'));
            if (!preg_match('~^[^@]+@[^@]+\.[^@]+?$~s', $fiscalEmail)) {
                $fiscalEmail = 'none';
            }

            $inputs['fiscal_data']['Receipt'] = [
                'EmailCompany' => $fiscalEmail,
                'Email'        => $order->email,
                'Phone'        => $order->phone,
                'Taxation'     => $order->order_tax,
                'Items'        => $items,
            ];

            // FFD 1.2
            $inputs['fiscal_data']['Receipt']['FfdVersion'] = '1.2';
        }

        $signature = hash_hmac('sha256', json_encode($inputs), $project_secret);
        echo \JText::_('JSHOP_REDIRECT_TO_PAYMENT_PAGE');
        ?>
        <form id="paymentform" action="<?php print $host ?>/payment" name="paymentform" method="get">
            <input type="hidden" name="language" value="<?=htmlspecialchars('en')?>" />
            <input type="hidden" name="body" value="<?=htmlspecialchars(json_encode($inputs))?>" />
            <input type="hidden" name="signature" value="<?=htmlspecialchars($signature)?>" />
        </form>
        <script>
            document.getElementById('paymentform').submit();
        </script>
        <?php

        die;
    }

    function getUrlParams($pmconfigs) {
        $params = array();
        $params['order_id'] = \JFactory::getApplication()->input->json->getArray()['order_id'];
        $params['hash'] = "";
        $params['checkHash'] = 0;
        $params['checkReturnParams'] = 0;
        return $params;
    }

    function fixOrderTotal($order) {
        $total = $order->order_total;
        return (int)(string)($total * 100);
    }

    private function balanceAmountForItems(bool $isShipping, array $items, int $amount): array
    {
        $itemsWithoutShipping = $items;

        if ($isShipping) {
            $shipping = array_pop($itemsWithoutShipping);
        }

        $sum = 0;

        foreach ($itemsWithoutShipping as $item) {
            $sum += $item['Amount'];
        }

        if (isset($shipping)) {
            $sum += $shipping['Amount'];
        }

        if ($sum !== $amount) {
            $sumAmountNew = 0;
            $difference = $amount - $sum;
            $amountNews = [];

            foreach ($itemsWithoutShipping as $key => $item) {
                $itemsAmountNew = $item['Amount'] + floor($difference * $item['Amount'] / $sum);
                $amountNews[$key] = $itemsAmountNew;
                $sumAmountNew += $itemsAmountNew;
            }

            if (isset($shipping)) {
                $sumAmountNew += $shipping['Amount'];
            }

            if ($sumAmountNew !== $amount) {
                $maxKey = array_keys($amountNews, max($amountNews))[0];    // key of max value
                $amountNews[$maxKey] = max($amountNews) + ($amount - $sumAmountNew);
            }

            foreach ($amountNews as $key => $item) {
                $items[$key]['Amount'] = $item;
            }
        }

        return $items;
    }
}
