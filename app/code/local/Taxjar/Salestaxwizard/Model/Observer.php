<?php
class Taxjar_Salestaxwizard_Model_Observer {
  
  public function execute($observer) {    
    $this->newRates = array();
    $this->client = Mage::getModel('salestaxwizard/client');
    $this->configuration = Mage::getModel('salestaxwizard/configuration');
    $regionId   = Mage::getStoreConfig('shipping/origin/region_id');
    $regionCode = Mage::getModel('directory/region')->load($regionId)->getCode(); 
    $configJson = $this->client->getResource('configuration', $regionCode);
    $this->configuration->setTaxBasis($configJson);
    $this->configuration->setShippingTaxability($configJson);
    $this->_purgeExisting('tax/calculation');
    $this->_purgeExisting('tax/calculation_rate');
    $this->_createRates($regionCode);
    $this->_createShippingRuleIfTaxable($configJson);
  }

  // private methods

  private function _createShippingRuleIfTaxable($configJson) {
    if($configJson['shipping_taxable']) {   
      $attributes = array(
        'code' => 'Retail Customer-Shipping-Rate 1',        
        'tax_customer_class' => array(3), 
        'tax_product_class' => array(4), 
        'tax_rate' => $this->newRates,
        'priority' => 1,
        'position' => 1
      );
      $ruleModel = Mage::getSingleton('tax/calculation_rule');
      $ruleModel->setData($attributes);
      $ruleModel->setCalculateSubtotal(0);
      $ruleModel->save();
      $ruleModel->saveCalculationData();
    }
  }

  private function _createRates($regionCode) {
    $ratesJson = $this->client->getResource('rates', $regionCode);
    foreach($ratesJson as $rateJson) {
      $this->_createRate($rateJson);
    }    
  }

  private function _createRate($rateJson) {
    $rateModel = Mage::getModel('tax/calculation_rate');
    $rateModel->setTaxCountryId('US');
    $rateModel->setTaxRegionId(12);
    $rateModel->setTaxPostcode('94597');
    $rateModel->setCode('US-CA-walnut-creek-Rate 1');
    $rateModel->setRate('8.2500');
    $rateModel->save();
    $rateId = $rateModel->getId();
    $this->newRates[] = $rateId; 

    $taxCalculationData = array(
      'tax_calculation_rate_id'   => $rateId,
      'tax_calculation_rule_id'   => 1,
      'customer_tax_class_id'     => 3,
      'product_tax_class_id'      => 2
    );

    Mage::getSingleton('tax/calculation')->setData($taxCalculationData)->save();
  }

  private function _purgeExisting($path) {
    $existingRecords = Mage::getModel($path)->getCollection();    
    foreach($existingRecords as $record) {
      $record->delete();
    }        
  }





}
?>