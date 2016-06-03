<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Core;

/**
 * Events subsystem
 */
class MobileDetect extends \XLite\Base\Singleton
{
    /**
     * Device detection
     *
     * @var \Mobile_Detect
     */
    public $detect;

    /**
     * Function for those of you who wanted to use another library
     * 
     * @return void
     */
    protected static function requireLibrary()
    {
        // Include Mobile_Detect class here to avoid the Autoloader errors
        require_once LC_DIR_LIB . 'Mobile_Detect.php';
    }

    /**
     * Method to access a singleton
     *
     * @return \Mobile_Detect
     */
    public static function getInstance()
    {
        return parent::getInstance()->detect;
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function __construct()
    {
        static::requireLibrary();
        $this->detect = new \Mobile_Detect;
    }
}