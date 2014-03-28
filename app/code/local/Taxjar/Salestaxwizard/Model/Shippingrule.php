<?php
class Taxjar_Salestaxwizard_Model_Shippingrule {
  public function createIfTaxable($configJson, $newRates) {
    $ruleModel = Mage::getSingleton('tax/calculation_rule');
    $ruleModel->load('Retail Customer-Shipping-Rate 1', 'code')->delete();
    if($configJson['shipping_taxable']) {   
      $attributes = array(
        'code' => 'Retail Customer-Shipping-Rate 1',        
        'tax_customer_class' => array(3), 
        'tax_product_class' => array(4), 
        'tax_rate' => $newRates,
        'priority' => 1,
        'position' => 1
      );      
      $ruleModel->setData($attributes);
      $ruleModel->setCalculateSubtotal(0);
      $ruleModel->save();
      $ruleModel->saveCalculationData();
    }
  }  
}
?>