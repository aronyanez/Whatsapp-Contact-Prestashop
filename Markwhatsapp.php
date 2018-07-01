<?php
/**
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}


class Markwhatsapp extends Module
{

    public function __construct()
    {
        $this->name = 'mark_whatsapp';
        $this->author = 'Arón Yáñez';
        $this->version = '1.0.0';
        $this->tab = 'social_networks';
        $this->ps_versions_compliancy = array(
            'min' => '1.6',
            'max' => _PS_VERSION_
        );
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Whatsapp Contact');
        $this->description = $this->l('Add Whatsapp Contact');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install()
    {
        return parent::install()
        && $this->registerHook('displayHeader')
        && $this->registerHook('displayHome')
        && Configuration::updateValue('Whats_Number', '4434395115')
        && Configuration::updateValue('Whats_Message', $this->l('I want information'));
    }


    public function uninstall()
    {
        return parent::uninstall()
        && Configuration::deleteByName('Whats_Number')
        && Configuration::deleteByName('Whats_Message');
    }


    public function hookDisplayHeader($params)
    {
         $this->context->controller->addCSS($this->_path.'/views/css/style.css', 'all');
         $this->context->controller->addCSS('https://use.fontawesome.com/releases/v5.0.13/css/all.css', 'all');
    }

    public function hookDisplayHome()
    {
        $this ->context->smarty-> assign(array(
            'Whats_Number' => Configuration::get('Whats_Number'),
            'Whats_Message' => Configuration::get('Whats_Message')
        ));

        return $this->display(__FILE__, 'views/templates/hook/Whatsapphook.tpl');
    }

    public function getContent()
    {
        $output = null;
        if (Tools::isSubmit('submit'.$this->name)) {
            $Whats_Number= (string)Tools::getValue('Whats_Number');
            $Whats_Message= (string)Tools::getValue('Whats_Message');

            if ((!$Whats_Number
                 || empty($Whats_Number)
                 || !Validate::isPhoneNumber($Whats_Number)
                )
                &&
                (
                 !$Whats_Message
                 || empty($Whats_Message)
                 || !Validate::isString($Whats_Message)
             )
            ) {
                $output .= $this->displayError($this->l('Invalid Configuration value'));
            } else {
                    Configuration::updateValue('Whats_Number', $Whats_Number);
                    Configuration::updateValue('Whats_Message', $Whats_Message);
                    $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }
        return $output.$this->displayForm();
    }


    public function displayForm()
    {
            // Get default language
        $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');
        $fieldsForm=array();

            // Init Fields form array
        $fieldsForm[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings'),
            ),
            'input' =>  array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Phone Number'),
                    'desc' => $this->l('Your phone number'),
                    'name' => 'Whats_Number',
                    'size' => 10,
                    'required' => true,
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Message'),
                    'desc' => $this->l('Your initial Message'),
                    'name' => 'Whats_Message',
                    'size' => 100,
                    'required' => true,
                )
            ),

            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            )
        );

        $helper = new HelperForm();

        // Module, Token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        // Language
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;


        // title and Toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = array(
            'save' => array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                '&token='.Tools::getAdminTokenLite('AdminModules'),
            ),
            'back' => array(
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );

            // Load current value
        $helper->fields_value['Whats_Number'] = Configuration::get('Whats_Number');
        $helper->fields_value['Whats_Message'] = Configuration::get('Whats_Message');

        return $helper->generateForm($fieldsForm);
    }
}
