<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View;

/**
 * Abstract widget
 */
abstract class AView extends \XLite\View\AView implements \XLite\Base\IDecorator
{
    /**
     * Current templates tree
     *
     * @var \XLite\Core\CommonGraph
     */
    protected static $tree = null;

    /**
     * Current tree node
     *
     * @var \XLite\Core\CommonGraph
     */
    protected static $current = null;

    /**
     * Template id
     *
     * @var integer
     */
    protected static $templateId = 0;

    /**
     * So called "static constructor".
     * NOTE: do not call the "parent::__constructStatic()" explicitly: it will be called automatically
     *
     * @return void
     */
    public static function __constructStatic()
    {
        static::$tree = new \XLite\Core\CommonGraph();
        static::$current = static::$tree;
    }

    /**
     * Returns current templates tree
     *
     * @return \XLite\Core\CommonGraph
     */
    public static function getTree()
    {
        return static::$tree;
    }

    /**
     * Returns current templates tree (HTML)
     *
     * @return string
     */
    public static function getHtmlTree()
    {
        $result = '';
        $htmlTree = static::buildHtmlTreeNode(static::$tree);

        if ($htmlTree) {
            $result .= '<div id="themeTweaker_wrapper" style="display: none;">';
            $title = static::t('Theme tweaker');
            $label = static::t('Pick template from page element');

            $result .= <<<HTML
<div class="themeTweaker-control-panel">
<div class="themeTweaker-title">{$title}</div>
<div class="themeTweaker-label">{$label}</div>
<div class="themeTweaker-onoffswitch">
  <input id="themeTweaker-switcher" type="checkbox" />
  <label for="themeTweaker-switcher">
    <span class="fa fa-check"></span>
  </label>
</div>
</div>
HTML;
            $result .= '<div id="themeTweaker_tree">';
            $result .= static::buildHtmlTreeNode(static::$tree);
            $result .= '</div></div>';
        }

        return $result;
    }

    /**
     * Returns current templates tree (HTML)
     *
     * @param \Includes\DataStructure\Graph $node Node
     *
     * @return string
     */
    public static function buildHtmlTreeNode(\Includes\DataStructure\Graph $node)
    {
        $result = '';
        $children = $node->getChildren();

        if ($children) {
            $result = '<ul>';

            /** @var \Includes\DataStructure\Graph $child */
            foreach ($children as $child) {
                $data = $child->getData();

                $additionalData = array();
                if ($data->isList) {
                    $additionalData['disabled'] = true;
                }

                $label = $data->class
                    ? sprintf('%s (%s)', $child->getKey(), $data->class)
                    : $child->getKey();

                $result .= sprintf(
                    '<li id="template_%s" data-template-id="%s" data-template-path="%s" data-jstree=\'%s\'>%s%s</li>',
                    $data->templateId,
                    $data->templateId,
                    $child->getKey(),
                    json_encode($additionalData),
                    $label,
                    static::buildHtmlTreeNode($child)
                );
            }

            $result .= '</ul>';
        }

        return $result;
    }

    /**
     * Returns current templates tree (HTML)
     *
     * @return string
     */
    public static function getJsonTree()
    {
        $result = static::buildJsonTreeNode(static::$tree);

        return json_encode($result);
    }

    /**
     * Returns current templates tree (JSON)
     *
     * @param \Includes\DataStructure\Graph $node Node
     *
     * @return array
     */
    public static function buildJsonTreeNode(\Includes\DataStructure\Graph $node)
    {
        $result = array();

        $children = $node->getChildren();

        if ($children) {
            /** @var \Includes\DataStructure\Graph $child */
            foreach ($children as $child) {
                $data = $child->getData();

                $label = $data->class
                    ? sprintf('%s (%s)', $child->getKey(), $data->class)
                    : $child->getKey();

                $result[] = array(
                    'id' => sprintf('template_%s', $data->templateId),
                    'text' => $label,
                    'state' => array(
                        'disabled' => $data->isList,
                    ),
                    'li_attr' => array(
                        'data-template-id' => $data->templateId,
                        'data-template-path' => $child->getKey(),
                    ),
                    'children' => static::buildJsonTreeNode($child),
                );
            }
        }

        return $result;
    }


    /**
     * Get list of methods, priorities and interfaces for the resources
     *
     * @return array
     */
    protected static function getResourcesSchema()
    {
        $schema = parent::getResourcesSchema();
        $schema[] =  array('getThemeTweakerCustomFiles', 1000, 'custom');

        return $schema;
    }

