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




use Pfc\PostFinanceCheckout\Application\Model\Transaction;
use Pfc\PostFinanceCheckout\Core\Service\PaymentService;
use Pfc\PostFinanceCheckout\Core\PostFinanceCheckoutModule;
use Monolog\Logger;

/**
 * Class PaymentList.
 * Extends \oxpaymentlist.
 *
 * @mixin \oxpaymentlist
 */
class pfcpostfinancecheckout_payment_list extends pfcpostfinancecheckout_payment_list_parent
{

    /**
     * Loads all PostFinanceCheckout payment methods.
     */
    public function loadPostFinanceCheckoutPayments()
    {
        $prefix = PostFinanceCheckoutModule::PAYMENT_PREFIX;
        $this->selectString("SELECT * FROM `oxpayments` WHERE `oxid` LIKE '$prefix%'");
        return $this->_aArray;
    }

    /**
     * Loads all PostFinanceCheckout payment methods.
     */
    public function loadActivePostFinanceCheckoutPayments()
    {
        $prefix = PostFinanceCheckoutModule::PAYMENT_PREFIX;
        $this->selectString("SELECT * FROM `oxpayments` WHERE `oxid` LIKE '$prefix%' AND `oxactive` = '1'");
        return $this->_aArray;
    }

    public function getPaymentList($sShipSetId, $dPrice, $oUser = null)
    {
        $oxPayments = $this->_PaymentList_getPaymentList_parent($sShipSetId, $dPrice, $oUser);
        if(!$this->isAdmin()) {
            $this->clear();
            $PostFinanceCheckoutPayments = array();
            try {
                $transaction = Transaction::loadPendingFromSession($this->getSession());
                $PostFinanceCheckoutPayments = PaymentService::instance()->fetchAvailablePaymentMethods($transaction->getTransactionId(), $transaction->getSpaceId());
            } catch (\Exception $e) {
                PostFinanceCheckoutModule::log(Logger::ERROR, $e->getMessage(), array($this, $e));
            }
            foreach ($oxPayments as $oxPayment) {
                /* @var $oxPayment \oxpayment */
                if (PostFinanceCheckoutModule::isPostFinanceCheckoutPayment($oxPayment->getId())) {
                    if (in_array($oxPayment->getId(), $PostFinanceCheckoutPayments)) {
                        $this->add($oxPayment);
                    }
                } else {
                    $this->add($oxPayment);
                }
            }
        }
        return $this->_aArray;
    }

    protected function _PaymentList_getPaymentList_parent($sShipSetId, $dPrice, $oUser = null)
    {
        return parent::getPaymentList($sShipSetId, $dPrice, $oUser);
    }
}