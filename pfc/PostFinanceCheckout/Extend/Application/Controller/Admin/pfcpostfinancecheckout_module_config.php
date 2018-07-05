<?php
/**
 * PostFinanceCheckout OXID
 *
 * This OXID module enables to process payments with PostFinanceCheckout (https://www.postfinance.ch/).
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
                // force api client refresh
                PostFinanceCheckoutModule::instance()->getApiClient(true);

                $paymentService = new PaymentService();
                $paymentService->synchronize();

                $oldUrl = PostFinanceCheckoutModule::settings()->getWebhookUrl();
                $newUrl = PostFinanceCheckoutModule::instance()->createWebhookUrl();
                if ($oldUrl !== $newUrl) {
                    $webhookService = new WebhookService();
                    $webhookService->uninstall(PostFinanceCheckoutModule::settings()->getSpaceId(), $oldUrl);;
                    $webhookService->install(PostFinanceCheckoutModule::settings()->getSpaceId(), $newUrl);
                    PostFinanceCheckoutModule::settings()->setWebhookUrl($newUrl);
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
