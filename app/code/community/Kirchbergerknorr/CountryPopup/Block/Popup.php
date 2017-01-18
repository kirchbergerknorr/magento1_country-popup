<?php
/**
 * Country Popup Block
 *
 * @category    Kirchbergerknorr
 * @package     Kirchbergerknorr_CountryPopup
 * @author      Aleksey Razbakov <ar@kirchbergerknorr.de>
 * @copyright   Copyright (c) 2017 kirchbergerknorr GmbH (http://www.kirchbergerknorr.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Kirchbergerknorr_CountryPopup_Block_Popup extends Mage_Core_Block_Template
{
    const COOKIE_NAME = 'country_popup';

    protected $_blockCode;
    protected $_countriesStoreViewsMapping;
    protected $_countryCode;
    protected $_showPopupOnUndefinedCountry;
    protected $_cookieName;

    /**
     * Load configuration
     */
    public function __construct()
    {
        parent::__construct();

        $this->_blockCode = Mage::getStoreConfig('kk_country_popup/general/block');
        $this->_cookieName = Mage::getStoreConfig('kk_country_popup/general/cookie_name');
        $this->_showPopupOnUndefinedCountry = Mage::getStoreConfig('kk_country_popup/general/show_popup_on_undefined_country');

        $countriesStoreViewsMappingConfig = Mage::getStoreConfig('kk_country_popup/general/countries');
        $this->_countriesStoreViewsMapping = array();

        if ($countriesStoreViewsMappingConfig) {
            $countriesStoreViewsMappingConfig = unserialize($countriesStoreViewsMappingConfig);
            if (is_array($countriesStoreViewsMappingConfig)) {
                foreach($countriesStoreViewsMappingConfig as $mappingItem) {
                    $this->_countriesStoreViewsMapping[$mappingItem['country_code']] = $mappingItem['storeview_id'];
                }
            } else {
                // there was some problem reading mapping
            }
        }
    }

    protected function _getStoreViewIdByCountryCode($countryCode)
    {
        if ($countryCode && isset($this->_countriesStoreViewsMapping[$countryCode])) {
            return $this->_countriesStoreViewsMapping[$countryCode];
        } else {
            return 'admin';
        }
    }

    /**
     * Get country popup block name
     *
     * @return string
     */
    protected function _getCountryCode()
    {
        if (!$this->_countryCode) {
            $geoIP = Mage::getSingleton('geoip/country');
            $this->_countryCode = $geoIP->getCountry();
        }

        return $this->_countryCode;
    }

    /**
     * Get country popup block content
     *
     * @return string
     */
    public function getBlockContent()
    {
        $countryCode = $this->_getCountryCode();

        $storeViewId = $this->_getStoreViewIdByCountryCode($countryCode);

        $block = Mage::getModel('cms/block')->setStoreId($storeViewId)->load($this->_blockCode, 'identifier');

        if (!$block) {
            return false;
        }

        $appEmulation = Mage::getSingleton('core/app_emulation');

        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeViewId);

        $filter = Mage::getModel('cms/template_filter');
        $result = $filter->filter($block->getContent());

        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        return $result;
    }

    public function isShown()
    {
        $cookie = Mage::getModel('core/cookie')->get($this->getCookieName());

        if ($cookie) {
            return false;
        }

        $countryCode = $this->_getCountryCode();
        if (!$this->_showPopupOnUndefinedCountry && !$countryCode) {
            return false;
        }

        if (Mage::app()->getStore()->getId() == $this->_getStoreViewIdByCountryCode($countryCode)) {
            return false;
        }

        if (!$this->getBlockContent()) {
            return false;
        }

        return true;
    }

    public function getCookieName()
    {
        return $this->_cookieName;
    }
}