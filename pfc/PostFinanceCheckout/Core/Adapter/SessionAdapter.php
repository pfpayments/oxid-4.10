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


namespace Pfc\PostFinanceCheckout\Core\Adapter;
require_once(OX_BASE_PATH . 'modules/pfc/PostFinanceCheckout/autoload.php');

use PostFinanceCheckout\Sdk\Model\AbstractTransactionPending;
use PostFinanceCheckout\Sdk\Model\TransactionCreate;
use PostFinanceCheckout\Sdk\Model\TransactionPending;
use Pfc\PostFinanceCheckout\Core\PostFinanceCheckoutModule;
use Pfc\PostFinanceCheckout\Application\Model\Transaction;

/**
 * Class SessionAdapter
 * Converts Oxid Session Data into data which can be fed into the PostFinanceCheckout SDK.
 *
 * @codeCoverageIgnore
 */
class SessionAdapter implements ITransactionServiceAdapter
{
    private $session = null;
    private $basketAdapter = null;
    private $addressAdapter = null;

    /**
     * SessionAdapter constructor.
     *
     * Checks if user is logged in and basket is present as well, and throws an exception if either is not present.
     *
     * @param \oxsession $session
     * @throws \Exception
     */
    public function __construct(\oxsession $session)
    {
        if(!$session->getUser() || !$session->getBasket()) {
            throw new \Exception("User must be logged in and basket must be present.");
        }
        $this->session = $session;
        $this->basketAdapter = new BasketAdapter($session->getBasket());
        $this->addressAdapter = new AddressAdapter($session->getUser()->getSelectedAddress(), $session->getUser());
    }

    public function getCreateData()
    {
        $transactionCreate = new TransactionCreate();
        if(isset($_COOKIE['PostFinanceCheckout_device_id'])) {
            $transactionCreate->setDeviceSessionIdentifier($_COOKIE['PostFinanceCheckout_device_id']);
        }
        $transactionCreate->setAutoConfirmationEnabled(false);
        $this->applyAbstractTransactionData($transactionCreate);
        return $transactionCreate;
    }

    public function getUpdateData(Transaction $transaction)
    {
        $transactionPending = new TransactionPending();
        $transactionPending->setId($transaction->getTransactionId());
        $transactionPending->setVersion($transaction->getVersion());
        $this->applyAbstractTransactionData($transactionPending);

        if($transaction->getOrderId()) {
            $transactionPending->setFailedUrl(PostFinanceCheckoutModule::getControllerUrl('order', 'pfcError', $transaction->getOrderId()));
class_exists('oxorder');            $order = oxNew('oxorder');
            /* @var $order \oxorder */
            if($order->load($transaction->getOrderId())) {
                $transactionPending->setMerchantReference($order->oxorder__oxordernr->value);
            }
        }

        return $transactionPending;
    }

    private function applyAbstractTransactionData(AbstractTransactionPending $transaction)
    {
        $transaction->setCustomerId($this->session->getUser()->getId());
        $transaction->setCustomerEmailAddress($this->session->getUser()->getFieldData('oxusername'));
        /** @noinspection PhpUndefinedFieldInspection */
        $transaction->setCurrency($this->session->getBasket()->getBasketCurrency()->name);
        $transaction->setLineItems($this->basketAdapter->getLineItemData());
        $transaction->setBillingAddress($this->addressAdapter->getBillingAddressData());
        $transaction->setShippingAddress($this->addressAdapter->getShippingAddressData());
        $transaction->setLanguage(\oxregistry::getLang()->getLanguageAbbr());
        $transaction->setSuccessUrl(PostFinanceCheckoutModule::getControllerUrl('thankyou'));
        $transaction->setFailedUrl(PostFinanceCheckoutModule::getControllerUrl('order', 'pfcError'));
    }
}