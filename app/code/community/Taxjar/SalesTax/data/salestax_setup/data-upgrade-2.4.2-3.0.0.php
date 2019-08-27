<?php

try {
    // Create timestamp for switch date
    Mage::getConfig()->saveConfig('tax/taxjar/sync_switch_date', time());

    // Unlink legacy authentication
    //TODO - confirm api endpoint and make request

    // Run backfill for the previous day
    $fromDate = new DateTime();
    $fromDate->add(DateInterval::createFromDateString('yesterday'));
    $toDate = new DateTime();
    $data = array('from_date' => $fromDate->format('m/d/Y'), 'to_date' => $toDate->format('m/d/Y'));

    Mage::dispatchEvent('taxjar_salestax_backfill_transactions', $data);
} catch (Exception $e) {
    Mage::logException($e);
}
