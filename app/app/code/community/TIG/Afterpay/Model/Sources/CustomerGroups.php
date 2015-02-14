<?php
class TIG_Afterpay_Model_Sources_CustomerGroups extends Varien_Object
{
    static public function toOptionArray()
    {
        $customerGroup = Mage::getModel('customer/group');
        $allGroups     = $customerGroup->getCollection()->toOptionHash();
        foreach($allGroups as $key => $allGroup){
              $groups[$key] = array('value' => $allGroup, 'label' => $allGroup);
        }
        return $groups;
    }
}