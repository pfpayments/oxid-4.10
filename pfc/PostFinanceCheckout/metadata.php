<?php
/**
 * PostFinanceCheckout OXID
 *
 * This OXID module enables to process payments with PostFinanceCheckout (https://www.postfinance.ch/).
 *
 * @package Whitelabelshortcut\PostFinanceCheckout
 * @author customweb GmbH (http://www.customweb.com/)
 * @license http://www.apache.org/licenses/LICENSE-2.0  Apache Software License (ASL 2.0)
 */
require_once('postfinancecheckout-sdk/autoload.php');



/**
 * Metadata version
 */
$sMetadataVersion = '1.1';

/**
 * Module information
 */
$aModule = array(
    'id' => 'pfcPostFinanceCheckout',
    'title' => array(
        'de' => 'PFC :: PostFinanceCheckout',
        'en' => 'PFC :: PostFinanceCheckout'
    ),
    'description' => array(
        'de' => 'PFC PostFinanceCheckout Module',
        'en' => 'PFC PostFinanceCheckout Module'
    ),
    'thumbnail' => 'out/pictures/picture.png',
    'version' => '1.0.7',
    'author' => 'customweb GmbH',
    'url' => 'https://www.customweb.com',
    'email' => 'info@customweb.com',
    'extend' => array(
    	'oxorder' => 'pfc/PostFinanceCheckout/Extend/Application/Model/pfcpostfinancecheckout_oxorder',
    	'oxpaymentlist' => 'pfc/PostFinanceCheckout/Extend/Application/Model/pfcpostfinancecheckout_payment_list',
    	'oxbasketitem' => 'pfc/PostFinanceCheckout/Extend/Application/Model/pfcpostfinancecheckout_oxbasketitem',
    	'oxstart' => 'pfc/PostFinanceCheckout/Extend/Application/Controller/pfcpostfinancecheckout_start',
    	'basket' => 'pfc/PostFinanceCheckout/Extend/Application/Controller/pfcpostfinancecheckout_basket',
    	'order' => 'pfc/PostFinanceCheckout/Extend/Application/Controller/pfcpostfinancecheckout_order',
    	'login' => 'pfc/PostFinanceCheckout/Extend/Application/Controller/Admin/pfcpostfinancecheckout_login',
    	'module_config' => 'pfc/PostFinanceCheckout/Extend/Application/Controller/Admin/pfcpostfinancecheckout_module_config',
    	'navigation' => 'pfc/PostFinanceCheckout/Extend/Application/Controller/Admin/pfcpostfinancecheckout_navigation',
    	'order_list' => 'pfc/PostFinanceCheckout/Extend/Application/Controller/Admin/pfcpostfinancecheckout_order_list',
    ),
	'files' => array(
		'PfcPostFinanceCheckoutSetup' => 'pfc/PostFinanceCheckout/Core/PfcPostFinanceCheckoutSetup.php',
		'pfc_postFinanceCheckout_Webhook' => 'pfc/PostFinanceCheckout/Application/Controller/pfc_postFinanceCheckout_Webhook.php',
		'pfc_postFinanceCheckout_Pdf' => 'pfc/PostFinanceCheckout/Application/Controller/pfc_postFinanceCheckout_Pdf.php',
		'pfc_postFinanceCheckout_Alert' => 'pfc/PostFinanceCheckout/Application/Controller/Admin/pfc_postFinanceCheckout_Alert.php',
		'pfc_postFinanceCheckout_RefundJob' => 'pfc/PostFinanceCheckout/Application/Controller/Admin/pfc_postFinanceCheckout_RefundJob.php',
		'pfc_postFinanceCheckout_Transaction' => 'pfc/PostFinanceCheckout/Application/Controller/Admin/pfc_postFinanceCheckout_Transaction.php',
    ),
    'templates' => array(
    	'pfcPostFinanceCheckoutCheckoutBasket.tpl' => 'pfc/PostFinanceCheckout/Application/views/pages/pfcPostFinanceCheckoutCheckoutBasket.tpl',
        'pfcPostFinanceCheckoutCheckoutBasket.tpl' => 'pfc/PostFinanceCheckout/Application/views/pages/pfcPostFinanceCheckoutCheckoutBasket.tpl',
        'pfcPostFinanceCheckoutCron.tpl' => 'pfc/PostFinanceCheckout/Application/views/pages/pfcPostFinanceCheckoutCron.tpl',
        'pfcPostFinanceCheckoutError.tpl' => 'pfc/PostFinanceCheckout/Application/views/pages/pfcPostFinanceCheckoutError.tpl',
        'pfcPostFinanceCheckoutTransaction.tpl' => 'pfc/PostFinanceCheckout/Application/views/admin/tpl/pfcPostFinanceCheckoutTransaction.tpl',
        'pfcPostFinanceCheckoutRefundJob.tpl' => 'pfc/PostFinanceCheckout/Application/views/admin/tpl/pfcPostFinanceCheckoutRefundJob.tpl',
    	'pfcPostFinanceCheckoutOrderList.tpl' => 'pfc/PostFinanceCheckout/Application/views/admin/tpl/pfcPostFinanceCheckoutOrderList.tpl',
    	'pfcPostFinanceCheckoutHeader.tpl' => 'pfc/PostFinanceCheckout/Application/views/admin/tpl/pfcPostFinanceCheckoutHeader.tpl',
    ),
    'blocks' => array(
        array(	
            'template' => 'page/checkout/order.tpl',
            'block' => 'checkout_order_btn_confirm_bottom',
            'file' => 'Application/views/blocks/pfcPostFinanceCheckout_checkout_order_btn_confirm_bottom.tpl'
        ),
        array(
            'template' => 'layout/base.tpl',
            'block' => 'base_js',
            'file' => 'Application/views/blocks/pfcPostFinanceCheckout_include_cron.tpl'
        ),
        array(
            'template' => 'login.tpl',
            'block' => 'admin_login_form',
            'file' => 'Application/views/blocks/pfcPostFinanceCheckout_include_cron.tpl'
        ),
        array(
            'template' => 'pfcPostFinanceCheckoutHeader.tpl',
            'block' => 'admin_header_links',
            'file' => 'Application/views/blocks/pfcPostFinanceCheckout_admin_header_links.tpl'
        ),
    	array(
    		'template' => 'page/account/order.tpl',
    		'block' => 'account_order_history',
    		'file' => 'Application/views/blocks/pfcPostFinanceCheckout_account_order_history.tpl'
    	),
    ),
    'settings' => array(
        array(
            'group' => 'pfcPostFinanceCheckoutGlobalSettings',
            'name' => 'pfcPostFinanceCheckoutAppKey',
            'type' => 'str',
            'value' => ''
        ),
        array(
            'group' => 'pfcPostFinanceCheckoutGlobalSettings',
            'name' => 'pfcPostFinanceCheckoutUserId',
            'type' => 'str',
            'value' => ''
        ),
        array(
            'group' => 'pfcPostFinanceCheckoutSettings',
            'name' => 'pfcPostFinanceCheckoutSpaceId',
            'type' => 'str',
            'value' => ''
        ),
        array(
            'group' => 'pfcPostFinanceCheckoutSettings',
            'name' => 'pfcPostFinanceCheckoutSpaceViewId',
            'type' => 'str',
            'value' => ''
        ),
        array(
            'group' => 'pfcPostFinanceCheckoutSettings',
            'name' => 'pfcPostFinanceCheckoutEmailConfirm',
            'type' => 'bool',
            'value' => true
        ),
        array(
            'group' => 'pfcPostFinanceCheckoutSettings',
            'name' => 'pfcPostFinanceCheckoutInvoiceDoc',
            'type' => 'bool',
            'value' => true
        ),
        array(
            'group' => 'pfcPostFinanceCheckoutSettings',
            'name' => 'pfcPostFinanceCheckoutPackingDoc',
            'type' => 'bool',
            'value' => true
        ),
        array(
            'group' => 'pfcPostFinanceCheckoutSettings',
            'name' => 'pfcPostFinanceCheckoutLogLevel',
            'type' => 'select',
            'value' => 'Error',
            'constrains' => 'Error|Info|Debug'
        )
    ),
    'events' => array(
        'onActivate' => 'PfcPostFinanceCheckoutSetup::onActivate',
        'onDeactivate' => 'PfcPostFinanceCheckoutSetup::onDeactivate'
    ),
    'transaction_states' => array(
        'POSTFINANCECHECKOUT_' . \PostFinanceCheckout\Sdk\Model\TransactionState::DECLINE,
        'POSTFINANCECHECKOUT_' . \PostFinanceCheckout\Sdk\Model\TransactionState::FULFILL,
        'POSTFINANCECHECKOUT_' . \PostFinanceCheckout\Sdk\Model\TransactionState::COMPLETED,
        'POSTFINANCECHECKOUT_' . \PostFinanceCheckout\Sdk\Model\TransactionState::PENDING,
        'POSTFINANCECHECKOUT_' . \PostFinanceCheckout\Sdk\Model\TransactionState::FAILED,
        'POSTFINANCECHECKOUT_' . \PostFinanceCheckout\Sdk\Model\TransactionState::AUTHORIZED,
        'POSTFINANCECHECKOUT_' . \PostFinanceCheckout\Sdk\Model\TransactionState::CONFIRMED,
        'POSTFINANCECHECKOUT_' . \PostFinanceCheckout\Sdk\Model\TransactionState::VOIDED,
        'POSTFINANCECHECKOUT_' . \PostFinanceCheckout\Sdk\Model\TransactionState::PROCESSING
    )
);