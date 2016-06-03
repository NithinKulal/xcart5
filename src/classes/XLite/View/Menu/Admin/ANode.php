<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Admin;

/**
 * Abstract menu node
 */
class ANode extends \XLite\View\AView
{
    /**
     * Widget param names
     */
    const PARAM_TITLE         = 'title';
    const PARAM_TOOLTIP       = 'tooltip';
    const PARAM_LINK          = 'link';
    const PARAM_LIST          = 'list';
    const PARAM_BLOCK         = 'block';
    const PARAM_CLASS         = 'className';
    const PARAM_TARGET        = 'linkTarget';
    const PARAM_EXTRA         = 'extra';
    const PARAM_PERMISSION    = 'permission';
    const PARAM_PUBLIC_ACCESS = 'publicAccess';
    const PARAM_CHILDREN      = 'children';
    const PARAM_SELECTED      = 'selected';
    const PARAM_BLANK_PAGE    = 'blankPage';
    const PARAM_ICON_FONT     = 'iconFont';
    const PARAM_ICON_SVG      = 'iconSVG';
    const PARAM_ICON_HTML     = 'iconHTML';
    const PARAM_ICON_IMG      = 'iconIMG';
    const PARAM_LABEL         = 'label';
    const PARAM_LABEL_LINK    = 'labelLink';
    const PARAM_LABEL_TITLE   = 'labelTitle';
    const PARAM_EXPANDED      = 'expanded';

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/node.twig';
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
            static::PARAM_TITLE         => new \XLite\Model\WidgetParam\TypeString('Name', ''),
            static::PARAM_TOOLTIP       => new \XLite\Model\WidgetParam\TypeString('Tooltip', ''),
            static::PARAM_LINK          => new \XLite\Model\WidgetParam\TypeString('Link', ''),
            static::PARAM_BLOCK         => new \XLite\Model\WidgetParam\TypeString('Block', ''),
            static::PARAM_LIST          => new \XLite\Model\WidgetParam\TypeString('List', ''),
            static::PARAM_CLASS         => new \XLite\Model\WidgetParam\TypeString('Class name', ''),
            static::PARAM_TARGET        => new \XLite\Model\WidgetParam\TypeString('Target', ''),
            static::PARAM_EXTRA         => new \XLite\Model\WidgetParam\TypeCollection('Additional request params', array()),
            static::PARAM_PERMISSION    => new \XLite\Model\WidgetParam\TypeString('Permission', ''),
            static::PARAM_PUBLIC_ACCESS => new \XLite\Model\WidgetParam\TypeBool('Public access', false),
            static::PARAM_BLANK_PAGE    => new \XLite\Model\WidgetParam\TypeBool('Use blank page', false),
            static::PARAM_CHILDREN      => new \XLite\Model\WidgetParam\TypeCollection('Children', array()),
            static::PARAM_SELECTED      => new \XLite\Model\WidgetParam\TypeBool('Selected', false),
            static::PARAM_ICON_FONT     => new \XLite\Model\WidgetParam\TypeString('Icon Awesome font name', ''),
            static::PARAM_ICON_SVG      => new \XLite\Model\WidgetParam\TypeString('Icon SVG image path', ''),
            static::PARAM_ICON_HTML     => new \XLite\Model\WidgetParam\TypeString('Icon HTML', ''),
            static::PARAM_ICON_IMG      => new \XLite\Model\WidgetParam\TypeString('Icon image path', ''),
            static::PARAM_LABEL         => new \XLite\Model\WidgetParam\TypeString('Label', ''),
            static::PARAM_LABEL_LINK    => new \XLite\Model\WidgetParam\TypeString('Label link', ''),
            static::PARAM_LABEL_TITLE   => new \XLite\Model\WidgetParam\TypeString('Label title', ''),
            static::PARAM_EXPANDED      => new \XLite\Model\WidgetParam\TypeBool('Expanded', false),
        );
    }

    /**
     * Return blank page flag (target = "_blank" for the link)
     *
     * @return array
     */
    protected function getBlankPage()
    {
        return $this->getParam(static::PARAM_BLANK_PAGE);
    }

    /**
     * Return children
     *
     * @return array
     */
    protected function getChildren()
    {
        return $this->getParam(static::PARAM_CHILDREN);
    }

    /**
     * Check if submenu available for this item
     *
     * @return string
     */
    protected function hasChildren()
    {
        return (
            '' !== $this->getParam(static::PARAM_LIST)
            && 0 < strlen(trim($this->getViewListContent($this->getListName())))
        ) || $this->getChildren();
    }

    /**
     * Check - node is branch but has empty childs list
     *
     * @return boolean
     */
    protected function isEmptyChildsList()
    {
        return '' !== $this->getParam(static::PARAM_LIST)
            && 0 == strlen(trim($this->getViewListContent($this->getListName())))
            && !$this->getChildren();
    }

    /**
     * Return list name
     *
     * @return string
     */
    protected function getLink()
    {
        $link = null;

        if ($this->getLabel() && $this->getLabelLink()) {
            $link = $this->getLabelLink();

        } elseif ('' !== $this->getParam(static::PARAM_LINK)) {
            $link = $this->getParam(static::PARAM_LINK);

        } elseif ('' !== $this->getNodeTarget()) {
            $link = $this->buildURL($this->getNodeTarget(), '', $this->getParam(static::PARAM_EXTRA));
        }

        return $link;
    }

    /**
     * Return the block text
     *
     * @return string
     */
    protected function getBlock()
    {
        return $this->getParam(static::PARAM_BLOCK);
    }

    /**
     * Return if the the link should be active
     * (linked to a current page)
     *
     * @return boolean
     */
    protected function isCurrentPageLink()
    {
        return $this->getParam(static::PARAM_SELECTED);
    }

    /**
     * Check - node is expanded or not
     * 
     * @return boolean
     */
    protected function isExpanded()
    {
        return $this->getParam(static::PARAM_EXPANDED)
            && $this->getParam(static::PARAM_CHILDREN);
    }

    /**
     * Get link template 
     * 
     * @return string
     */
    protected function getLinkTemplate()
    {
        return $this->getDir() . '/link.twig';
    }

    /**
     * Get contrainer tag attributes 
     * 
     * @return array
     */
    protected function getContainerTagAttributes()
    {
        return array(
            'class' => trim('menu-item ' . $this->getCSSClass()),
        );
    }

    /**
     * Return CSS class for the link item
     *
     * @return string
     */
    protected function getCSSClass()
    {
        $class = $this->getParam(static::PARAM_CLASS);

        if ($this->isCurrentPageLink()) {
            $class .= ' active';
        }

        $class .= $this->getIcon() ? ' icon' : ' no-icon';
        if ($this->isExpanded()) {
            $class .= ' pre-expanded';
        }

        if ($this->getLabel()) {
            $class .= ' has-label';
        }

        return trim($class);
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && !$this->isEmptyChildsList();
    }

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    protected function checkACL()
    {
        $auth = \XLite\Core\Auth::getInstance();

        $additionalPermission = $this->getParam(static::PARAM_PERMISSION);

        return parent::checkACL()
            && (
                $this->getParam(static::PARAM_LIST)
                || $this->getParam(static::PARAM_PUBLIC_ACCESS)
                || $auth->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS)
                || ($additionalPermission && $auth->isPermissionAllowed($additionalPermission))
            );
    }

    /**
     * Get title
     *
     * @return string
     */
    protected function getTitle()
    {
        return $this->getParam(static::PARAM_TITLE);
    }

    /**
     * Get tooltip
     *
     * @return string
     */
    protected function getTooltip()
    {
        return $this->getParam(static::PARAM_TOOLTIP) ?: $this->getTitle();
    }

    /**
     * Get icon
     *
     * @return string
     */
    protected function getIcon()
    {
        if ($this->getParam(static::PARAM_ICON_FONT)) {
            $result = '<i class="fa ' . $this->getParam(static::PARAM_ICON_FONT) . '"></i>';

        } elseif ($this->getParam(static::PARAM_ICON_SVG)) {
            $result = $this->getSVGImage($this->getParam(static::PARAM_ICON_SVG));

        } elseif ($this->getParam(static::PARAM_ICON_HTML)) {
            $result = $this->getParam(static::PARAM_ICON_HTML);

        } elseif ($this->getParam(static::PARAM_ICON_IMG)) {
            $result = $this->getImageTag($this->getParam(static::PARAM_ICON_IMG));

        } else {
            $result = null;
        }

        return $result;
    }

    /**
     * Get image tag
     *
     * @param string $src Image src
     *
     * @return string
     */
    protected function getImageTag($src)
    {
        $result = null;

        if ($src) {
            $result = sprintf('<img src="%s" alt="" />', $src);
        }

        return $result;
    }

    /**
     * Get label
     *
     * @return string
     */
    protected function getLabel()
    {
        return $this->getParam(static::PARAM_LABEL);
    }

    /**
     * Get label link
     *
     * @return string
     */
    protected function getLabelLink()
    {
        return $this->getParam(static::PARAM_LABEL_LINK);
    }

    /**
     * Get label title
     *
     * @return string
     */
    protected function getLabelTitle()
    {
        return $this->getParam(static::PARAM_LABEL_TITLE);
    }

    /**
     * Get link target
     *
     * @return string
     */
    protected function getNodeTarget()
    {
        return $this->getParam(static::PARAM_TARGET);
    }
}
