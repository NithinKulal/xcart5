<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Validator\String\ObjectId;

/**
 * Country code
 */
class Country extends \XLite\Core\Validator\TypeString
{
    /**
     * Only-enabled country validation flag
     *
     * @var boolean
     */
    protected $onlyEnabled = false;

    /**
     * Constructor
     *
     * @param boolean $nonEmpty    Non-empty flag OPTIONAL
     * @param boolean $onlyEnabled Only enabled flag OPTIONAL
     *
     * @return void
     */
    public function __construct($nonEmpty = false, $onlyEnabled = false)
    {
        parent::__construct($nonEmpty);

        $this->onlyEnabled = $onlyEnabled;
    }

    /**
     * Validate
     *
     * @param mixed $data Data
     *
     * @return void
     * @throws \XLite\Core\Validator\Exception
     */
    public function validate($data)
    {
        parent::validate($data);

        if (0 < strlen($data)) {
            $country = $this->sanitize($data);
            if (!$country) {
                throw $this->throwError('Not a country code');

            } else {
                if ($this->onlyEnabled && !$country->getEnabled()) {
                    throw $this->throwError('Country is not enabled');
                }
            }
        }
    }

    /**
     * Sanitize
     *
     * @param mixed $data Daa
     *
     * @return mixed
     */
    public function sanitize($data)
    {
        return 0 < strlen($data)
            ? \XLite\Core\Database::getRepo('XLite\Model\Country')->find($data)
            : null;
    }

}
