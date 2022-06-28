<?php
/**
 * PostFinanceCheckout OXID
 *
 * This OXID module enables to process payments with PostFinanceCheckout (https://postfinance.ch/en/business/products/e-commerce/postfinance-checkout-all-in-one.html/).
 *
 * @package Whitelabelshortcut\PostFinanceCheckout
 * @author customweb GmbH (http://www.customweb.com/)
 * @license http://www.apache.org/licenses/LICENSE-2.0  Apache Software License (ASL 2.0)
 */require_once(OX_BASE_PATH . "modules/pfc/PostFinanceCheckout/autoload.php");



use Pfc\PostFinanceCheckout\Extend\Application\Model\Order;

/**
 * Class NavigationController.
 * Extends \order_list.
 *
 * @mixin \order_list
 */
class pfcpostfinancecheckout_order_list extends pfcpostfinancecheckout_order_list_parent
{
    protected $_sThisTemplate = 'pfcPostFinanceCheckoutOrderList.tpl';

    public function render()
    {
        $orderId = $this->getEditObjectId();
        if ($orderId != '-1' && isset($orderId)) {
class_exists('oxorder');        	$order = oxNew('oxorder');
            $order->load($orderId);
            /* @var $order Order */

            if ($order->isPfcOrder()) {
                $this->_aViewData['pfcEnabled'] = true;
            }
        }
        $this->_OrderList_render_parent();

        return $this->_sThisTemplate;
    }

    protected function _OrderList_render_parent()
    {
        return parent::render();
    }
}