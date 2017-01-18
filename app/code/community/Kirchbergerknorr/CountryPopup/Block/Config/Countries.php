<?php
class Kirchbergerknorr_CountryPopup_Block_Config_Countries
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $_countryRenderer;
    protected $_storeViewRenderer;

    public function _prepareToRender()
    {
        $this->addColumn('country_code', array(
            'label' => Mage::helper('kk_country_popup')->__('Country'),
            'style' => 'width:100px',
            'renderer' => $this->_getCountryRenderer(),
        ));
        $this->addColumn('storeview_id', array(
            'label' => Mage::helper('kk_country_popup')->__('Store View'),
            'style' => 'width:100px',
            'renderer' => $this->_getStoreViewRenderer(),
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('kk_country_popup')->__('Add');
    }

    protected function _getCountryRenderer()
    {
        if (!$this->_countryRenderer) {
            $this->_countryRenderer = $this->getLayout()->createBlock(
                'kk_country_popup/config_adminhtml_form_field_country', '',
                array('is_render_to_js_template' => true)
            );
        }
        return $this->_countryRenderer;
    }

    protected function _getStoreViewRenderer()
    {
        if (!$this->_storeViewRenderer) {
            $this->_storeViewRenderer = $this->getLayout()->createBlock(
                'kk_country_popup/config_adminhtml_form_field_storeview', '',
                array('is_render_to_js_template' => true)
            );
        }
        return $this->_storeViewRenderer;
    }

    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getCountryRenderer()
                ->calcOptionHash($row->getData('country_code')),
            'selected="selected"'
        );
        $row->setData(
            'option_extra_attr_' . $this->_getStoreViewRenderer()
                ->calcOptionHash($row->getData('storeview_id')),
            'selected="selected"'
        );
    }
}