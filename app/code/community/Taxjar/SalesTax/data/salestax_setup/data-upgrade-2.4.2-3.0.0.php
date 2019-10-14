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

try {
    // Create timestamp for switch date
    Mage::getConfig()->saveConfig('tax/taxjar/sync_switch_date', time());

    // Run backfill for the previous day
    $fromDate = new DateTime();
    $fromDate->add(DateInterval::createFromDateString('yesterday'));
    $toDate = new DateTime();
    $data = array('from_date' => $fromDate->format('m/d/Y'), 'to_date' => $toDate->format('m/d/Y'));

    Mage::dispatchEvent('taxjar_salestax_backfill_transactions', $data);

    // Unlink legacy authentication
    $apiKey = preg_replace('/\s+/', '', Mage::getStoreConfig('tax/taxjar/apikey'));

    if ($apiKey) {
        $urls = array(
            Mage::getBaseUrl() . 'index.php/api/v2_soap/?wsdl=1',
            Mage::getBaseUrl() . 'api/v2_soap/?wsdl=1',
            Mage::getBaseUrl() . 'index.php/api/soap/?wsdl',
            Mage::getBaseUrl() . 'api/soap/?wsdl'
        );

        $deregistered = false;

        foreach ($urls as $url) {
            $client = Mage::getModel('taxjar/client');

            try {
                $response = $client->deleteResource('deregister', '', array('store_url' => $url));
                $deregistered = true;
                break;
            } catch (Exception $e) {
                // noop
            }
        }

        if (!$deregistered) {
            $inbox = Mage::getModel('adminnotification/inbox');
            $inbox->addNotice(
                'Please unlink your Magento store in TaxJar',
                'Your Magento orders will now be synced to TaxJar in real-time through our API. This means you can 
                unlink your Magento store in the TaxJar app. Please log in to app.taxjar.com and manually unlink your 
                Magento store. For more information, check out https://support.taxjar.com/article/834-how-do-i-remove-a-linked-account. 
                If you have additional questions, please email support@taxjar.com.',
                'https://app.taxjar.com/account#linked-accounts'
            );
        }
    }

    // Remove API user/roles
    $apiUser = Mage::getModel('api/user')->load('taxjar', 'username');

    if ($apiUser->getId()) {
        $apiRoleChild = Mage::getModel('api/role')->load($apiUser->getUserId(), 'user_id');
        $apiRoleParent = Mage::getModel('api/role')->load($apiRoleChild->getParentId());

        if ($apiRoleParent->getId()) {
            $apiRoleParent->delete();
        }

        $apiUser->delete();
    }
} catch (Exception $e) {
    Mage::logException($e);
}
