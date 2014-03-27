<?php
class Taxjar_Rateupdater_Model_Observer {

  public function execute($observer) {
    $regionId   = Mage::getStoreConfig('shipping/origin/region_id');
    $regionCode = Mage::getModel('directory/region')->load($regionId)->getCode(); 
    $configJson = $this->_getResource('configuration', $regionCode);
    $this->_setTaxBasis($configJson);
    $this->_setShippingTaxability($configJson);
    $this->_purgeExisting('tax/calculation_rule');
    $this->_purgeExisting('tax/calculation_rate');
    $rateJson = $this->_getResource('rates', $regionCode);
    Mage::log($rateJson);
  }

  // private methods

  private function _createRate($rateJson) {
    $rateModel = Mage::getModel('tax/calculation_rate');
    $rateModel->setTaxCountryId('US');
    $rateModel->setTaxRegionId(12);
    $rateModel->setTaxPostcode('94597');
    $rateModel->setCode('US-CA-walnut-creek-Rate 1');
    $rateModel->setRate('8.2500');
    $rateModel->save();
  }

  private function _purgeExisting($path) {
    $existingRecords = Mage::getModel($path)->getCollection();
    foreach($existingRecords as $record) {
      $record->delete();
    }        
  }

  private function _setShippingTaxability($configJson) {
    $taxClass = 0;
    if($configJson['shipping_taxable']) {
      $taxClass = 4;
    }
    $this->_setConfig('tax/classes/shipping_tax_class', $taxClass);
  }

  private function _setTaxBasis($configJson) {
    $basis = 'shipping';
    if($configJson['origin_based']) {
      $basis = 'origin';
    }
    $this->_setConfig('tax/calculation/based_on', $basis);
  }

  private function _setConfig($path, $value){
    $config = new Mage_Core_Model_Config();
    $config->saveConfig($path, $value, 'default', 0);
  }

  private function _getClient($url) {
    $apiKey = Mage::getStoreConfig('rateupdater_options/states/rateupdater_apikey');
    $client = new Varien_Http_Client($url);
    $client->setMethod(Varien_Http_Client::GET);
    $client->setHeaders('Authorization', 'Token token="' . $apiKey .  '"');
    return $client;
  }

  private function _getResource($resourceName, $regionCode) {
    $url        = 'http://localhost:4000/magento/get_' . $resourceName . '/' . $regionCode;
    $response   = $this->_getClient($url)->request();
    if ($response->isSuccessful()) {
      $json = $response->getBody();      
      return json_decode($json, true);
    } else {
      throw new Exception('Could not connect to TaxJar.');
    }
  }

}
?>