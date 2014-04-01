<?php
class Taxjar_Salestaxwizard_Model_Client {

    public function getResource($url) {
      $response   = $this->_getClient($url)->request();
      if ($response->isSuccessful()) {
        $json = $response->getBody();      
        return json_decode($json, true);
      } else {
        throw new Exception('Could not connect to TaxJar.');
      }
    }  

    private function _getClient($url) {
      $apiKey = Mage::getStoreConfig('salestaxwizard/config/salestaxwizard_apikey');
      $client = new Varien_Http_Client($url);
      $client->setMethod(Varien_Http_Client::GET);
      $client->setHeaders('Authorization', 'Token token="' . $apiKey .  '"');
      return $client;
    }

  }

?>