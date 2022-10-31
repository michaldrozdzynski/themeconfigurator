<?php
/**
* 2007-2022 PrestaShop
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
*  @copyright 2007-2022 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Themeconfigurator extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'themeconfigurator';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Michał Drożdżyński';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Theme Configurator');
        $this->description = $this->l('Theme Configurations');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);    
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        include(dirname(__FILE__).'/sql/install.php');

        return parent::install() && $this->registerHook('actionFrontControllerSetVariables');
    }

    public function uninstall()
    {
        include(dirname(__FILE__).'/sql/uninstall.php');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitThemeconfiguratorModule')) == true) {
            $this->postProcess();
        }

        if (((bool)Tools::isSubmit('updateThemeconfiguratorModule')) == true) {
            $this->postProcess();
        }


        if (Tools::isSubmit('addElement')) {
            return $this->renderForm();
        }

        if (Tools::isSubmit('deletethemeconfigurator')) {
            $id = Tools::getValue('id_themeconfigurator');

            $db = \Db::getInstance();
            $db->delete('themeconfigurator', 'id_themeconfigurator = ' . $id);
        }

        if (Tools::isSubmit('updatethemeconfigurator')) {
            return $this->renderUpdateForm();
        }


        return $this->sliderList();
    }

    public function sliderList() {
        $query =  'SELECT * FROM ' . _DB_PREFIX_ . 'themeconfigurator WHERE 1=1';
        $db = \Db::getInstance();
        $result = $db->executeS($query);
        

        $fields_list = array(
           'id_themeconfigurator'=> array(
              'title' => "ID",
              'align' => 'center',
              'class' => 'fixed-width-xs',
              'search' => false,
            ),
           'image' => array(
              'title' => $this->l('Image'),
              'orderby' => false,
              'class' => 'fixed-width-xxl',
              'search' => false,
              'callback' => 'displayImage',
              'callback_object' => $this,
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'orderby' => false,
                'class' => 'fixed-width-xxl',
                'search' => false,
            ),
            'text1' => array(
                'title' => $this->l('Text 1'),
                'orderby' => false,
                'class' => 'fixed-width-xxl',
                'search' => false,
            ),
            'text2' => array(
                'title' => $this->l('Text 2'),
                'orderby' => false,
                'class' => 'fixed-width-xxl',
                'search' => false,
            ),
            'text3' => array(
                'title' => $this->l('Text 3'),
                'orderby' => false,
                'class' => 'fixed-width-xxl',
                'search' => false,
            ),
            'text4' => array(
                'title' => $this->l('Text 4'),
                'orderby' => false,
                'class' => 'fixed-width-xxl',
                'search' => false,
            ),
            'url' => array(
                'title' => $this->l('URL'),
                'orderby' => false,
                'class' => 'fixed-width-xxl',
                'search' => false,
            ),
        );
  
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = 'id_themeconfigurator';
        $helper->table = 'themeconfigurator';
        $helper->actions = ['edit', 'delete'];
        $helper->show_toolbar = false;
        $helper->_default_pagination = 10;
        $helper->listTotal = count($result);
        $helper->_pagination = array(5, 10, 50, 100);
        $helper->toolbar_btn['new'] = [
            'href' => $this->context->link->getAdminLink('AdminModules', true, [], ['configure' => $this->name, 'module_name' => $this->name, 'addElement' => '']),
            'desc' => $this->trans('Add New Element', [], 'Modules.Productcomments.Admin'),
        ];
        $helper->module = $this;
        $helper->title = $this->l('Configuration list');
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $page = ( $page = Tools::getValue( 'submitFilter' . $helper->table ) ) ? $page : 1;
        $pagination = ( $pagination = Tools::getValue( $helper->table . '_pagination' ) ) ? $pagination : 10;
        $content = $this->paginate_content( $result, $page, $pagination );

        return $helper->generateList($content, $fields_list);
    }

    public function displayImage($path)
    {
        return '<img width="50px" src="'._PS_BASE_URL_.__PS_BASE_URI__ .'modules/themeconfigurator/images/'.$path.'">';
    }

    private function generateRandomString($length = 10) {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }
    

    public function paginate_content( $content, $page = 1, $pagination = 10 ) {

        if( count($content) > $pagination ) {
             $content = array_slice( $content, $pagination * ($page - 1), $pagination );
        }
     
        return $content;
     
     }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderUpdateForm()
    {
        $id = Tools::getValue('id_themeconfigurator');

        $query =  'SELECT * FROM ' . _DB_PREFIX_ . 'themeconfigurator WHERE id_themeconfigurator=' . $id;
        $db = \Db::getInstance();

        $row = $db->getRow($query);

        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'updateThemeconfiguratorModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => [
                'text1' => $row['text1'],
                'text2' => $row['text2'],
                'text3' => $row['text3'],
                'text4' => $row['text4'],
                'name' => $row['name'],
                'url' => $row['url'],
                'id_themeconfigurator' => $row['id_themeconfigurator'],
            ], /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getUpdateConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getUpdateConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'required' => true,
                        'type' => 'file',
                        'name' => 'image',
                        'label' => $this->l('Image'),
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'id_themeconfigurator',
                    ),
                    array(
                        'col' => 4,
                        'required' => true,
                        'type' => 'text',
                        'name' => 'name',
                        'label' => $this->l('Name'),
                    ),
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'name' => 'text1',
                        'label' => $this->l('Text 1'),
                    ),
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'name' => 'text2',
                        'label' => $this->l('Text 2'),
                    ),
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'name' => 'text3',
                        'label' => $this->l('Text 3'),
                    ),
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'name' => 'text4',
                        'label' => $this->l('Text 4'),
                    ),
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'name' => 'url',
                        'label' => $this->l('URL'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitThemeconfiguratorModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => [
                'text1' => '',
                'text2' => '',
                'text3' => '',
                'text4' => '',
                'name' => '',
                'url' => '',
            ], /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'required' => true,
                        'type' => 'file',
                        'name' => 'image',
                        'label' => $this->l('Image'),
                    ),
                    array(
                        'col' => 4,
                        'required' => true,
                        'type' => 'text',
                        'name' => 'name',
                        'label' => $this->l('Name'),
                    ),
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'name' => 'text1',
                        'label' => $this->l('Text 1'),
                    ),
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'name' => 'text2',
                        'label' => $this->l('Text 2'),
                    ),
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'name' => 'text3',
                        'label' => $this->l('Text 3'),
                    ),
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'name' => 'text4',
                        'label' => $this->l('Text 4'),
                    ),
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'name' => 'url',
                        'label' => $this->l('URL'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $image = $_FILES['image'];
        $text1 = Tools::getValue('text1');
        $text2 = Tools::getValue('text2');
        $text3 = Tools::getValue('text3');
        $text4 = Tools::getValue('text4');
        $url = Tools::getValue('url');
        $name = Tools::getValue('name');
        $id = Tools::getValue('id_themeconfigurator');

        if (isset($_FILES['image']))    {
              $target_dir = _PS_MODULE_DIR_. '/themeconfigurator/images/';
              $target_file = $target_dir . basename($_FILES['image']["name"]);
              $file_name = basename($_FILES['image']["name"]);
              $uploadOk = 1;
              $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
              $filename = $this->generateRandomString() . '.' . $imageFileType;
              $file_path = $target_dir . $filename;
              // Check if image file is a actual image or fake image
              if(isset($_POST["submit"]))
              {
                  $check = getimagesize($_FILES['image']["tmp_name"]);
                  if($check !== false) {
                      $uploadOk = 1;
                  } else {
                      $uploadOk = 0;
                  }
              }

            // Allow certain file formats
            if($imageFileType != "jpg" && $imageFileType != "JPG" && $imageFileType != "png" && $imageFileType != "PNG" 
                && $imageFileType != "JPEG" && $imageFileType != "jpeg" && $imageFileType != "GIF"
            && $imageFileType != "gif" ) {
                $uploadOk = 0;
            }
              // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 1) {
                  if (move_uploaded_file($_FILES['image']["tmp_name"], $file_path)) {
                        $db = \Db::getInstance();

                        if ($id) {
                            $db->update('themeconfigurator', [
                                'image' => $filename,
                                'text1' => $text1,
                                'text2' => $text2,
                                'text3' => $text3,
                                'text4' => $text4,
                                'name' => $name,
                                'url' => $url
                            ], 'id_themeconfigurator=' . $id);
                        } else {
                            /** @var bool $result */
                            $db->insert('themeconfigurator', [
                                'image' => $filename,
                                'text1' => $text1,
                                'text2' => $text2,
                                'text3' => $text3,
                                'text4' => $text4,
                                'name' => $name,
                                'url' => $url
                            ]);
                        }
                  }
            }
        }

    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookActionFrontControllerSetVariables()
    {
        $query =  'SELECT * FROM ' . _DB_PREFIX_ . 'themeconfigurator WHERE 1=1';
        $db = \Db::getInstance();
        $results = $db->executeS($query);

        $themeconfigurator = [];
        foreach ($results as $result) {
            $result['image'] = _PS_BASE_URL_.__PS_BASE_URI__ .'modules/themeconfigurator/images/' . $result['image'];
            $themeconfigurator[$result['name']] = $result;
        }

        return $themeconfigurator;
    }
}
