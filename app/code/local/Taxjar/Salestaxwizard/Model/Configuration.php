<?php
class Taxjar_Salestaxwizard_Model_Configuration {


  public function setShippingTaxability($configJson) {
    $taxClass = 0;
    if($configJson['shipping_taxable']) {
      $taxClass = 4;
    }
    $this->_setConfig('tax/classes/shipping_tax_class', $taxClass);
  }

  public function setTaxBasis($configJson) {
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



}
?>