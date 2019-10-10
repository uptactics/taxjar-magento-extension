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
            /** @var Mage_AdminNotification_Model_Inbox $inbox */
            $inbox = Mage::getModel('adminnotification/inbox');
            $inbox->addNotice(
                'Please unlink your TaxJar account ',
                'We were unable to unlink your TaxJar account.  Please login to taxjar.com and manually 
                unlink it.  You can email support@taxjar.com if you need assistance!',
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
