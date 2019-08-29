<?php

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
        $client = Mage::getModel('taxjar/client');
        $response = $client->getResource('categories'); //TODO: replace categories with deregister
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
