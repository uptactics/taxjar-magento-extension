<?php
class Taxjar_SalesTax_Model_Observer //extends Mage_Core_Model_Abstract
{

  public function execute($observer) {
  	$session = Mage::getSingleton('adminhtml/session');
  	$storeId = Mage::getModel('core/store')->load($observer->getEvent()->getStore())->getStoreId();
    $apiKey = Mage::getStoreConfig('taxjar/config/apikey', $storeId);
    $apiKey = preg_replace('/\s+/', '', $apiKey);
    $filename = Mage::getBaseDir('tmp') ."/tmp_rates.dat";
    if ($apiKey){
      $client         = Mage::getModel('taxjar/client');
      $configuration  = Mage::getModel('taxjar/configuration');
      $regionId       = Mage::getStoreConfig('shipping/origin/region_id',$storeId);
      $regionCode     = Mage::getModel('directory/region')->load($regionId)->getCode();
      $storeZip       = Mage::getStoreConfig('shipping/origin/postcode',$storeId);
      $apiHost = 'http://tax-rate-service.dev';
      $validZip = preg_match("/(\d{5}-\d{4})|(\d{5})/", $storeZip);
      if(isset($regionCode)){
          $configJson = $client->getResource(
          $apiKey,
          $apiHost . '/magento/get_configuration/' . $regionCode
        );
        Mage::getModel('core/config')->saveConfig('taxjar/config/freight_taxable', $configJson['freight_taxable']);
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
      Mage::getModel('core/config')->saveConfig('taxjar/config/states', serialize(explode(',', $configJson['states'])));
      $configuration->setTaxBasis($configJson);
      $configuration->setShippingTaxability($configJson);
      $configuration->setDisplaySettings();
      $configuration->setApiSettings($apiKey);
      $this->_purgeExisting();
      if ( false !== file_put_contents($filename, serialize($ratesJson)) ) {
        Mage::dispatchEvent('taxjar_salestax_import_rates'); 
      }
      else {
        throw new Exception("Could not write to your Magento temp directory (".Mage::getBaseDir('tmp').").");
      }
    } else {
      $this->_purgeExisting();
      $this->_setLastUpdateDate(NULL);
    }
    Mage::app()->getCacheInstance()->flush();
  }

  public function importRates() {
    @set_time_limit(0);
    @ignore_user_abort(true);
    $this->newRates = array();
    $this->freightTaxableRates = array();
    $rate = Mage::getModel('taxjar/rate');
    $filename = Mage::getBaseDir('tmp') ."/tmp_rates.dat";
    $rule = Mage::getModel('taxjar/rule');
    $shippingTaxable = Mage::getStoreConfig('taxjar/config/freight_taxable');
    $ratesJson = unserialize(file_get_contents($filename));
    foreach($ratesJson as $rateJson) {
      $rateIdWithShippingId = $rate->create($rateJson);
      if ( $rateIdWithShippingId[1] ) {
        $this->freightTaxableRates[] = $rateIdWithShippingId[1];
      }
      $this->newRates[] = $rateIdWithShippingId[0];
    }
    $this->_setLastUpdateDate(date('m-d-Y'));
    $rule->create('Retail Customer-Taxable Goods-Rate 1', 2, 1, $this->newRates);
    if ( $shippingTaxable ){
      $rule->create('Retail Customer-Shipping-Rate 1', 4, 2, $this->freightTaxableRates); 
    }
    file_put_contents($filename, '');
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

  private function _setLastUpdateDate($date) {
    Mage::getModel('core/config')->saveConfig('taxjar/config/last_update', $date);
  }

}
?>