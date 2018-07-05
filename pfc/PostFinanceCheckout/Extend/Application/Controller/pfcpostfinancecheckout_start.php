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



use Pfc\PostFinanceCheckout\Application\Controller\Cron;


/**
 * Class used to include tracking device id on basket.
 *
 * Class BasketController.
 * Extends \start.
 *
 * @mixin \start
 */
class pfcpostfinancecheckout_start extends pfcpostfinancecheckout_start_parent
{
    public function render()
    {
        $this->_aViewData['pfcCronUrl'] = Cron::getCronUrl();
        return $this->_StartController_render_parent();
    }

    protected function _StartController_render_parent()
    {
        return parent::render();
    }
}