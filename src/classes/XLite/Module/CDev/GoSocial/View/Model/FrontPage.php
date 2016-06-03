<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoSocial\View\Model;

/**
 * Root category (front page) model widget extention
 */
class FrontPage extends \XLite\View\Model\FrontPage implements \XLite\Base\IDecorator
{
    /**
     * Add useCustomOG field to the list of included fields
     *
     * @return array
     */
    protected function getIncludedFields()
    {
        $fields = parent::getIncludedFields();
        $fields[] = 'useCustomOG';

        return $fields;
    }
}
