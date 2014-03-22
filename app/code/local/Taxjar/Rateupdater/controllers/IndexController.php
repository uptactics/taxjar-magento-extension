<?php

class Taxjar_Rateupdater_IndexController extends Mage_Core_Controller_Front_Action {        
    public function indexAction() {
        echo "Let's update some friggin' rates!";
        $rateModel = Mage::getModel('tax/calculation_rate');
        $rateModel->setCalculationRateId(3);
        $rateModel->setTaxCountryId('US');
        $rateModel->setTaxRegionId(12);
        $rateModel->setTaxPostcode('94597');
        $rateModel->setCode('US-CA-walnut-creek-Rate 1');
        $rateModel->setRate('8.2500');
        Mage::log($rateModel);
        $rateModel->save();
    }
}

