<?php
class Taxjar_SalesTax_Model_Observer extends Mage_Core_Model_Abstract
{
  
  public function execute($observer) {
  	$session = Mage::getSingleton('adminhtml/session');
  	$storeId = Mage::getModel('core/store')->load($observer->getEvent()->getStore())->getStoreId();
  	    
    $apiKey = Mage::getStoreConfig('taxjar/config/apikey',$storeId);
    $apiKey = preg_replace('/\s+/', '', $apiKey);
    if ($apiKey){
      $this->newRates = array();
      $client         = Mage::getModel('taxjar/client');
      $configuration  = Mage::getModel('taxjar/configuration');
      $rule           = Mage::getModel('taxjar/rule');
      $regionId       = Mage::getStoreConfig('shipping/origin/region_id',$storeId);
      $regionCode     = Mage::getModel('directory/region')->load($regionId)->getCode();
      $storeZip       = Mage::getStoreConfig('shipping/origin/postcode',$storeId);
      $apiHost = 'https://api.taxjar.com';
      $validZip = preg_match("/(\d{5}-\d{4})|(\d{5})/", $storeZip);
      if(isset($regionCode)){
          $configJson = $client->getResource(
          $apiKey,
          $apiHost . '/magento/get_configuration/' . $regionCode
        );
      } else {
        throw new Exception("Please check that you have set a Region/State in Shipping Settings.");
      }
      if(!$configJson['allow_update']) {      
        return;
      }
      if($validZip === 1 && isset($storeZip) && trim($storeZip) !== ''){
        $ratesJson = $client->getResource(
          $apiKey,
          $apiHost . '/magento/get_rates/' . $regionCode . '/' . $storeZip
        );
      } else{
        throw new Exception("Please check that your zip code is a valid US zip code in Shipping Settings.");
      }
      $configuration->setTaxBasis($configJson);
      $configuration->setShippingTaxability($configJson);
      $configuration->setDisplaySettings();
      $configuration->setApiSettings($apiKey);
      $this->_purgeExisting();
      $this->_createRates($ratesJson);
      $rule->create('Retail Customer-Taxable Goods-Rate 1', 2, 1, $this->newRates);
      if($configJson['freight_taxable']) {
        $rule->create('Retail Customer-Shipping-Rate 1', 4, 2, $this->newRates);
      }
    } else {
      $this->_purgeExisting();
      $this->_setLastUpdateDate(NULL);
    }
  }



  private function _purgeExisting() {
    $paths = array('tax/calculation', 'tax/calculation_rate', 'tax/calculation_rule');
    foreach($paths as $path){
      $existingRecords = Mage::getModel($path)->getCollection();    
      foreach($existingRecords as $record) {
        $record->delete();
      }
    }
  }

  private function _createRates($ratesJson) {
    $rate = Mage::getModel('taxjar/rate');
    foreach($ratesJson as $rateJson) {
      $this->newRates[] = $rate->create($rateJson);
    }
    $this->_setLastUpdateDate(date('m-d-Y'));
  }

  private function _setLastUpdateDate($date) {
    Mage::getModel('core/config')->saveConfig('taxjar/config/last_update', $date);
  }



}
?>