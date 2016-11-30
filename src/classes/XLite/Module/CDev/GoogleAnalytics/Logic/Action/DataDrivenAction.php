<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\Logic\Action;

class DataDrivenAction implements IAction
{
    protected $data;
    /**
     * @var
     */
    private $name;

    /**
     * ItemChange constructor.
     *
     * @param       $name
     * @param array $data
     */
    public function __construct($name, array $data)
    {
        $this->data = $data;
        $this->name = $name;
    }

    /**
     * @return bool
     */
    public function isApplicable()
    {
        return \XLite\Module\CDev\GoogleAnalytics\Main::isECommerceEnabled()
            && $this->data;
    }

    /**
     * @return array
     */
    public function getActionData()
    {
        $result = [
            'ga-type'   => $this->name,
            'ga-action' => 'event',
            'data'      => $this->data
        ];

        return $result;
    }
}