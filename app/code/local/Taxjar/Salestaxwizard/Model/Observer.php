<?php
class Taxjar_Salestaxwizard_Model_Observer {
  
  public function execute($observer) {    
    $this->newRates = array();
    $this->client = Mage::getModel('salestaxwizard/client');
    $this->configuration = Mage::getModel('salestaxwizard/configuration');
    $this->shippingrule = Mage::getModel('salestaxwizard/shippingrule');
    $this->rate = Mage::getModel('salestaxwizard/rate');
    $regionId   = Mage::getStoreConfig('shipping/origin/region_id');
    $regionCode = Mage::getModel('directory/region')->load($regionId)->getCode(); 
    $configJson = $this->client->getResource('configuration', $regionCode);
    $this->configuration->setTaxBasis($configJson);
    $this->configuration->setShippingTaxability($configJson);
    $this->_purgeExisting('tax/calculation');
    $this->_purgeExisting('tax/calculation_rate');
    $this->_createRates($regionCode);
    $this->shippingrule->createIfTaxable($configJson, $this->newRates);
  }

  // private methods



  private function _createRates($regionCode) {
    $ratesJson = $this->client->getResource('rates', $regionCode);
    foreach($ratesJson as $rateJson) {
      $this->newRates[] = $this->rate->create($rateJson);
    }    
  }


  private function _purgeExisting($path) {
    $existingRecords = Mage::getModel($path)->getCollection();    
    foreach($existingRecords as $record) {
      $record->delete();
    }        
  }





}
?>