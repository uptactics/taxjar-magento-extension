<?php
class Taxjar_Rateupdater_Model_States
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'CA', 'label'=>Mage::helper('rateupdater')->__('CA')),
            array('value'=>'NY', 'label'=>Mage::helper('rateupdater')->__('NY')),
        );
    }

}
