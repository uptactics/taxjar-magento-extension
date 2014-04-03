<?php
class Taxjar_Salestaxwizard_Model_Comment{
    public function getCommentText(){ //this method must exits. It returns the text for the comment
      $regionId       = Mage::getStoreConfig('shipping/origin/region_id');
      $regionName     = Mage::getModel('directory/region')->load($regionId)->getDefaultName();
      $lastUpdate     = Mage::getStoreConfig('salestaxwizard/config/last_update');
      if($lastUpdate){
        return "<br/><p>Your " . $regionName . " sales tax rates were last updated on: <ul><li>" . $lastUpdate . "</li></ul></p>";
      } else {
      return "<br/><p>Enter your TaxJar API Token to import current sales tax rates for all zip codes in " . $regionName . ", your state of origin as set in Shipping Settings.  To get an API Token, go to <a href='https://app.taxjar.com/account' target='_blank'>TaxJar's Account Screen.</a></p><p>For more information on how your tax settings are changed, <a href='http://taxjar.com/magento/tax-settings' target='_blank'>click here</a>.</p>";
    }
  }
}
?>