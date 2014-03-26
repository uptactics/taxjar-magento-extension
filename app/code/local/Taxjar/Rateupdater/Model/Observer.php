<?php
class Taxjar_Rateupdater_Model_Observer {

  public function execute($observer) {
    Mage::log($this->getConfiguration()->getBody());
  }

  private function getApiKey() {
    return Mage::getStoreConfig('rateupdater_options/states/rateupdater_apikey');
  }

  private function getRegionCode() {
    $regionId = Mage::getStoreConfig('shipping/origin/region_id');
    return Mage::getModel('directory/region')->load($regionId)->getCode();
  }

  private function getConfigUrl() {
    return 'http://localhost:4000/magento/get_configuration/' . $this->getRegionCode();
  }

  private function getAuthHeader() {
    return 'Token token="' . $this->getApiKey() .  '"';
  }

  private function getClient($url) {
    $client = new Varien_Http_Client($url);
    $client->setMethod(Varien_Http_Client::GET);
    $client->setHeaders('Authorization', $this->getAuthHeader());
    return $client;
  }

  private function getConfiguration() {
    $response = $this->getClient($this->getConfigUrl())->request();
    if ($response->isSuccessful()) {
      return $response;
    } else {
      throw new Exception('Could not connect to TaxJar.');
    }
  }

}
?>