<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from SARL 202 ecommerce
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL 202 ecommerce is strictly forbidden.
 * In order to obtain a license, please contact us: tech@202-ecommerce.com
 * ...........................................................................
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise a une licence commerciale
 * concedee par la societe 202 ecommerce
 * Toute utilisation, reproduction, modification ou distribution du present
 * fichier source sans contrat de licence ecrit de la part de la SARL 202 ecommerce est
 * expressement interdite.
 * Pour obtenir une licence, veuillez contacter 202-ecommerce <tech@202-ecommerce.com>
 * ...........................................................................
 *
 * @author    202-ecommerce <tech@202-ecommerce.com>
 * @copyright Copyright (c) 202-ecommerce
 * @license   Commercial license
 * @version   develop
 */
 
include_once(_PS_MODULE_DIR_.'paypal/vendor/autoload.php');

use Symfony\Component\HttpFoundation\JsonResponse;
use PaypalAddons\classes\AdminPayPalController;

class AdminPayPalHelpController extends AdminPayPalController
{
    public function initContent()
    {
        parent::initContent();
        if (Tools::isSubmit('download-documentation')) {
            return $this->downloadDocumentation();
        }
        $need_rounding = (Configuration::get('PS_ROUND_TYPE') != Order::ROUND_ITEM) || (Configuration::get('PS_PRICE_ROUND_MODE') != PS_ROUND_HALF_UP);
        $tpl_vars = array(
            'need_rounding' => $need_rounding,
        );
        $this->context->smarty->assign($tpl_vars);
        $this->content = $this->context->smarty->fetch($this->getTemplatePath() . 'help.tpl');
        $this->context->smarty->assign('content', $this->content);
        $this->addJS('modules/' . $this->module->name . '/views/js/helpAdmin.js');
    }

    public function displayAjaxCheckCredentials()
    {
        $response = new JsonResponse($this->_checkRequirements());
        return $response->send();
    }

    public function downloadDocumentation()
    {
        $filePath = _PS_MODULE_DIR_ . $this->module->name . '/PayPal_officiel_user_guide.zip';
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=PayPal_officiel_user_guide.zip");
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: binary");
        readfile($filePath);
    }
}