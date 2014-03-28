<?php
class Taxjar_Salestaxwizard_Model_Rate {

  public function create($rateJson) {
    $rateModel = Mage::getModel('tax/calculation_rate');
    $rateModel->setTaxCountryId('US');
    $rateModel->setTaxRegionId(12);
    $rateModel->setTaxPostcode('94597');
    $rateModel->setCode('US-CA-walnut-creek-Rate 1');
    $rateModel->setRate('8.2500');
    $rateModel->save();
    $rateId = $rateModel->getId();
    

    $taxCalculationData = array(
      'tax_calculation_rate_id'   => $rateId,
      'tax_calculation_rule_id'   => 1,
      'customer_tax_class_id'     => 3,
      'product_tax_class_id'      => 2
    );

    Mage::getSingleton('tax/calculation')->setData($taxCalculationData)->save();

    return $rateId;
  }  

}
?>