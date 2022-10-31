<?php

class ThemeconfiguratorAjaxModuleFrontController extends ModuleFrontController
{
    /**
     * @var bool
     */
    public $ssl = true;

    /**
     * @see FrontController::initContent()
     *
     * @return void
     */
    public function initContent()
    {

        $query =  'SELECT * FROM ' . _DB_PREFIX_ . 'themeconfigurator WHERE 1=1';
        $db = \Db::getInstance();
        $results = $db->executeS($query);

        $themeconfigurator = [];
        foreach ($results as $result) {
            $result['image'] = _PS_BASE_URL_.__PS_BASE_URI__ .'modules/themeconfigurator/images/' . $result['image'];
            $themeconfigurator[$result['name']] = $result;
        }

        parent::initContent();
  
        exit(json_encode($themeconfigurator));
    }
}