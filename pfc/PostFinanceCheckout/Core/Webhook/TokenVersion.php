<?php
/**
 * PostFinanceCheckout OXID
 *
 * This OXID module enables to process payments with PostFinanceCheckout (https://postfinance.ch/en/business/products/e-commerce/postfinance-checkout-all-in-one.html/).
 *
 * @package Whitelabelshortcut\PostFinanceCheckout
 * @author customweb GmbH (http://www.customweb.com/)
 * @license http://www.apache.org/licenses/LICENSE-2.0  Apache Software License (ASL 2.0)
 */
namespace Pfc\PostFinanceCheckout\Core\Webhook;
require_once(OX_BASE_PATH . 'modules/pfc/PostFinanceCheckout/autoload.php');

use Pfc\PostFinanceCheckout\Core\Service\Token as TokenService;

/**
 * Webhook processor to handle token version state transitions.
 */
class TokenVersion extends AbstractWebhook
{

    public function process(Request $request)
    {
        TokenService::instance()->updateTokenVersion($request->getSpaceId(), $request->getEntityId());
    }
}