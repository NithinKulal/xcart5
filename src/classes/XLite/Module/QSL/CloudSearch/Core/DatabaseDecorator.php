<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Core;

/**
 * Database
 */
abstract class DatabaseDecorator extends \XLite\Core\Database implements \XLite\Base\IDecorator
{
    /**
     * Start Doctrine entity manager
     *
     * @return void
     */
    public function startEntityManager()
    {
        $this->configuration->addCustomStringFunction('field', '\\XLite\\Module\\QSL\\CloudSearch\\Core\\Doctrine\\FieldFunction');

        parent::startEntityManager();
    }
}
