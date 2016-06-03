<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Validator\String;

/**
 * Regular expression
 */
class RegExp extends \XLite\Core\Validator\TypeString
{
    /**
     * Regular expression
     *
     * @var string
     */
    protected $regExp;

    /**
     * Constructor
     *
     * @param boolean $nonEmpty Non-empty flag OPTIONAL
     * @param string  $regExp   Regular expression OPTIONAL
     */
    public function __construct($nonEmpty = false, $regExp = null)
    {
        parent::__construct($nonEmpty);

        if ($regExp) {
            $this->setRegExp($regExp);
        }
    }

    /**
     * Set regular expression
     *
     * @param string $regExp Regular expression
     *
     * @return void
     */
    public function setRegExp($regExp)
    {
        $this->regExp = $regExp;
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

        if (!$this->regExp) {
            throw $this->throwInternalError('Regular expression is empty');
        }

        if (0 < strlen($data) && !preg_match($this->regExp, $data)) {
            throw $this->throwError($this->getRegexpMatchError());
        }
    }

    /**
     * Return regexp match error message
     *
     * @return string
     */
    protected function getRegexpMatchError()
    {
        return 'Regular expression does not match';
    }
}
