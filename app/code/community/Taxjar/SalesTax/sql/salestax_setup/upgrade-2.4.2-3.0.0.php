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
 * @copyright  Copyright (c) 2019 TaxJar. TaxJar is a trademark of TPS Unlimited, Inc. (http://www.taxjar.com)
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();

try {
    Mage::getConfig()->saveConfig('tax/taxjar/provider', 'magento');

    if ($connection->tableColumnExists($installer->getTable('sales/order'), 'tj_salestax_sync_date') == false) {
        $connection
            ->addColumn($installer->getTable('sales/order'), 'tj_salestax_sync_date', array(
                'type' => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable' => true,
                'after' => null,
                'comment' => 'Order sync date for TaxJar'
            ));
    } else {
        $orders = Mage::getModel('sales/order')->getCollection();
        $orders->addFieldToFilter('tj_salestax_sync_date', array('notnull' => true));
        if (count($orders) > 0) {
            Mage::getConfig()->saveConfig('tax/taxjar/provider', 'api');
        }
    }

    if ($connection->tableColumnExists($installer->getTable('sales/creditmemo'), 'tj_salestax_sync_date') == false) {
        $connection
            ->addColumn($installer->getTable('sales/creditmemo'), 'tj_salestax_sync_date', array(
                'type' => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable' => true,
                'after' => null,
                'comment' => 'Refund sync date for TaxJar'
            ));
    } else {
        $creditMemos = Mage::getModel('sales/order_creditmemo')->getCollection();
        $creditMemos->addFieldToFilter('tj_salestax_sync_date', array('notnull' => true));
        if (count($creditMemos) > 0) {
            Mage::getConfig()->saveConfig('tax/taxjar/provider', 'api');
        }
    }
} catch (Exception $e) {
    Mage::logException($e);
}

$installer->endSetup();
