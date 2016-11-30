<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * Event listener (common)
 */
class EventListener extends \XLite\Base\Singleton
{
    /**
     * Errors
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Handle event
     *
     * @param string $name      Event name
     * @param array  $arguments Event arguments OPTIONAL
     *
     * @return boolean
     */
    public function handle($name, array $arguments = [])
    {
        $result = false;
        $this->errors = [];

        $list = $this->getListeners();

        if (isset($list[$name])) {
            $list = is_array($list[$name]) ? $list[$name] : [$list[$name]];
            foreach ($list as $class) {
                if ($class::handle($name, $arguments)) {
                    $result = true;
                }
                if ($class::getInstance()->getErrors()) {
                    $this->errors = $class::getInstance()->getErrors();
                }

            }
        }

        return $result;
    }

    /**
     * Get errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get events
     *
     * @return array
     */
    public function getEvents()
    {
        return array_keys($this->getListeners());
    }

    /**
     * Get listeners
     *
     * @return array
     */
    protected function getListeners()
    {
        return [
            'probe'                 => ['XLite\Core\EventListener\Probe'],
            'export'                => ['XLite\Core\EventListener\Export'],
            'import'                => ['XLite\Core\EventListener\Import'],
            'imageResize'           => ['XLite\Core\EventListener\ImageResize'],
            'quickData'             => ['XLite\Core\EventListener\QuickData'],
            'removeData'            => ['XLite\Core\EventListener\RemoveData'],
            'membershipsQuickData'  => ['XLite\Core\EventListener\MembershipsQuickData'],
            'integrityCheck'        => ['XLite\Core\EventListener\IntegrityCheck'],
            'sales'                 => ['XLite\Core\EventListener\Sales'],
        ];
    }
}
