<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\ORM\EntityManager;
use PrestaShop\PrestaShop\Core\Exception\ContainerNotFoundException;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use Pixel\Module\GoogleMyBusiness\Entity\GooglePlace;
use Pixel\Module\GoogleMyBusiness\Entity\GoogleReview;

class Pixel_googlemybusiness extends Module implements WidgetInterface
{
    protected $templateFile;

    /**
     * Module's constructor.
     */
    public function __construct()
    {
        $this->name = 'pixel_googlemybusiness';
        $this->version = '1.0.3';
        $this->author = 'Pixel Open';
        $this->tab = 'front_office_features';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('Google My Business', [], 'Modules.Pixelgooglemybusiness.Admin');
        $this->description = $this->trans('Retrieve and display the Google My Business place data.', [], 'Modules.Pixelgooglemybusiness.Admin');
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
        return parent::install() &&
            $this->createTables() &&
            $this->registerHook('actionFrontControllerSetMedia');
    }

    /**
     * Add CSS
     *
     * @return void
     */
    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/gmb.css');
    }

    /**
     * @return bool
     */
    public function uninstall(): bool
    {
        return parent::uninstall() && $this->deleteTables() && $this->deleteConfigurations();
    }

    /**
     * @param string $hookName
     * @param array $configuration
     *
     * @return string
     * @throws ContainerNotFoundException
     */
    public function renderWidget($hookName, array $configuration): string
    {
        $keys = [$this->name, md5(serialize($configuration))];
        $cacheId = join('_', $keys);

        $template = $configuration['template'] ?? $this->templateFile;

        if (!$this->isCached($template, $cacheId)) {
            $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));
        }

        return $this->fetch($template, $cacheId);
    }

    /**
     * @param string $hookName
     * @param mixed[] $configuration
     *
     * @return Object[]
     * @throws ContainerNotFoundException
     */
    public function getWidgetVariables($hookName, array $configuration): array
    {
        $placeIds = array_filter(
            explode(',', $configuration['place_ids'] ?? '')
        );
        $display = array_filter(
            explode(',', $configuration['display'] ?? 'name,rating,opening-hours,reviews')
        );
        $reviewNumber = $configuration['review_number'] ?? 5;
        $reviewMinRating = $configuration['review_min_rating'] ?? 0;

        return [
            'places'  => $this->getPlaces(
                $placeIds,
                in_array('reviews', $display),
                (int)$reviewNumber,
                (int)$reviewMinRating
            ),
            'display' => $display,
        ];
    }

    /**
     * Retrieve places
     *
     * @param string[] $placesIds
     *
     * @return Object[]
     * @throws ContainerNotFoundException
     */
    protected function getPlaces(
        array $placesIds = [],
        bool $loadReviews = true,
        int $reviewNumber = 5,
        int $reviewMinRating = 0
    ): array {
        /** @var EntityManager $entityManager */
        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');

        $placeRepository  = $entityManager->getRepository(GooglePlace::class);
        $reviewRepository = $entityManager->getRepository(GoogleReview::class);

        $criteria = [
            'language' => [$this->context->language->iso_code, null],
        ];
        if (!empty($placesIds)) {
            $criteria['placeId']  = $placesIds;
        }

        $places = $placeRepository->findBy($criteria);

        if ($loadReviews) {
            /** @var GooglePlace $place */
            foreach ($places as $place) {
                $language = new CompositeExpression(
                    CompositeExpression::TYPE_OR,
                    [
                        Criteria::expr()->eq('language', $this->context->language->iso_code),
                        Criteria::expr()->eq('language', null)
                    ]
                );
                $filters = new CompositeExpression(
                    CompositeExpression::TYPE_AND,
                    [
                        $language,
                        Criteria::expr()->eq('placeId', $place->getPlaceId()),
                        Criteria::expr()->eq('enabled', 1),
                        Criteria::expr()->gte('rating', $reviewMinRating),
                    ]
                );

                $criteria = Criteria::create()
                    ->where($filters)
                    ->orderBy(['time' => Criteria::DESC])
                    ->setMaxResults($reviewNumber);

                $reviews = $reviewRepository->matching($criteria)->getValues();

                $place->setReviews($reviews);
            }
        }

        return $places;
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
                'label'    => $this->trans('Google API Key', [], 'Modules.Pixelgooglemybusiness.Admin'),
                'name'     => 'GOOGLE_MY_BUSINESS_API_KEY',
                'size'     => 20,
                'required' => true,
            ],
            'GOOGLE_MY_BUSINESS_PLACE_IDS' => [
                'type'     => 'textarea',
                'label'    => $this->trans('Google Place IDs', [], 'Modules.Pixelgooglemybusiness.Admin'),
                'name'     => 'GOOGLE_MY_BUSINESS_PLACE_IDS',
                'size'     => 20,
                'required' => true,
                'desc'     => $this->trans('One place id per line', [], 'Modules.Pixelgooglemybusiness.Admin'),
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
                    return $this->displayError($this->trans('%field% is empty', ['%field%' => $field['label']], 'Modules.Pixelgooglemybusiness.Admin')) . $this->displayForm();
                }
                Configuration::updateValue($field['name'], $value);
            }

            $output = $this->displayConfirmation($this->trans('Settings updated', [], 'Modules.Pixelgooglemybusiness.Admin'));
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
                    'title' => $this->trans('Settings', [], 'Modules.Pixelgooglemybusiness.Admin'),
                ],
                'input' => $this->getConfigFields(),
                'submit' => [
                    'title' => $this->trans('Save', [], 'Modules.Pixelgooglemybusiness.Admin'),
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
                `id` INT(11) AUTO_INCREMENT NOT NULL,
                `place_id` VARCHAR(255) NOT NULL,
                `language` VARCHAR(2) NULL,
                `name` VARCHAR(255) NOT NULL,
                `phone` VARCHAR(255) NOT NULL,
                `opening_hours_periods` TEXT DEFAULT NULL,
                `opening_hours_weekday_text` TEXT DEFAULT NULL,
                `rating` NUMERIC(4, 2) DEFAULT NULL,
                `user_ratings_total` INT DEFAULT NULL,
                `price_level` INT DEFAULT NULL,
                PRIMARY KEY(`id`),
                UNIQUE KEY(`place_id`, `language`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
            
            CREATE TABLE `' . _DB_PREFIX_ . 'google_review` (
                `id` INT(11) AUTO_INCREMENT NOT NULL,
                `place_id` VARCHAR(255) NOT NULL,
                `author_name` VARCHAR(255) DEFAULT NULL,
                `author_url` VARCHAR(255) DEFAULT NULL,
                `language` VARCHAR(2) NULL,
                `original_language` VARCHAR(2) DEFAULT NULL,
                `profile_photo_url` VARCHAR(255) DEFAULT NULL,
                `rating` SMALLINT DEFAULT NULL,
                `relative_time_description` VARCHAR(255) DEFAULT NULL,
                `comment` LONGTEXT DEFAULT NULL,
                `time` INT DEFAULT NULL,
                `translated` TINYINT(1) DEFAULT NULL,
                `enabled` TINYINT(1) DEFAULT NULL,
                KEY INDEX_PLACE_ID_TIME (`place_id`, `time`),
                PRIMARY KEY(`id`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
        ');
    }

    /**
     * Delete tables
     *
     * @return bool
     */
    protected function deleteTables(): bool
    {
        return (bool)Db::getInstance()->execute('
            DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'google_place`;
            DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'google_review`;
        ');
    }

    /**
     * Delete configurations
     *
     * @return bool
     */
    protected function deleteConfigurations(): bool
    {
        foreach ($this->getConfigFields() as $key => $options) {
            Configuration::deleteByName($key);
        }

        return true;
    }

    /**
     * Use the new translation system
     *
     * @return bool
     */
    public function isUsingNewTranslationSystem(): bool
    {
        return true;
    }
}