    /**
     * Via this method the widget registers the CSS files which it uses.
     * During the viewers initialization the CSS files are collecting into the static storage.
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        if ($this->isMarkTemplates()) {
            $list[] = 'modules/XC/ThemeTweaker/template_editor/vakata-jstree/dist/themes/default/style.min.css';
            $list[] = 'modules/XC/ThemeTweaker/template_editor/style.css';
        }

        return $list;
    }

    /**
     * Via this method the widget registers the JS files which it uses.
     * During the viewers initialization the JS files are collecting into the static storage.
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        if ($this->isMarkTemplates()) {
            $list[] = 'modules/XC/ThemeTweaker/template_editor/vakata-jstree/dist/jstree.min.js';
            $list[] = 'modules/XC/ThemeTweaker/template_editor/editor.js';
        }

        return $list;
    }

    /**
     * Return custom common files
     *
     * @return array
     */
    protected function getThemeTweakerCustomFiles()
    {
        $files = array();

        if (
            !\XLite::isAdminZone()
        ) {
            if (
                \XLite\Core\Config::getInstance()->XC->ThemeTweaker->use_custom_js
            ) {
                $files[static::RESOURCE_JS] = array(
                    array(
                        'file'  => 'theme/custom.js',
                        'media' => 'all'
                    )
                );
            }

            if (
                \XLite\Core\Config::getInstance()->XC->ThemeTweaker->use_custom_css
            ) {
                $files[static::RESOURCE_CSS] = array(
                    array(
                        'file'  => 'theme/custom.css',
                        'media' => 'all'
                    )
                );
            }
        }

        return $files;
    }

    /**
     * Prepare template display
     *
     * @param string $template Template short path
     *
     * @return array
     */
    protected function prepareTemplateDisplay($template)
    {
        $result = parent::prepareTemplateDisplay($template);

        if ($this->isMarkTemplates()) {

            $templateId = static::$templateId++;

            $localPath = substr($template, strlen(LC_DIR_SKINS));
            $current = new \XLite\Core\CommonGraph($localPath);

            $data = new \XLite\Core\CommonCell();
            $data->class = get_class($this);
            $data->templateId = $templateId;
            $current->setData($data);

            static::$current->addChild($current);
            static::$current = $current;

            $templateWrapperText = get_class($this) . ' : ' . $localPath . ' (' . $templateId . ')'
                . ($this->viewListName ? ' [\'' . $this->viewListName . '\' list child]' : '');

            echo ('<!-- ' . $templateWrapperText . ' {' . '{{ -->');
            $result['templateWrapperText'] = $templateWrapperText;
        }

        return $result;
    }

    /**
     * Finalize template display
     *
     * @param string $template     Template short path
     * @param array  $profilerData Profiler data which is calculated and returned in the 'prepareTemplateDisplay' method
     *
     * @return void
     */
    protected function finalizeTemplateDisplay($template, array $profilerData)
    {
        if (isset($profilerData['templateWrapperText'])) {
            echo ('<!-- }}' . '} ' . $profilerData['templateWrapperText'] . ' -->');

            static::$current = static::$current->getParent();
        }

        parent::finalizeTemplateDisplay($template, $profilerData);
    }

    /**
     * Display view list content
     *
     * @param string $list      List name
     * @param array  $arguments List common arguments OPTIONAL
     *
     * @return void
     */
    public function displayViewListContent($list, array $arguments = array())
    {
        if ($this->isMarkTemplates()) {
            $templateId = static::$templateId++;

            $current = new \XLite\Core\CommonGraph($list);

            $data = new \XLite\Core\CommonCell();
            $data->templateId = $templateId;
            $data->isList = true;
            $current->setData($data);

            static::$current->addChild($current);
            static::$current = $current;
        }

        parent::displayViewListContent($list, $arguments);

        if ($this->isMarkTemplates()) {
            static::$current = static::$current->getParent();
        }
    }

    /**
     * Mark templates
     *
     * @return boolean
     */
    protected function isMarkTemplates()
    {
        return \XLite\Core\Config::getInstance()->XC->ThemeTweaker->edit_mode
            && !\XLite::isAdminZone()
            && \XLite\Module\XC\ThemeTweaker\Main::isTargetAllowed()
            && \XLite\Module\XC\ThemeTweaker\Main::isUserAllowed()
            && !\XLite\Core\Request::getInstance()->isPost()
            && !\XLite\Core\Request::getInstance()->isCLI()
            && !\XLite\Core\Request::getInstance()->isAJAX()
            && !\Includes\Decorator\Utils\CacheManager::isRebuildNeeded();
    }

    /**
     * Cache allowed
     *
     * @return boolean
     */
    protected function isCacheAllowed()
    {
        return parent::isCacheAllowed() && !\XLite\Core\Config::getInstance()->XC->ThemeTweaker->edit_mode;
    }
}

// Call static constructor
\XLite\Module\XC\ThemeTweaker\View\AView::__constructStatic();
