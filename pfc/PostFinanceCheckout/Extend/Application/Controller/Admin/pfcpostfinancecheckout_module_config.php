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

use Monolog\Logger;
use Pfc\PostFinanceCheckout\Core\PostFinanceCheckoutModule;
use Pfc\PostFinanceCheckout\Core\Service\PaymentService;
use Pfc\PostFinanceCheckout\Core\Webhook\Service as WebhookService;

/**
 * Class BasketItem.
 * Extends \module_config.
 *
 * @mixin \module_config
 */
class pfcpostfinancecheckout_module_config extends pfcpostfinancecheckout_module_config_parent
{

    public function init()
    {
        if ($this->getEditObjectId() == PostFinanceCheckoutModule::instance()->getId() && $this->getFncName() !== 'saveConfVars') {
            // if plugin was inactive before and has settings changed (which we cannot interfere with as extensions are inactive) - force global parameters over current local settings.
            PostFinanceCheckoutModule::settings()->setGlobalParameters($this->getConfig()->getBaseShopId());
        }
        $this->_ModuleConfiguration_init_parent();
    }

    protected function _ModuleConfiguration_init_parent()
    {
        parent::init();
    }

    public function saveConfVars()
    {
        $this->_ModuleConfiguration_saveConfVars_parent();
        if ($this->getEditObjectId() == PostFinanceCheckoutModule::instance()->getId()) {
            try {
            	PostFinanceCheckoutModule::settings()->setGlobalParameters();
            	PostFinanceCheckoutModule::addMessage(PostFinanceCheckoutModule::instance()->translate("Settings saved successfully."));
                // force api client refresh
                PostFinanceCheckoutModule::instance()->getApiClient(true);

                $paymentService = new PaymentService();
                $paymentService->synchronize();
                PostFinanceCheckoutModule::addMessage(PostFinanceCheckoutModule::instance()->translate("Payment methods successfully synchronized."));

                $oldUrl = PostFinanceCheckoutModule::settings()->getWebhookUrl();
                $newUrl = PostFinanceCheckoutModule::instance()->createWebhookUrl();
                if ($oldUrl !== $newUrl) {
                    $webhookService = new WebhookService();
                    $webhookService->uninstall(PostFinanceCheckoutModule::settings()->getSpaceId(), $oldUrl);;
                    $webhookService->install(PostFinanceCheckoutModule::settings()->getSpaceId(), $newUrl);
                    PostFinanceCheckoutModule::settings()->setWebhookUrl($newUrl);
                    PostFinanceCheckoutModule::addMessage(PostFinanceCheckoutModule::instance()->translate("Webhook URL updated successfully."));
                }
            } catch (\Exception $e) {
                PostFinanceCheckoutModule::log(Logger::ERROR, "Unable to synchronize settings: {$e->getMessage()}.");
                PostFinanceCheckoutModule::getUtilsView()->addErrorToDisplay($e->getMessage());
            }
        }
    }

    protected function _ModuleConfiguration_saveConfVars_parent()
    {
        parent::saveConfVars();
    }
}

