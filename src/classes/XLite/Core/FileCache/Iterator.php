<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\FileCache;

/**
 * Iterator 
 */
class Iterator extends \FilterIterator
{
    /**
     * Regular expression
     * 
     * @var string
     */
    protected $regexp;

    /**
     * Set regular expression
     * 
     * @param string $regexp Regular expression
     *  
     * @return void
     */
    public function setRegexp($regexp)
    {
        $this->regexp = $regexp;
    }

    /**
     * Accepter
     * 
     * @return boolean
     */
    public function accept()
    {
        $path = $this->getInnerIterator()->current()->getFilename();

        return preg_match('/^(.+)\.php$/Ss', $path, $match)
            && preg_match($this->regexp, $match[1]);
    }
}


