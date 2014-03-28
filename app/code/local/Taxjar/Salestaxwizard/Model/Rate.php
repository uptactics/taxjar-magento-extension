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

    return $rateModel->getId();
  }
  
}
?>