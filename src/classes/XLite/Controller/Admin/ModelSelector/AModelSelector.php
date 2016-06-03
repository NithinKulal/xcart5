<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin\ModelSelector;

/**
 * Model selector abstract
 */
abstract class AModelSelector extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Get data of the model request
     *
     * @return \Doctrine\ORM\PersistentCollection | array
     */
    abstract protected function getData();

    /**
     * Format model text presentation
     *
     * @param mixed $item Model item
     *
     * @return string
     */
    abstract protected function formatItem($item);

    /**
     * Defines the model value
     *
     * @param mixed $item Model item
     *
     * @return string
     */
    abstract protected function getItemValue($item);

    /**
     * Define specific data structure which will be sent in the triggering event (model.selected)
     *
     * @param mixed $item Model item
     *
     * @return string
     */
    protected function defineDataItem($item)
    {
        return array(
            'presentation' => $this->formatItem($item),
        );
    }

    /**
     * Process request
     *
     * @return void
     */
    public function processRequest()
    {
        header('Content-Type: text/html; charset=utf-8');

        \Includes\Utils\Operator::flush($this->getJSONData());
    }

    /**
     * Final data presentation. JSON array:
     * key => array(data)
     *
     * @return string
     */
    protected function getJSONData()
    {
        $data = $this->getData();
        array_walk($data, array($this, 'prepareItem'));

        return json_encode(
            array(
                $this->getKey() => (false === $data ? array() : $data),
            )
        );
    }

    /**
     * Format the value for the method: $this->getJSONData()
     *
     * @param mixed   &$item
     * @param integer $index
     *
     * @return void
     */
    public function prepareItem(&$item, $index)
    {
        $item = array(
            'text_presentation'  => $this->formatItem($item),
            'value'              => $this->getItemValue($item),
            'data'               => $this->defineDataItem($item)
        );
    }

    /**
     * The main value to search between the models
     *
     * @return string
     */
    protected function getKey()
    {
        return \XLite\Core\Request::getInstance()->search;
    }
}
