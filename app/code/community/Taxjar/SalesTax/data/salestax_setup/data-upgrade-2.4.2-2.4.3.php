<?php
/**
 * Taxjar_SalesTax
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Taxjar
 * @package    Taxjar_SalesTax
 * @copyright  Copyright (c) 2017 TaxJar. TaxJar is a trademark of TPS Unlimited, Inc. (http://www.taxjar.com)
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

$apiKey = preg_replace('/\s+/', '', Mage::getStoreConfig('tax/taxjar/apikey'));

if($apiKey) {
    $client = Mage::getModel('taxjar/client');

    try {
        $configJson = $client->getResource($apiKey, 'config');

        if (is_array($configJson) && isset($configJson['configuration']) && isset($configJson['configuration']['states'])) {
            $states = explode(',', $configJson['configuration']['states']);
            Mage::getConfig()->saveConfig('tax/taxjar/states', json_encode($states));
            Mage::app()->getCacheInstance()->flush();
            $a = Mage::getStoreConfig('tax/taxjar/states');
        }
    } catch (Exception $e) {
        // noop
    }
}

$installer->endSetup();
