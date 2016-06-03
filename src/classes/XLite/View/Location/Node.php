<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Location;

/**
 * Node
 */
class Node extends \XLite\View\AView
{
    /**
     * Widget param names
     */
    const PARAM_NAME     = 'name';
    const PARAM_LINK     = 'list';
    const PARAM_SUBNODES = 'subnodes';
    const PARAM_IS_LAST  = 'last';

    /**
     * Static method to create nodes in controller classes
     *
     * @param string $name     Node title
     * @param string $link     Node link OPTIONAL
     * @param array  $subnodes Node subnodes OPTIONAL
     *
     * @return object
     */
    public static function create($name, $link = null, array $subnodes = null)
    {
        return new static(
            array(
                self::PARAM_NAME     => $name,
                self::PARAM_LINK     => $link,
                self::PARAM_SUBNODES => $subnodes,
            )
        );
    }

    /**
     * Check - node is last in nodes list or not
     *
     * @return boolean
     */
    public function isLast()
    {
        return $this->getParam(self::PARAM_IS_LAST);
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'location/node.twig';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_NAME     => new \XLite\Model\WidgetParam\TypeString('Name', ''),
            self::PARAM_LINK     => new \XLite\Model\WidgetParam\TypeString('Link', ''),
            self::PARAM_SUBNODES => new \XLite\Model\WidgetParam\TypeCollection('Subnodes', array()),
            self::PARAM_IS_LAST  => new \XLite\Model\WidgetParam\TypeBool('Is last', false),
        );
    }

    /**
     * Get node name
     *
     * @return string
     */
    protected function getName()
    {
        return $this->getParam(self::PARAM_NAME);
    }

    /**
     * Get link URL
     *
     * @return string
     */
    protected function getLink()
    {
        return $this->getParam(self::PARAM_LINK);
    }

    /**
     * Get maximum amount of location nodes
     *
     * @return integer
     */
    public static function getLocationNodesLimit()
    {
        return \XLite::getInstance()->getController() instanceof \XLite\Controller\Customer\Base\Catalog
            ? \XLite::getInstance()->getController()->getCategorySiblingsLimit() - 1
            : 10;
    }

    /**
     * More link at the bottom needed
     * 
     * @return boolnean
     */
    protected function moreLinkNeeded()
    {
        return static::getLocationNodesLimit() < count($this->getParam(self::PARAM_SUBNODES));
    }

    /**
     * Get link to the parent of current category
     * 
     * @return string
     */
    protected function getMoreLink()
    {
        return $this->buildURL('category', '', array('category_id' => $this->getCategory()->getParent()->getCategoryId()));
    }

    /**
     * Get subnodes
     *
     * @return array
     */
    protected function getSubnodes()
    {
        return array_slice($this->getParam(self::PARAM_SUBNODES), 0, static::getLocationNodesLimit());
    }

    /**
     * Get list container attributes 
     * 
     * @return array
     */
    protected function getListContainerAttributes()
    {
        $attributes = array(
            'class' => array(
                'location-node'
            ),
        );

        if ($this->getSubnodes()) {
            $attributes['class'][] = 'expandable';
        }

        if ($this->isLast()) {
            $attributes['class'][] = 'last';
        }

        return $attributes;
    }
}
