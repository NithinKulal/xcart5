<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Validator\Pair;

/**
 * Abstarct hash array pair validator
 */
abstract class APair extends \XLite\Core\Validator\AValidator
{
    /**
     * Modes
     */
    const STRICT = 'strict';
    const SOFT   = 'soft';

    /**
     * Validation mode
     *
     * @var string
     */
    protected $mode = self::STRICT;

    /**
     * Constructor
     *
     * @param string $mode Validation mode OPTIONAL
     *
     * @return void
     */
    public function __construct($mode = self::STRICT)
    {
        parent::__construct();

        $this->mode = $mode;
    }
}
