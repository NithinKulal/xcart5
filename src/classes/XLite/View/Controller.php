<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Controller main widget
 */
class Controller extends \XLite\View\AView
{
    /**
     * Content of the currnt page
     * NOTE: this is a text, so it's not passed by reference; do not wrap it into a getter (or pass by reference)
     * NOTE: until it's not accessing via the function, do not change its access modifier
     *
     * @var string
     */
    public static $bodyContent = null;

    /**
     * Get html tag prefixes
     *
     * @return array
     */
    public static function defineHTMLPrefixes()
    {
        return array();
    }

    /**
     * __construct
     *
     * @param array  $params          Widget params OPTIONAL
     * @param string $contentTemplate Central area template OPTIONAL
     *
     * @return void
     */
    public function __construct(array $params = array(), $contentTemplate = null)
    {
        parent::__construct($params);

        $this->template = $contentTemplate;
    }

    /**
     * Show current page and, optionally, footer
     *
     * @param string $template Template file name OPTIONAL
     *
     * @return void
     */
    public function display($template = null)
    {
        if (!$this->isSilent()) {
            $this->displayPage($template);
        }

        if ($this->isDumpStarted()) {
            $this->refreshEnd();
        }

        $this->postprocess();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->isAJAXCenterRequest() ? 'layout/content/center_top.twig' : 'body.twig';
    }

    /**
     * CSS classes which are defined in the defineBodyClasses() method are assembled into the one string
     *
     * @return string
     *
     * @see \XLite\View\Content::defineBodyClasses()
     */
    protected function getBodyClasses()
    {
        return implode(' ', $this->defineBodyClasses());
    }

    /**
     * The layout defines the specific CSS classes for the 'body' tag
     * The body CSS classes can define:
     *
     * AREA: area-a / area-c
     * SKINS that are added to this interface: skin-<skin1>, skin-<skin2>, ...
     * TARGET : target-<target_name>
     * Sidebars: one-sidebar | two-sidebars | no-sidebars | sidebar-first | sidebar-second
     *
     * @return array Array of CSS classes to apply to the 'body' tag
     */
    protected function defineBodyClasses()
    {
        $classes = array(
            'area-' . (\XLite::isAdminZone() ? 'a' : 'c'),
        );

        foreach (array_reverse(\XLite\Core\Layout::getInstance()->getSkins()) as $skin) {
            $classes[] = 'skin-' . $this->prepareCSSClass($skin);
        }

        $classes[] = (
            \XLite\Core\Auth::getInstance()->isLogged()
            && (\XLite\Core\Auth::getInstance()->getProfile()->isAdmin() == \XLite::isAdminZone()
                || !\XLite::isAdminZone())
        )
            ? 'authorized'
            : 'unauthorized';

        $classes[] = 'target-' . str_replace('_', '-', \XLite\Core\Request::getInstance()->target);

        $first = \XLite\Core\Layout::getInstance()->isSidebarFirstVisible();
        $second = \XLite\Core\Layout::getInstance()->isSidebarSecondVisible();

        if ($first && $second) {
            $classes[] = 'two-sidebars';

        } elseif ($first || $second) {
            $classes[] = 'one-sidebar';

        } else {
            $classes[] = 'no-sidebars';
        }

        if ($first) {
            $classes[] = 'sidebar-first';
        }

        if ($second) {
            $classes[] = 'sidebar-second';
        }

        $classes = \XLite::getController()->defineBodyClasses($classes);

        return $classes;
    }

    /**
     * Before using the CSS class in the 'class' attribute it must be prepared to be valid
     * The restricted symbols are changed to '-'
     *
     * @param string $class CSS class name to be prepared
     *
     * @return string
     *
     * @see \XLite\View\AView::defineBodyClasses()
     */
    protected function prepareCSSClass($class)
    {
        return preg_replace('/[^a-z0-9_-]+/Sis', '-', $class);
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
            self::PARAM_SILENT       => new \XLite\Model\WidgetParam\TypeBool('Silent', false),
            self::PARAM_DUMP_STARTED => new \XLite\Model\WidgetParam\TypeBool('Dump started', false)
        );
    }

    /**
     * isSilent
     *
     * @return boolean
     */
    protected function isSilent()
    {
        return $this->getParam(self::PARAM_SILENT);
    }

    /**
     * isDumpStarted
     *
     * @return boolean
     */
    protected function isDumpStarted()
    {
        return $this->getParam(self::PARAM_DUMP_STARTED);
    }

    /**
     * getContentWidget
     *
     * @return \XLite\View\AView
     */
    protected function getContentWidget()
    {
        return $this->getWidget(array(\XLite\View\AView::PARAM_TEMPLATE => $this->template), '\XLite\View\Content');
    }

    /**
     * prepareContent
     *
     * @return void
     */
    protected function prepareContent()
    {
        self::$bodyContent = $this->getContentWidget()->getContent();
    }

    /**
     * Return TRUE  if widget must be displayed inside CMS content.
     * Return FALSE if standalone display mode of LC is used.
     *
     * @return boolean
     */
    protected function useDefaultDisplayMode()
    {
        return $this->isExported();
    }

    /**
     * displayPage
     *
     * @param string $template Template file name OPTIONAL
     *
     * @return void
     */
    protected function displayPage($template = null)
    {
        if ($this->useDefaultDisplayMode()) {
            // Display widget content inside some CMS content
            $this->getContentWidget()->display($template);

        } else {
            // Display widget in standalone display mode
            $this->prepareContent();

            parent::display($template);
        }
    }

    /**
     * refreshEnd
     *
     * @return void
     */
    protected function refreshEnd()
    {
        func_refresh_end();
    }

    /**
     * Get body class
     *
     * @return string
     */
    protected function getBodyClass()
    {
        return implode(' ', $this->defineBodyClasses());
    }

    /**
     * Return common data to send to JS
     *
     * @return array
     */
    protected function getCommonJSData()
    {
        return $this->defineCommonJSData();
    }

    /**
     * Get html tag attributes
     *
     * @return array
     */
    protected function getHTMLAttributes()
    {
        $list = array();

        $prefixes = static::defineHTMLPrefixes();
        if ($prefixes) {
            $data = array();
            foreach ($prefixes as $name => $uri) {
                $data[] = $name . ': ' . $uri;
            }
            $prefixes = implode(' ', $data);
        }

        if ($prefixes) {
            $list['prefix'] = $prefixes;
        }

        return $list;
    }
}
