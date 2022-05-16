<?php
/**
 * 2007-2022 PayPal
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
 *  @author 2007-2022 PayPal
 *  @author 202 ecommerce <tech@202-ecommerce.com>
 *  @copyright PayPal
 *  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace PaypalAddons\classes\PUI;

use Context;
use PaypalAddons\classes\PuiMethodInterface;
use Tools;

class SignUpLinkButton
{
    protected $context;

    protected $method;

    public function __construct(PuiMethodInterface $method)
    {
        $this->context = Context::getContext();
        $this->method = $method;
    }

    public function render()
    {
        $this->context->smarty->assign('actionUrl', $this->getActionUrl());
        $this->context->smarty->assign('paypalOnboardingLib', $this->getOnboardingLib());
        $this->context->smarty->assign('isPuiAvailable', $this->isPuiAvailable());

        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'paypal/views/templates/pui/signUpLinkButton.tpl');
    }

    protected function getActionUrl()
    {
        return $this->initSignupLink()->get();
    }

    protected function getOnboardingLib()
    {
        if ($this->method->isSandbox()) {
            return 'https://www.sandbox.paypal.com/webapps/merchantboarding/js/lib/lightbox/partner.js';
        } else {
            return 'https://www.paypal.com/webapps/merchantboarding/js/lib/lightbox/partner.js';
        }
    }

    protected function initSignupLink()
    {
        return new SignupLink($this->method);
    }

    protected function isPuiAvailable()
    {
        $sellerStatus = $this->method->getSellerStatus();

        if ($sellerStatus->isSuccess() == false) {
            return false;
        }

        if (empty($sellerStatus->getCapabilities())) {
            return false;
        }

        foreach ($sellerStatus->getCapabilities() as $capability) {
            if (Tools::strtoupper($capability) == 'PAY_UPON_INVOICE') {
                return true;
            }
        }

        return false;
    }
}