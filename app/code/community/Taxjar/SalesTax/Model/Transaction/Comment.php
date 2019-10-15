<?php
/**
 * Taxjar_SalesTax
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Taxjar
 * @package    Taxjar_SalesTax
 * @copyright  Copyright (c) 2019 TaxJar. TaxJar is a trademark of TPS Unlimited, Inc. (http://www.taxjar.com)
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * TaxJar Extension UI
 * Returns transaction sync content
 */
class Taxjar_SalesTax_Model_Transaction_Comment
{
    /**
     * Display transaction sync content
     *
     * @param void
     * @return string
     */
    public function getCommentText()
    {
        $syncUrl = Mage::helper('adminhtml')->getUrl('adminhtml/tax_transaction');
        $htmlString = "<p class='note'><span>Sync orders and refunds with TaxJar for automated sales tax reporting and filing. Complete and closed transactions sync automatically on update.</span></p><br/>";
        $htmlString .= "<p><button type='button' class='scalable' onclick='window.location=\"$syncUrl\"'><span>Sync Transactions</span></button></p><br>";
        return $htmlString;
    }

    private function _getPopupUrl($authUrl)
    {
        $popupUrl = $authUrl . '/smartcalcs/connect/magento/upgrade_account/?store=' . urlencode($this->_getStoreOrigin());
        $pluginVersion = Mage::getConfig()->getModuleConfig('Taxjar_SalesTax')->version;

        if ($this->_getStoreGeneralEmail()) {
            $popupUrl .= '&email=' . urlencode($this->_getStoreGeneralEmail());
        }

        $popupUrl .= '&plugin=magento&version=' . $pluginVersion;

        return $popupUrl;
    }

    /**
     * Get current store origin
     *
     * @param void
     * @return string
     */
    private function _getStoreOrigin()
    {
        $protocol = Mage::app()->getRequest()->isSecure() ? 'https://' : 'http://';
        return $protocol . $_SERVER['HTTP_HOST'];
    }

    /**
     * Get store general contact email if non-default
     *
     * @param void
     * @return string
     */
    private function _getStoreGeneralEmail()
    {
        $email = Mage::getStoreConfig('trans_email/ident_general/email');
        if ($email != 'owner@example.com') {
            return $email;
        } else {
            return '';
        }
    }
}
