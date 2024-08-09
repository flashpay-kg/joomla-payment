INSERT IGNORE INTO `#__jshopping_payment_method`
SET
	`payment_code`          = 'flashpay',
	`payment_class`         = 'pm_flashpay',
	`scriptname`            = 'pm_flashpay',
	`payment_publish`       = 0,
	`payment_ordering`      = 0,
    `payment_params`        = '{"transaction_end_status":"6","transaction_pending_status":"1","transaction_failed_status":"3","sendfiscal":"1","fiscal_email":"test@test.test","testmode":"1","t_host":"https://pay-stage.flashpay.ru","p_host":"https://pay.flashpay.ru","t_host_status":"https://endpoint-stage.flashpay.ru","p_host_status":"https://endpoint.flashpay.ru","t_shop_id":"","p_shop_id":"","t_shop_secret_key":"","p_shop_secret_key":""}',
	`payment_type`          = 2,
	`price`                 = 0.00,
	`price_type`            = 1,
	`tax_id`                = -1,
	`show_descr_in_email`   = 0,
	`name_en-GB`            = 'FlashPay',
    `description_en-GB`     = '',
    `order_description`     = ''
;
