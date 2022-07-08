<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class Pixel_googlemybusiness extends Module implements WidgetInterface
{
    protected $templateFile;

    /**
     * Module's constructor.
     */
    public function __construct()
    {
        $this->name = 'pixel_googlemybusiness';
        $this->version = '1.0.0';
        $this->author = 'Pixel';
        $this->tab = 'front_office_features';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Google My Business');
        $this->description = $this->l('Retrieve and display the Google My Business place data.');
        $this->ps_versions_compliancy = [
            'min' => '1.7.6.0',
            'max' => _PS_VERSION_,
        ];

        $this->templateFile = 'module:pixel_googlemybusiness/pixel_googlemybusiness.tpl';
    }

    /**
     * @return bool
     */
    public function install(): bool
    {
        return parent::install() && $this->createTables() && $this->registerHook('displayHome');
    }

    /**
     * @return bool
     */
    public function uninstall(): bool
    {
        return parent::uninstall() && $this->deleteTables();
    }

    /**
     * @param string $hookName
     * @param array $configuration
     *
     * @return string
     */
    public function renderWidget($hookName, array $configuration): string
    {
        if (!$this->isCached($this->templateFile, $this->getCacheId($this->name))) {
            $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));
        }

        return $this->fetch($this->templateFile, $this->getCacheId($this->name));
    }

    /**
     * @param string $hookName
     * @param array $configuration
     *
     * @return mixed[]
     */
    public function getWidgetVariables($hookName, array $configuration): array
    {
        return [];
    }

    /**
     * Retrieve config fields
     *
     * @return array[]
     */
    protected function getConfigFields(): array
    {
        return [
            'GOOGLE_MY_BUSINESS_API_KEY' => [
                'type'     => 'text',
                'label'    => $this->l('Google API Key'),
                'name'     => 'GOOGLE_MY_BUSINESS_API_KEY',
                'size'     => 20,
                'required' => true
            ],
            'GOOGLE_MY_BUSINESS_PLACE_ID' => [
                'type'     => 'text',
                'label'    => $this->l('Google Place ID'),
                'name'     => 'GOOGLE_MY_BUSINESS_PLACE_ID',
                'size'     => 20,
                'required' => true
            ]
        ];
    }

    /**
     * This method handles the module's configuration page
     *
     * @return string
     */
    public function getContent(): string
    {
        $output = '';

        if (Tools::isSubmit('submit' . $this->name)) {
            foreach ($this->getConfigFields() as $field) {
                $value = (string) Tools::getValue($field['name']);
                if ($field['required'] && empty($value)) {
                    return $this->displayError($this->l($field['label'] . ' is empty')) . $this->displayForm();
                }
                Configuration::updateValue($field['name'], $value);
            }

            $output = $this->displayConfirmation($this->l('Settings updated'));
        }

        return $output . $this->displayForm();
    }

    /**
     * Builds the configuration form
     *
     * @return string
     */
    public function displayForm(): string
    {
        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                ],
                'input' => $this->getConfigFields(),
                'submit' => [
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];

        $helper = new HelperForm();

        $helper->table = $this->table;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&' . http_build_query(['configure' => $this->name]);
        $helper->submit_action = 'submit' . $this->name;

        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');

        foreach ($this->getConfigFields() as $field) {
            $helper->fields_value[$field['name']] = Tools::getValue(
                $field['name'],
                Configuration::get($field['name'])
            );
        }

        return $helper->generateForm([$form]);
    }

    /**
     * Create tables
     */
    protected function createTables(): bool
    {
        return (bool)Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'google_place` (
                id INT AUTO_INCREMENT NOT NULL,
                place_id VARCHAR(255) NOT NULL,
                opening_hours_periods TEXT DEFAULT NULL,
                opening_hours_weekday_text TEXT DEFAULT NULL,
                rating NUMERIC(4, 2) DEFAULT NULL,
                user_ratings_total INT DEFAULT NULL,
                PRIMARY KEY(id),
                UNIQUE KEY(place_id)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
        ');
    }

    /**
     * Delete tables
     */
    protected function deleteTables():  bool
    {
        return (bool)Db::getInstance()->execute('
            DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'google_place`;
        ');
    }
}
