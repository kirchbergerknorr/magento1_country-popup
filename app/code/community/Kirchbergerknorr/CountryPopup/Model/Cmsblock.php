<?php
class Kirchbergerknorr_CountryPopup_Model_Cmsblock
{
    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $blocks = Mage::getModel('cms/block')->getCollection();

            $options = array();
            if ($blocks) {
                foreach ($blocks as $block) {
                    $options[$block->getIdentifier()] = array(
                        'value'=>$block->getIdentifier(),
                        'label'=>$block->getIdentifier()
                    );
                }
            }
            $this->_options = $options;
        }

        return $this->_options;
    }
}