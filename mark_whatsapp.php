<?php
if (!defined('_PS_VERSION_')) {
    exit;
}


class mark_whatsapp extends Module
{

    public function __construct()
    {
        $this->name = 'mark_whatsapp';
        $this->author = 'Arón Yáñez';
        $this->version = '1.0.0';
        $this->tab = 'front_office_features';
        $this->ps_versions_compliancy = [
            'min' => '1.6',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Whatsapp Contact');
        $this->description = $this->l('Add Whatsapp Contact');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

    }

    public function install()
    {

        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!parent::install() ||
            !$this->registerHook('displayHeader') ||
            !$this->registerHook('displayHome') ||
            !Configuration::updateValue('Whats_Number', '4434395115') ||
            !Configuration::updateValue('Whats_Message', $this->l('I want information') )
        ) {
            return false;
    }

    return true;
}


public function uninstall()
{
    return parent::uninstall()
    && Configuration::deleteByName('Whats_Number')
    && Configuration::deleteByName('Whats_Message');
}


public function hookDisplayHeader($params)
{

    $this->context->controller->registerStylesheet('modules-mark_whatsapp-font', 'https://fonts.googleapis.com/css?family=Anton', ['server' => 'remote', 'position' => 'head','media' => 'all', 'priority' => 161]);

    $this->context->controller->registerStylesheet('modules-mark_whatsapp-icon', 'https://use.fontawesome.com/releases/v5.0.13/css/all.css', ['server' => 'remote', 'position' => 'head','media' => 'all', 'priority' => 162]);

    $this->context->controller->registerStylesheet('modules-whatsapp-style', 'modules/'.$this->name.'/views/css/style.css', 
        ['media' => 'all', 'priority' => 163]);
}

public function hookDisplayHome()
{

    $this ->context->smarty-> assign([
        'Whats_Number' => Configuration::get('Whats_Number'),
        'Whats_Message' => Configuration::get('Whats_Message')
    ]);
        //

    return $this->display(__FILE__, 'views/templates/hook/Whatsapphook.tpl');
}

public function getContent()
{
   $output = null;


   if (Tools::isSubmit('submit'.$this->name))
   {
      $Whats_Number= strval(Tools::getValue('Whats_Number'));
      $Whats_Message= strval(Tools::getValue('Whats_Message'));

      if ( (!$Whats_Number || empty($Whats_Number) || !Validate::isPhoneNumber($Whats_Number))
       &&   (!$Whats_Message || empty($Whats_Message)  || !Validate::isString($Whats_Message)) )
        $output .= $this->displayError($this->l('Invalid Configuration value'));

    else
    {
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


    // Init Fields form array
    $fieldsForm[0]['form'] = [
        'legend' => [
            'title' => $this->l('Settings'),
        ],
        'input' =>  [
            [
                'type' => 'text',
                'label' => $this->l('Phone Number'),
                'desc' => $this->l('Your phone number'),
                'name' => 'Whats_Number',
                'size' => 10,
                'required' => true
            ],
            [
                'type' => 'text',
                'label' => $this->l('Message'),
                'desc' => $this->l('Your initial Message'),
                'name' => 'Whats_Message',
                'size' => 100,
                'required' => true
            ]
        ],

        'submit' => [
            'title' => $this->l('Save'),
            'class' => 'btn btn-default pull-right'
        ]
    ];

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
$helper->toolbar_btn = [
    'save' => [
        'desc' => $this->l('Save'),
        'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
        '&token='.Tools::getAdminTokenLite('AdminModules'),
    ],
    'back' => [
        'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
        'desc' => $this->l('Back to list')
    ]
];

    // Load current value
$helper->fields_value['Whats_Number'] = Configuration::get('Whats_Number');
$helper->fields_value['Whats_Message'] = Configuration::get('Whats_Message');


return $helper->generateForm($fieldsForm);
}


}
