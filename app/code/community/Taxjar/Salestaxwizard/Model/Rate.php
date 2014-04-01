<?php
class Taxjar_Salestaxwizard_Model_Rate {

  public function create($rateJson) {    
    $zip       = $rateJson['zip'];
    $rate      = $rateJson['rate'];
    $rateModel = Mage::getModel('tax/calculation_rate');
    $rateModel->setTaxCountryId('US');
    $rateModel->setTaxRegionId(12);
    $rateModel->setTaxPostcode($zip);
    $rateModel->setCode('US-CA-' . $zip);
    $rateModel->setRate($rate);
    $rateModel->save();

    return $rateModel->getId();
  }
  
}
?>