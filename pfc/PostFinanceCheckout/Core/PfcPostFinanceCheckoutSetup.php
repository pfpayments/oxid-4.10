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

require_once(OX_BASE_PATH . 'modules/pfc/PostFinanceCheckout/autoload.php');

use Pfc\PostFinanceCheckout\Core\PostFinanceCheckoutModule;

/**
 * Wrapper for installation events, as defining PostFinanceCheckoutModule would interfere with our autoloader.
 * 
 * @author sebastian
 *
 */
class PfcPostFinanceCheckoutSetup {
	public static function onActivate(){
		PostFinanceCheckoutModule::onActivate();
	}
	
	public static function onDeactivate() {
		PostFinanceCheckoutModule::onDeactivate();
	}
}