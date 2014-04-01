<?php
class Taxjar_Salestaxwizard_Model_Observer {
  
  public function execute($observer) {
    
    $this->newRates = array();

    $client         = Mage::getModel('salestaxwizard/client');
    $configuration  = Mage::getModel('salestaxwizard/configuration');
    $rule           = Mage::getModel('salestaxwizard/rule');
    $regionId       = Mage::getStoreConfig('shipping/origin/region_id');
    $regionCode     = Mage::getModel('directory/region')->load($regionId)->getCode();
    $storeZip       = Mage::getStoreConfig('shipping/origin/postcode');

    $apiHost = 'http://76.21.49.40:4000/';
      
    $configJson = $client->getResource(
      $apiHost . '/magento/get_configuration/' . $regionCode
    );

    if(!$configJson['allow_update']) {
      return;
    }

    $ratesJson = $client->getResource(
      $apiHost . '/magento/get_rates/' . $regionCode . '/' . $storeZip
    );

    $configuration->setTaxBasis($configJson);

    $configuration->setShippingTaxability($configJson);

    $configuration->setDisplaySettings();

    $this->_purgeExisting();

    $this->_createRates($ratesJson);

    $rule->create('Retail Customer-Taxable Goods-Rate 1', 2, 1, $this->newRates);

    if($configJson['freight_taxable']) {

      $rule->create('Retail Customer-Shipping-Rate 1', 4, 2, $this->newRates);

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
    $rate = Mage::getModel('salestaxwizard/rate');
    foreach($ratesJson as $rateJson) {
      $this->newRates[] = $rate->create($rateJson);
    }    
  }



}
?>