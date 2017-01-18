<?php
class Kirchbergerknorr_CountryPopup_Model_Store
{
    public function toOptionArray() {
        $result = Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, false);
        return $result;
    }
}