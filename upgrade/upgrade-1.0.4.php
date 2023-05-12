<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_0_4($module)
{
    return (bool)Db::getInstance()->execute('
        ALTER TABLE `' . _DB_PREFIX_ . 'google_place`
        ADD COLUMN `phone` VARCHAR(255) DEFAULT NULL;
        
        ALTER TABLE `' . _DB_PREFIX_ . 'google_place`
        ADD COLUMN `price_level` INT DEFAULT NULL;
        
        ALTER TABLE  `' . _DB_PREFIX_ . 'google_review`
        ADD COLUMN `author_url` VARCHAR(255) DEFAULT NULL;
    ');
}
