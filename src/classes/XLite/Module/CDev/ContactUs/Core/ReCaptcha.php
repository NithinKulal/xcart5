<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ContactUs\Core;

/**
 * Class ReCaptcha
 */
class ReCaptcha extends \XLite\Base\Singleton
{
    /**
     * Private (secret) key
     *
     * @var null
     */

    protected $privateKey = null;

    /**
     * Public (site) key
     *
     * @var null
     */
    protected $publicKey = null;

    /**
     * @var \ReCaptcha\ReCaptcha
     */
    protected $reCaptchaInstance = null;

    /**
     * Constructor
     */
    protected function __construct()
    {
        $config = \XLite\Core\Config::getInstance()->CDev->ContactUs;

        $this->setPrivateKey($config->recaptcha_private_key);
        $this->setPublicKey($config->recaptcha_public_key);

        static::preloadIncludes();
    }

    /**
     * Return ReCaptcha
     *
     * @return \ReCaptcha\ReCaptcha
     */
    protected function getReCaptcha()
    {
        if (null === $this->reCaptchaInstance) {
            $this->reCaptchaInstance = new \ReCaptcha\ReCaptcha($this->getPrivateKey());
        }

        return $this->reCaptchaInstance;
    }

    /**
     * Verify response
     *
     * @param $response
     *
     * @return null|\ReCaptcha\Response
     */
    public function verify($response)
    {
        return $this->getReCaptcha() ? $this->getReCaptcha()->verify($response) : null;
    }

    /**
     * Check if this configured
     *
     * @return bool
     */
    public function isConfigured()
    {
        return strlen($this->getPrivateKey()) && strlen($this->getPublicKey());
    }

    /**
     * Return list or js urls
     *
     * @return array
     */
    public function getJsResources()
    {
        return ['https://www.google.com/recaptcha/api.js'];
    }

    /**
     * Return widget html code
     *
     * @return string
     */
    public function getWidget()
    {
        $result = '';

        if ($this->isConfigured()) {
            foreach ($this->getJsResources() as $resource) {
                $result .= "<script src=\"{$resource}\" async defer></script>";
            }

            $result .= '<div class="g-recaptcha" data-sitekey="' . $this->getPublicKey() . '"></div>';
        }

        return $result;
    }

    /**
     * Return PrivateKey
     *
     * @return string
     */
    public function getPrivateKey()
    {
        return (string)$this->privateKey;
    }

    /**
     * Set PrivateKey
     *
     * @param string $privateKey
     *
     * @return $this
     */
    public function setPrivateKey($privateKey)
    {
        $this->privateKey = $privateKey;
        return $this;
    }

    /**
     * Return PublicKey
     *
     * @return string
     */
    public function getPublicKey()
    {
        return (string)$this->publicKey;
    }

    /**
     * Set PublicKey
     *
     * @param string $publicKey
     *
     * @return $this
     */
    public function setPublicKey($publicKey)
    {
        $this->publicKey = $publicKey;
        return $this;
    }

    /**
     * Load ReCaptcha autoloader
     *
     * @return void
     */
    protected static function preloadIncludes()
    {
        include_once LC_DIR_MODULES . 'CDev' . LC_DS . 'ContactUs' . LC_DS . 'lib' . LC_DS . 'autoload.php';
    }
}