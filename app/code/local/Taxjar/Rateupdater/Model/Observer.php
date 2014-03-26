<?php
class Taxjar_Rateupdater_Model_Observer {

  public function execute($observer) {
    $configJson = $this->_getConfiguration();
    $this->_setTaxBasis($configJson);
    $this->_setShippingTaxability($configJson);
  }

  // private methods
  
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

  private function _getConfiguration() {
    $regionId   = Mage::getStoreConfig('shipping/origin/region_id');
    $regionCode = Mage::getModel('directory/region')->load($regionId)->getCode();
    $url        = 'http://localhost:4000/magento/get_configuration/' . $regionCode;
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