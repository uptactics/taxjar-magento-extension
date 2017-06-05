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
 * @copyright  Copyright (c) 2017 TaxJar. TaxJar is a trademark of TPS Unlimited, Inc. (http://www.taxjar.com)
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * TaxJar Logger
 * Log transactions to a local file
 */
class Taxjar_SalesTax_Model_Logger
{
    protected $playback = array();
    protected $isRecording;

    /**
     * Get the temp log filename
     *
     * @return string
     */
    public function getPath()
    {
        return Mage::getBaseDir('log') . DS . 'taxjar.log';
    }

    /**
     * Save a message to taxjar.log
     *
     * @param string $message
     * @param string $label
     * @throws LocalizedException
     * @return void
     */
    public function log($message, $label = '') {
        try {
            if (!empty($label)) {
                $label = '[' . strtoupper($label) . '] ';
            }

            $timestamp = date('d M Y H:i:s', time());
            $message = sprintf('%s%s - %s%s', PHP_EOL, $timestamp, $label, $message);
            file_put_contents($this->getPath(), $message, FILE_APPEND);

            if ($this->isRecording) {
                $this->playback[] = $message;
            }
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError(Mage::helper('taxjar')->__('Could not write to your Magento log directory under /var/log. Please make sure the directory is created and check permissions for %1.', Mage::getBaseDir('log')));
        }
    }

    /**
     * Enable log recording
     *
     * @return void
     */
    public function record()
    {
        $this->isRecording = true;
    }

    /**
     * Return log recording
     *
     * @return array
     */
    public function playback()
    {
        return $this->playback;
    }
}
