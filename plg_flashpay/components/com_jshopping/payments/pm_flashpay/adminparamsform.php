<?php

defined('_JEXEC') or die();
?>
<div class="col100">
<fieldset class="adminform">
<table class="admintable">
 <tr>
   <td class="key">
     <?php echo \JText::_('JSHOP_TRANSACTION_END')?>
   </td>
   <td>
     <?php
     print \JHTML::_('select.genericlist', $orders->getAllOrderStatus(), 'pm_params[transaction_end_status]', 'class = "inputbox custom-select" size = "1" style="max-width:240px; display: inline-block"', 'status_id', 'name', $params['transaction_end_status'] );
     echo " ".\JSHelperAdmin::tooltip(_JSHOP_FLASHPAY_TRANSACTION_END_DESCRIPTION);
     ?>
   </td>
 </tr>
 <tr>
   <td class="key">
     <?php echo \JText::_('JSHOP_TRANSACTION_PENDING')?>
   </td>
   <td>
     <?php
     echo \JHTML::_('select.genericlist',$orders->getAllOrderStatus(), 'pm_params[transaction_pending_status]', 'class = "inputbox custom-select" size = "1" style="max-width:240px; display: inline-block"', 'status_id', 'name', $params['transaction_pending_status']);
     echo " ".\JSHelperAdmin::tooltip(_JSHOP_FLASHPAY_TRANSACTION_PENDING_DESCRIPTION);
     ?>
   </td>
 </tr>
 <tr>
   <td class="key">
     <?php echo \JText::_('JSHOP_TRANSACTION_FAILED')?>
   </td>
   <td>
     <?php
     echo \JHTML::_('select.genericlist',$orders->getAllOrderStatus(), 'pm_params[transaction_failed_status]', 'class = "inputbox custom-select" size = "1" style="max-width:240px; display: inline-block"', 'status_id', 'name', $params['transaction_failed_status']);
     echo " ".\JSHelperAdmin::tooltip(_JSHOP_FLASHPAY_TRANSACTION_FAILED_DESCRIPTION);
     ?>
   </td>
 </tr>
    <tr>
        <td class="key">
            <?php echo _JSHOP_FLASHPAY_SEND_FISCAL?>
        </td>
        <td class="key">
            <?php
            print \JHTML::_('select.booleanlist', 'pm_params[sendfiscal]', 'class = "inputbox" size = "1"', $params['sendfiscal']);
            echo " ".\JSHelperAdmin::tooltip(_JSHOP_FLASHPAY_SEND_FISCAL_DESCRIPTION);
            ?>
        </td>
        <td></td>
        <td></td class="key">
        <td></td class="key">
    </tr>
    <tr>
        <td><?php echo _JSHOP_FLASHPAY_FISCAL_EMAIL_NAME?></td>
        <td><input type = "text" class = "inputbox form-control" name = "pm_params[fiscal_email]" value = "<?php echo $params['fiscal_email']?>"/>
            <?php echo " ".\JSHelperAdmin::tooltip(_JSHOP_FLASHPAY_FISCAL_EMAIL_DESCR)?>
        </td>
    </tr>
    <tr>
        <td class="key">
            <?php echo \JText::_('JSHOP_TESTMODE')?>
        </td>
        <td class="key">
            <?php
            print \JHTML::_('select.booleanlist', 'pm_params[testmode]', 'class = "inputbox" size = "1"', $params['testmode']);
            echo " ".\JSHelperAdmin::tooltip(_JSHOP_FLASHPAY_TESTMODE_DESCRIPTION);
            ?>
        </td>
        <td></td>
        <td></td class="key">
        <td></td class="key">
    </tr>
    <tr style="text-align:left;">
        <td style="border-top: 1px solid; border-left: 1px solid; padding:10px"><?php echo _JSHOP_FESTPAY_TEST_ENV?></td>
        <td style="border-top: 1px solid; border-right: 1px solid; padding:10px"></td>
        <td></td>
        <td style="border-top: 1px solid; border-left: 1px solid; padding:10px"><?php echo _JSHOP_FESTPAY_LIVE_ENV?></td>
        <td style="border-top: 1px solid; border-right: 1px solid; padding:10px"></td>
    </tr>
    <tr>
        <td style="border-left: 1px solid; padding:10px"><?php echo _JSHOP_FLASHPAY_HOST?></td>
        <td style="border-right: 1px solid; padding:10px"><input type = "text" class = "inputbox form-control wide" name = "pm_params[t_host]" value = "<?php echo $params['t_host']?>" /></td>
        <td></td>
        <td style="border-left: 1px solid; padding:10px"><?php echo _JSHOP_FLASHPAY_HOST?></td>
        <td style="border-right: 1px solid; padding:10px"><input type = "text" class = "inputbox form-control wide" name = "pm_params[p_host]" value = "<?php echo $params['p_host']?>" /></td>
    </tr>
    <tr>
        <td style="border-left: 1px solid; padding:10px"><?php echo _JSHOP_FLASHPAY_HOST_STATUS?></td>
        <td style="border-right: 1px solid; padding:10px"><input type = "text" class = "inputbox form-control wide" name = "pm_params[t_host_status]" value = "<?php echo $params['t_host_status']?>" /></td>
        <td></td>
        <td style="border-left: 1px solid; padding:10px"style="border-top: 1px solid; border-left: 1px solid; padding:10px"><?php echo _JSHOP_FLASHPAY_HOST_STATUS?></td>
        <td style="border-right: 1px solid; padding:10px"><input type = "text" class = "inputbox form-control wide" name = "pm_params[p_host_status]" value = "<?php echo $params['p_host_status']?>" /></td>
    </tr>
    <tr>
        <td style="border-left: 1px solid; padding:10px"><?php echo _JSHOP_FLASHPAY_SHOP_ID?></td>
        <td style="border-right: 1px solid; padding:10px"><input type = "text" class = "inputbox form-control wide" name = "pm_params[t_shop_id]" value = "<?php echo $params['t_shop_id']?>" /></td>
        <td></td>
        <td style="border-left: 1px solid; padding:10px"><?php echo _JSHOP_FLASHPAY_SHOP_ID?></td>
        <td style="border-right: 1px solid; padding:10px"><input type = "text" class = "inputbox form-control wide" name = "pm_params[p_shop_id]" value = "<?php echo $params['p_shop_id']?>" /></td>
    </tr>
    <tr>
        <td style="border-left: 1px solid; padding:10px; border-bottom: 1px solid"><?php echo _JSHOP_FLASHPAY_SHOP_SECRET_KEY?></td>
        <td style="border-right: 1px solid; padding:10px; border-bottom: 1px solid"><input type = "text" class = "inputbox form-control wide" name = "pm_params[t_shop_secret_key]" value = "<?php echo $params['t_shop_secret_key']?>" /></td>
        <td></td>
        <td style="border-bottom: 1px solid; border-left: 1px solid; padding:10px"><?php echo _JSHOP_FLASHPAY_SHOP_SECRET_KEY?></td>
        <td style="border-bottom: 1px solid; border-right: 1px solid; padding:10px"><input type = "text" class = "inputbox form-control wide" name = "pm_params[p_shop_secret_key]" value = "<?php echo $params['p_shop_secret_key']?>" /></td>
    </tr>
</table>
</fieldset>
</div>
<div class="clr"></div>
