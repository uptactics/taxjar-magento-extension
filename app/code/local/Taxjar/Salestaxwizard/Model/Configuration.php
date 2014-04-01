<?php
class Taxjar_Salestaxwizard_Model_Configuration {


  public function setShippingTaxability($configJson) {
    $taxClass = 0;
    if($configJson['freight_taxable']) {
      $taxClass = 4;
    }
    $this->_setConfig('tax/classes/shipping_tax_class', $taxClass);
  }

  public function setTaxBasis($configJson) {
    $basis = 'shipping';
    if($configJson['tax_source']==='origin') {
      $basis = 'origin';
    }
    $this->_setConfig('tax/calculation/based_on', $basis);
  }

  public function setDisplaySettings() {
    $settings = array(
      'tax/display/type', 
      'tax/display/shipping', 
      'tax/cart_display/price',
      'tax/cart_display/subtotal',
      'tax/cart_display/shipping'
    );
    foreach($settings as $setting) {
      $this->_setConfig($setting, 1);
    }
  }

  private function _setConfig($path, $value){
    $config = new Mage_Core_Model_Config();
    $config->saveConfig($path, $value, 'default', 0);
  }



}
?>