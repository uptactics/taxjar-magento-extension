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
//$installer = $this;
//$installer->startSetup();

/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

try {
    $url = 'https://www.taxjar.com/guides/integrations/magento/#product-sales-tax-exemptions';
    $note = 'TaxJar requires a product tax class assigned to a TaxJar category in order to exempt products from sales 
    tax. <a href="' . $url . '" target="_blank">Click here</a> to learn more.';

    $installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'tax_class_id', 'note', $note);
} catch (Exception $e) {
    Mage::logException($e);
}

$installer->endSetup();
