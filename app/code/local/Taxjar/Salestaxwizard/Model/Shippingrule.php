<?php
class Taxjar_Salestaxwizard_Model_Shippingrule {
  public function createIfTaxable($configJson, $newRates) {
    if($configJson['shipping_taxable']) {   
      $attributes = array(
        'code' => 'Retail Customer-Shipping-Rate 1',        
        'tax_customer_class' => array(3), 
        'tax_product_class' => array(4), 
        'tax_rate' => $newRates,
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
}
?>