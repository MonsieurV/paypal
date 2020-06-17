<?php
/**
 * 2007-2020 PrestaShop
 *
 *  NOTICE OF LICENSE
 *
 *  This source file is subject to the Academic Free License (AFL 3.0)
 *  that is bundled with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  http://opensource.org/licenses/afl-3.0.php
 *  If you did not receive a copy of the license and are unable to
 *  obtain it through the world-wide-web, please send an email
 *  to license@prestashop.com so we can send you a copy immediately.
 *
 *  DISCLAIMER
 *
 *  Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 *  versions in the future. If you wish to customize PrestaShop for your
 *  needs please refer to http://www.prestashop.com for more information.
 *
 *  @author 2007-2019 PayPal
 *  @author 202 ecommerce <tech@202-ecommerce.com>
 *  @copyright PayPal
 *  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

use PaypalAddons\classes\API\Onboarding\PaypalGetCredentials;
use PaypalAddons\classes\AbstractMethodPaypal;
use PaypalPPBTlib\Extensions\ProcessLogger\ProcessLoggerHandler;

include_once(_PS_MODULE_DIR_.'paypal/vendor/autoload.php');

class AdminPaypalGetCredentialsController extends ModuleAdminController
{
    public function init()
    {
        parent::init();

        $method = AbstractMethodPaypal::load();
        $authToken = Configuration::get('PAYPAL_AUTH_TOKEN');
        $partnerId = $method->isSandbox() ? PayPal::PAYPAL_PARTNER_ID_SANDBOX : PayPal::PAYPAL_PARTNER_ID_LIVE;
        $paypalGetCredentials = new PaypalGetCredentials($authToken, $partnerId, $method->isSandbox());
        $result = $paypalGetCredentials->execute();

        if ($result->isSuccess()) {
            $params = [
                'clientId' => $result->getClientId(),
                'secret' => $result->getSecret()
            ];
            $method->setConfig($params);
        } else {
            ProcessLoggerHandler::openLogger();
            ProcessLoggerHandler::logError(
                $result->getError()->getMessage(),
                null,
                null,
                null,
                null,
                null,
                $method->isSandbox()
            );
            ProcessLoggerHandler::closeLogger();
        }

        Tools::redirectAdmin($this->context->link->getAdminLink('AdminPayPalSetup', true, [], ['checkCredentials' => 1]));
    }
}

