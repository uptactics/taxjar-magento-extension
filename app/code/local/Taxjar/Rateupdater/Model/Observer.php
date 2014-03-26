<?php
class Taxjar_Rateupdater_Model_Observer {

    public function logobs($observer) {
        //$observer contains data passed from when the event was triggered.
        //You can use this data to manipulate the order data before it's saved.
        //Uncomment the line below to log what is contained here:
        //Mage::log($observer);
        $regionId = Mage::getStoreConfig('shipping/origin/region_id');        
        $regionCode = Mage::getModel('directory/region')->load($regionId)->getCode();
        Mage::log($regionCode);
        $client = new Varien_Http_Client('http://localhost:4000/magento/get_configuration/TX');
        $client->setMethod(Varien_Http_Client::GET);
        $client->setHeaders('Authorization', 'Token token="49452ec7f67ea6fa88fb05492f02443c"');
        //more parameters
        try{
            $response = $client->request();
            if ($response->isSuccessful()) {
              Mage::log($response);
            }
        } catch (Exception $e) {
          Mage::log($e);
        }        
    }

}
?>