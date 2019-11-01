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
 * TaxJar Logger
 * Log transactions to a local file
 */
class Taxjar_SalesTax_Model_Logger
{
    protected $playback = array();
    protected $isRecording;
    protected $filename = 'default.log';

    /**
     * @var boolean
     */
    protected $isForced = false;

    /**
     * Sets the filename used for the logger
     *
     * @param string $filename
     * @return Taxjar_SalesTax_Model_Logger
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * Enables or disables the logger
     * @param boolean $isForced
     * @return Logger
     */
    public function force($isForced = true)
    {
        $this->isForced = $isForced;
        return $this;
    }

    /**
     * Get the temp log filename
     *
     * @return string
     */
    public function getPath()
    {
        return Mage::getBaseDir('log') . DS . 'taxjar' . DS . $this->filename;
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
        if (Mage::getStoreConfig('tax/taxjar/debug') || $this->isForced) {
            try {
                if (!empty($label)) {
                    $label = '[' . strtoupper($label) . '] ';
                }

                $timestamp = date('d M Y H:i:s', time());
                $message = sprintf('%s%s - %s%s', PHP_EOL, $timestamp, $label, $message);

                if (!is_dir(dirname($this->getPath()))) {
                    // dir doesn't exist, make it
                    mkdir(dirname($this->getPath()));
                }

                file_put_contents($this->getPath(), $message, FILE_APPEND);

                if ($this->isRecording) {
                    $this->playback[] = $message;
                }
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError(Mage::helper('taxjar')->__('Could not write to your Magento log directory under /var/log. Please make sure the directory is created and check permissions for %1.', Mage::getBaseDir('log')));
            }
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
        return $this;
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
