<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use XLite\Core\Templating\EngineInterface;
use XLite\Core\Templating\TemplateFinderInterface;

use Symfony\Component\EventDispatcher\EventDispatcher;
use XLite\Core\DependencyInjection\ContainerAwareTrait;
use XLite\Core\Event\WidgetAfterRenderEvent;
use XLite\Core\Event\WidgetBeforeRenderEvent;
use XLite\Core\Events;
use XLite\Core\View\DTO\Assets;
use XLite\Core\View\DTO\RenderedWidget;
use XLite\Core\View\DynamicWidgetInterface;
use XLite\Core\View\DynamicWidgetRenderer;
use XLite\Core\View\RenderingContextFactory;
use XLite\Core\View\RenderingContextInterface;
use XLite\Core\WidgetCache;

/**
 * Abstract widget
 */
abstract class AView extends \XLite\Core\Handler
{
    use ContainerAwareTrait;

    /**
     * Resource types
     */
    const RESOURCE_JS   = 'js';
    const RESOURCE_CSS  = 'css';

    /**
     * Common widget parameter names
     */
    const PARAM_TEMPLATE      = 'template';
    const PARAM_METADATA      = 'metadata';
    const PARAM_MODES         = 'modes';

    /**
     *  View list insertation position
     */
    const INSERT_BEFORE = 'before';
    const INSERT_AFTER  = 'after';
    const REPLACE       = 'replace';

    /**
     * Favicon resource short path
     */
    const FAVICON = 'favicon.ico';

    /**
     * Views tail
     *
     * @var   array
     */
    protected static $viewsTail = array();

    /**
     * View lists (cache)
     *
     * @var \XLite\View\AView[][]
     */
    protected $viewLists = array();

    /**
     * isCloned
     *
     * @var boolean
     */
    protected $isCloned = false;

    /**
     * Runtime cache for widgets initialization
     */
    protected static $initFlags = array();

    /**
     * Return widget default template
     *
     * @return string
     */
    abstract protected function getDefaultTemplate();

    /**
     * Return list of allowed targets
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        return [];
    }

    /**
     * Return list of disallowed targets
     *
     * @return string[]
     */
    public static function getDisallowedTargets()
    {
        return [];
    }

    /**
     * Get templates tail
     *
     * @return array
     */
    public static function getTail()
    {
        return static::$viewsTail;
    }

    /**
     * Use current controller context
     *
     * @param string $name Property name
     *
     * @return mixed
     */
    public function __get($name)
    {
        $value = parent::__get($name);

        return null !== $value ? $value : \XLite::getController()->$name;
    }

    /**
     * Use current controller context
     *
     * @param string $method Method name
     * @param array  $args   Call arguments OPTIONAL
     *
     * @return mixed
     */
    public function __call($method, array $args = array())
    {
        return call_user_func_array(array(\XLite::getController(), $method), $args);
    }

    /**
     * Copy widget params
     *
     * @return void
     */
    public function __clone()
    {
        foreach ($this->getWidgetParams() as $name => $param) {
            $this->widgetParams[$name] = clone $param;
        }

        $this->isCloned = true;
    }

    /**
     * Return widget object
     *
     * @param array  $params Widget params OPTIONAL
     * @param string $class  Widget class OPTIONAL
     *
     * @return \XLite\View\AView
     */
    public function getWidget(array $params = array(), $class = null)
    {
        // Create/clone current widget
        $widget = $this->getChildWidget($class, $params);

        // Set param values
        $widget->setWidgetParams($params);

        // Initialize
        $widget->init();

        return $widget;
    }

    /**
     * Get widget by parameters
     *
     * @param array $params Parameters
     *
     * @return \XLite\View\AView
     */
    public function getWidgetByParams(array $params)
    {
        $class = null;
        if (isset($params['class'])) {
            $class = $params['class'];
            unset($params['class']);
        }

        $name = null;
        if (isset($params['name'])) {
            $name = $params['name'];
            unset($params['name']);
        }

        return $this->getWidget($params, $class, $name);
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    public function checkVisibility()
    {
        return $this->isCloned || $this->isVisible();
    }

    /**
     * Mark string value as safe so it won't be (double)escaped in templates
     *
     * @param $string
     *
     * @return object
     */
    protected function getSafeValue($string)
    {
        return $this->getRenderingContext()
            ->getTemplatingEngine()
            ->getSafeValue($string);
    }

    /**
     * Return unique-guaranteed string to be used as id attr.
     * Returns given string in case of the first call with such argument.
     * Any subsequent calls return as <string>_<prefix>
     *
     * @param string $id Given id string
     *
     * @return object
     */
    protected function getUniqueId($id)
    {
        return \XLite\Core\Layout::getInstance()->getUniqueIdFor($id);
    }

    /**
     * Check visibility, initialize and display widget or fetch it from cache.
     *
     * TODO: remove the ability to override template, use twig's {{ include }} instead.
     *
     * @param string $template Override default template OPTIONAL
     *
     * @return void
     */
    public function display($template = null)
    {
        $overrideTemplate = isset($template);
        $originalWidget = !$this->isCloned && !$overrideTemplate;

        if (
            $this->getRenderingContext()->isBuffering()
            && $this instanceof DynamicWidgetInterface
            && $originalWidget
        ) {
            /** @var AView|DynamicWidgetInterface $this */
            echo $this->getDynamicWidgetRenderer()->getWidgetPlaceholder($this);

            return;
        }

        $this->dispatchBeforeRenderEvent();

        if ($overrideTemplate || $this->checkVisibility()) {
            if ($originalWidget) {
                $this->initView();
            }

            $cacheEnabled = $this->isCacheAllowed() && $originalWidget;
            $renderedWidget = $cacheEnabled ? $this->getRenderedWidgetFromCache() : null;

            if ($renderedWidget !== null) {
                $this->displayRenderedWidget($renderedWidget);

                $this->dispatchAfterRenderEvent($template, WidgetAfterRenderEvent::STATE_VISIBLE | WidgetAfterRenderEvent::STATE_CACHED);
            } else {

                if ($cacheEnabled) {
                    $this->getRenderingContext()->startBuffering();
                }

                $this->doDisplay($template);

                if ($cacheEnabled) {
                    $renderedWidget = $this->getRenderingContext()->stopBuffering();

                    $this->storeRenderedWidgetInCache($renderedWidget);

                    $this->displayRenderedWidget($renderedWidget);
                }

                $this->dispatchAfterRenderEvent($template, WidgetAfterRenderEvent::STATE_VISIBLE);
            }

        } else {
            $this->dispatchAfterRenderEvent($template);
        }
    }

    /**
     * Display widget with the default or overriden template.
     *
     * @param $template
     */
    protected function doDisplay($template = null)
    {
        $templateName = $this->getTemplateName($template);

        $engine = $this->getRenderingContext()->getTemplatingEngine();

        $templatePath = $engine->getTemplatePath($templateName);

        if ($templatePath !== false) {
            // Collect the specific data to send it to the finalizeTemplateDisplay method
            $profilerData = $this->prepareTemplateDisplay($templatePath);

            static::$viewsTail[] = $templatePath;

            $engine->display($templateName, $this);

            array_pop(static::$viewsTail);

            $this->finalizeTemplateDisplay($templatePath, $profilerData);

        } else if ($this->getDefaultTemplate() !== null) {
            \XLite\Logger::getInstance()->log(
                sprintf(
                    'Empty compiled template. View class: %s, view main template: %s',
                    get_class($this),
                    $this->getTemplate()
                ),
                \LOG_DEBUG
            );
        }
    }

    /**
     * Return viewer output
     *
     * @return string
     */
    public function getContent()
    {
        ob_start();
        $this->display();
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    /**
     * Check for current target
     *
     * @param array $targets List of allowed targets
     *
     * @return boolean
     */
    public static function isDisplayRequired(array $targets)
    {
        return in_array(\XLite\Core\Request::getInstance()->target, $targets, true);
    }

    /**
     * Check for current target
     *
     * @param array $targets List of disallowed targets
     *
     * @return boolean
     */
    public static function isDisplayRestricted(array $targets)
    {
        return in_array(\XLite\Core\Request::getInstance()->target, $targets, true);
    }

    /**
     * Check for current mode
     * @todo: must be static
     *
     * @param array $modes List of allowed modes
     *
     * @return boolean
     */
    public function isDisplayRequiredForMode(array $modes)
    {
        return in_array(\XLite\Core\Request::getInstance()->mode, $modes, true);
    }

    /**
     * Get current language
     *
     * @return \XLite\Model\Language
     */
    public function getCurrentLanguage()
    {
        return \XLite\Core\Session::getInstance()->getLanguage();
    }

    /**
     * FIXME - backward compatibility
     *
     * @param string $name Property name
     *
     * @return mixed
     */
    public function get($name)
    {
        $value = parent::get($name);

        return null !== $value ? $value : \XLite::getController()->get($name);
    }

    /**
     * Return instance of the child widget
     *
     * @param string $class  Child widget class OPTIONAL
     * @param array  $params Params OPTIONAL
     *
     * @return \XLite\View\AView
     */
    public function getChildWidget($class = null, array $params = array())
    {
        if (null !== $class) {
            /** @var AView $widget */
            $widget = new $class($params);

            $widget->setRenderingContext($this->getRenderingContext());
        } else {
            $widget = clone $this;
        }

        return $widget;
    }

    /**
     * Display rendered widget (html and assets).
     *
     * Dynamic widget placeholders are replaced with actual widget content when widget buffering level is zero.
     *
     * @param RenderedWidget $widget
     */
    protected function displayRenderedWidget(RenderedWidget $widget)
    {
        $renderingContext = $this->getRenderingContext();

        foreach ($widget->assets as $assets) {
            $renderingContext->registerAssets($assets);
        }

        $renderingContext->registerMetaTags($widget->metaTags);

        echo !$renderingContext->isBuffering()
            ? $this->getDynamicWidgetRenderer()->reifyWidgetPlaceholders($this, $widget->content)
            : $widget->content;
    }

    /**
     * Return current template
     *
     * @return string
     */
    protected function getTemplate()
    {
        return $this->getParam(self::PARAM_TEMPLATE);
    }

    /**
     * Get template name (aka short path)
     *
     * @param string $template Template file name OPTIONAL
     *
     * @return string
     */
    protected function getTemplateName($template = null)
    {
        $template = $template ?: $this->getTemplate();

        if (!$this->checkLicense()) {
            $template = $this->showLicenseMessage()
                ? $this->getLicenseMessageTemplate()
                : '';
        }

        return $template;
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
        return array();
    }

    /**
     * Finalize template display
     *
     * @param string $template     Template short path
     * @param array  $profilerData Profiler data
     *
     * @return void
     */
    protected function finalizeTemplateDisplay($template, array $profilerData)
    {
        if (!$this->isCloned && null === $template) {
            $this->closeView();
        }
    }

    /**
     * Return list of the modes allowed by default
     *
     * @return array
     */
    protected function getDefaultModes()
    {
        return array();
    }

    /**
     * Return favicon resource path
     *
     * @return string
     */
    protected function getFavicon()
    {
        return \XLite\Core\Layout::getInstance()->getResourceWebPath(
            static::FAVICON,
            \XLite\Core\Layout::WEB_PATH_OUTPUT_URL
        );
    }

    /**
     * Flag if the favicon is displayed in the customer area
     * By default the favicon is not displayed
     *
     * @return boolean
     */
    protected function displayFavicon()
    {
        return false;
    }

    /**
     * Define widget parameters
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_TEMPLATE => new \XLite\Model\WidgetParam\TypeFile('Template', $this->getDefaultTemplate()),
            self::PARAM_METADATA => new \XLite\Model\WidgetParam\TypeCollection('Widget metadata', array()),
            self::PARAM_MODES    => new \XLite\Model\WidgetParam\TypeCollection('Modes', $this->getDefaultModes()),
        );
    }

    /**
     * Check visibility according to the current target
     * todo: must be static
     * todo: move to public section
     *
     * @return boolean
     */
    public static function checkTarget()
    {
        $targets = static::getAllowedTargets();

        return (!((bool)$targets) || static::isDisplayRequired($targets)) && !static::isDisplayRestricted(static::getDisallowedTargets());
    }

    /**
     * Check if current mode is allowable
     *
     * @return boolean
     */
    protected function checkMode()
    {
        $modes = $this->getParam(self::PARAM_MODES);

        return empty($modes) || $this->isDisplayRequiredForMode($modes);
    }

    /**
     * Called before the includeCompiledFile()
     *
     * @return void
     */
    protected function initView()
    {
        $cachekey = get_class($this);
        if (!isset(static::$initFlags[$cachekey])) {
            // Add widget resources to the static array
            $this->registerResourcesForCurrentWidget();
            static::$initFlags[$cachekey] = true;
        }
    }

    /**
     * Called after the includeCompiledFile()
     *
     * @return void
     */
    protected function closeView()
    {
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return static::checkTarget()
            && $this->checkMode()
            && $this->checkACL()
            && ($this->checkLicense() || $this->showLicenseMessage());
    }

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    protected function checkACL()
    {
        return true;
    }

    /**
     * Check if current page is accessible for current x-cart license
     *
     * @return boolean
     */
    protected function checkLicense()
    {
        return true;
    }

    /**
     * Show license message
     * @todo: rename to isShowLicenseMessage
     *
     * @return boolean
     */
    protected function showLicenseMessage()
    {
        return false;
    }

    /**
     * Returns license message template
     *
     * @return string
     */
    protected function getLicenseMessageTemplate()
    {
        return 'license_message.twig';
    }

    /**
     * Returns license message
     *
     * @return string
     */
    protected function getLicenseMessage()
    {
        return '';
    }

    /**
     * FIXME - must be removed
     *
     * @param string $name Param name
     *
     * @return mixed
     */
    protected function getRequestParamValue($name)
    {
        return \XLite\Core\Request::getInstance()->$name;
    }

    // {{{ Resources (CSS and JS)

    /**
     * Get list of methods, priorities and interfaces for the resources
     *
     * @return array
     */
    protected static function getResourcesSchema()
    {
        return array(
            array('getCommonFiles', 100, \XLite::COMMON_INTERFACE),
            array('getResources',   200, null),
            array('getThemeFiles',  300, null),
        );
    }

    /**
     * Via this method the widget registers the CSS files which it uses.
     * During the viewers initialization the CSS files are collecting into the static storage.
     *
     * The method must return the array of the CSS file paths:
     *
     * return array(
     *      'modules/Developer/Module/style.css',
     *      'styles/css/main.css',
     * );
     *
     * Also the best practice is to use parent result:
     *
     * return array_merge(
     *      parent::getCSSFiles(),
     *      array(
     *          'modules/Developer/Module/style.css',
     *          'styles/css/main.css',
     *          ...
     *      )
     * );
     *
     * LESS resource usage:
     * You can also use the less resources along with the CSS ones.
     * The LESS resources will be compiled into CSS.
     * However you can merge your LESS resource with another one using 'merge' parameter.
     * 'merge' parameter must contain the file path to the parent LESS file.
     * In this case the resources will be linked into one LESS file with the '@import' LESS instruction.
     *
     * !Important note!
     * Right now only one parent is supported, so you cannot link the resources in LESS chain.
     *
     * You shouldn't add the widget as a list child of 'body' because it won't have its CSS resources loaded that way.
     * Use 'layout.main' or 'layout.footer' instead.
     *
     * The best practice is to merge LESS resources with 'bootstrap/css/bootstrap.less' file
     *
     * @return array
     */
    public function getCSSFiles()
    {
        return array(
            array(
                'file'  => 'css/style.less',
                'media' => 'screen',
                // We use the bootstrap LESS instructions
                'merge' => 'bootstrap/css/bootstrap.less',
            ),
        );
    }

    /**
     * Via this method the widget registers the JS files which it uses.
     * During the viewers initialization the JS files are collecting into the static storage.
     *
     * The method must return the array of the JS file paths:
     *
     * return array(
     *      'modules/Developer/Module/script.js',
     *      'script/js/main.js',
     * );
     *
     * Also the best practice is to use parent result:
     *
     * return array_merge(
     *      parent::getJSFiles(),
     *      array(
     *          'modules/Developer/Module/script.js',
     *          'script/js/main.js',
     *          ...
     *      )
     * );
     *
     * You shouldn't add the widget as a list child of 'body' because it won't have its JS resources loaded that way.
     * Use 'layout.main' or 'layout.footer' instead.
     *
     * @return array
     */
    public function getJSFiles()
    {
        return array();
    }

    /**
     * Via this method the widget registers the meta tags which it uses.
     * During the viewers initialization the meta tags are collecting into the static storage.
     *
     * The method must return the array of the full meta tag definitions:
     *
     * return array(
     *      '<meta name="name1" content="Content1" />',
     *      '<meta http-equiv="Content-Style-Type" content="text/css">',
     * );
     *
     * Also the best practice is to use parent result:
     *
     * return array_merge(
     *      parent::getMetaTags(),
     *      array(
     *          '<meta name="name1" content="Content1" />',
     *          '<meta http-equiv="Content-Style-Type" content="text/css">',
     *          ...
     *      )
     * );
     *
     * @return array
     */
    public function getMetaTags()
    {
        return array();
    }

    /**
     * Flag indicating that AView's common files were already registered to avoid iterating over and over again
     * Much more clean way is to move the whole resource declaration into the concrete widget class (representing a root of the page), but it will break BC
     *
     * @var bool
     */
    private static $returnedAViewsCommonFiles = false;

    /**
     * Register files from common repository
     *
     * @return array
     */
    protected function getCommonFiles()
    {
        if (self::$returnedAViewsCommonFiles) {
            return array();
        }

        self::$returnedAViewsCommonFiles = true;

        return array(
            static::RESOURCE_JS => array(),
            static::RESOURCE_CSS => array(),
        );
    }

    /**
     * Flag indicating that AView's theme files were already registered to avoid iterating over and over again
     * Much more clean way is to move the whole resource declaration into the concrete widget class (representing a root of the page), but it will break BC
     *
     * @var bool
     */
    private static $returnedAViewsThemeFiles = false;

    /**
     * Return theme common files
     *
     * @param boolean|null $adminZone
     *
     * @return array
     */
    protected function getThemeFiles($adminZone = null)
    {
        if (self::$returnedAViewsThemeFiles) {
            return array();
        }

        self::$returnedAViewsThemeFiles = true;

        return array(
            static::RESOURCE_JS => array(),
            static::RESOURCE_CSS => array(),
        );
    }

    /**
     * Return list of widget resources
     *
     * @return array
     */
    protected function getResources()
    {
        return array(
            static::RESOURCE_JS   => $this->getJSFiles(),
            static::RESOURCE_CSS  => $this->getCSSFiles(),
        );
    }

    /**
     * Returns reference to this object
     *
     * @return string
     */
    public function getHashCode()
    {
        return get_class($this);
    }

    /**
     * Return resource structure for validation engine language file.
     * By default there are several ready-to-use language files from validationEngine project.
     * The translation module is able to use its own language validation file.
     * It should decorate this method for this case.
     *
     * @return array
     */
    protected function getValidationEngineLanguageResource()
    {
        return array(
            'file' => 'js/validationEngine.min/languages/jquery.validationEngine-LANGUAGE_CODE.js',
            'filelist' => array(
                $this->getValidationEngineLanguageFile(),
                'js/validationEngine.min/languages/jquery.validationEngine-en.js',
            ),
            'no_minify' => true,
        );
    }

    /**
     * Return validation engine language file path.
     * By default there are several ready-to-use language files from validationEngine project.
     * The translation module is able to use its own language validation file.
     * It should decorate this method for this case.
     *
     * @return string
     */
    protected function getValidationEngineLanguageFile()
    {
        return 'js/validationEngine.min/languages/jquery.validationEngine-'
            . $this->getCurrentLanguage()->getCode()
            . '.js';
    }

    /**
     * Register widget resources
     *
     * @return void
     */
    protected function registerResourcesForCurrentWidget()
    {
        foreach (static::getResourcesSchema() as $data) {
            list($method, $index, $interface) = $data;

            $this->registerResources($this->$method(), $index, $interface, $method);
        }

        $this->registerMetas();
    }

    /**
     * This method collects the JS/CSS resources which are registered by various widgets via
     * methods registered in \XLite\View\AView::getResourcesSchema:
     *
     * getCommonFiles()
     * getResources() (this is a compilation of getJSFiles() / getCSSFiles() methods)
     * getThemeFiles()
     *
     * Every widget to display registers the resources which are collected in the \XLite\Core\Layout static storage.
     * Then these resources are prepared in this method and are ready to use in \XLite\View\AResourcesContainer class.
     * Container class just gets these resources and puts them into the page as a script or CSS files inclusions.
     *
     * This method takes the $resources parameter in the following format:
     * array(
     *  static::RESOURCE_JS => array(
     *      'js_file_path1',
     *      'js_file_path2',
     *      ...
     *  ),
     *  static::RESOURCE_CSS => array(
     *      'css_file_path1',
     *      'css_file_path2',
     *      ...
     *  ),
     * )
     *
     * Note: You can provide more details for the resource if the resource array is provided
     * instead of file path ('js_file_path1'):
     *
     * array(
     *      'file'  => 'resource_file_path',
     *      'media' => 'print'  // for example
     *      'filelist' => array(          // If you use this parameter then the 'file' parameter
     *                                    // is taken as a 'resource name',
     *          'file1_path(real_path)',  // and the real file paths must be provided via 'filelist' parameter
     *      )
     * )
     *
     * $index - parameter is an order_by number which helps to insert the resources into some ordered queue
     *
     * $interface - parameter to inform where the resources are placed.
     *
     * @param array   $resources List of resources to register
     * @param integer $index     Position in the ordered resources queue
     * @param string  $interface Interface where the resources are placed OPTIONAL
     * @param \XLite\View\AView  $owner Mark resources with this object OPTIONAL
     *
     * @return void
     *
     * @see \XLite\View\AView::registerResourcesForCurrentWidget()
     * @see \XLite\View\AView::initView()
     */
    protected function registerResources(array $resources, $index, $interface = null, $owner = null)
    {
        $this->getRenderingContext()->registerAssets(new Assets($resources, $index, $interface, $owner));
    }

    /**
     * Method collects the meta definitions (meta tags) into the static meta storage. (the Customer interface only!)
     * Widgets can register the meta they are using via 'getMetaTags()'
     *
     * @return void
     *
     * @see \XLite\View\AView::getMetaTags()
     */
    protected function registerMetas()
    {
        $this->getRenderingContext()->registerMetaTags($this->getMetaTags());
    }

    // }}}

    // {{{ View lists

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
        foreach ($this->getViewList($list, $arguments) as $widget) {
            $widget->display();
        }
    }

    /**
     * Display a nested view list
     *
     * @param string $part   Suffix that should be appended to the name of a parent list (will be delimited with a dot)
     * @param array  $params Widget params OPTIONAL
     *
     * @return void
     */
    public function displayNestedViewListContent($part, array $params = array())
    {
        $this->displayViewListContent($this->getNestedListName($part), $params);
    }

    /**
     * Display a inherited view list
     *
     * @param string $part   Suffix that should be appended to the name of a inherited list
     *                       (will be delimited with a dot)
     * @param array  $params Widget params OPTIONAL
     *
     * @return void
     */
    public function displayInheritedViewListContent($part, array $params = array())
    {
        $this->displayViewListContent($this->getInheritedListName($part), $params);
    }

    /**
     * Combines the nested list name from the parent list name and a suffix
     *
     * @param string $part Suffix to be added to the parent list name
     *
     * @return string
     */
    protected function getNestedListName($part)
    {
        return $this->viewListName ? $this->viewListName . '.' . $part : $part;
    }

    /**
     * Get a nested view list
     *
     * @param string $part      Suffix of the nested list name
     * @param array  $arguments List common arguments OPTIONAL
     *
     * @return array
     */
    protected function getNestedViewList($part, array $arguments = array())
    {
        return $this->getViewList($this->getNestedListName($part), $arguments);
    }

    /**
     * Combines the inherited list name from the parent list name and a suffix
     *
     * @param string $part Suffix to be added to the inherited list name
     *
     * @return string
     */
    protected function getInheritedListName($part)
    {
        return $this->getListName() ? $this->getListName() . '.' . $part : $part;
    }

    /**
     * Get a inherited view list
     *
     * @param string $part      Suffix of the inherited list name
     * @param array  $arguments List common arguments OPTIONAL
     *
     * @return array
     */
    protected function getInheritedViewList($part, array $arguments = array())
    {
        return $this->getViewList($this->getInheritedListName($part), $arguments);
    }

    // }}}

    /**
     * Display plain array as JS array
     *
     * @param array $data Plain array
     *
     * @return void
     */
    public function displayCommentedData(array $data)
    {
        if (!empty($data)) {
            echo ('<script type="text/x-cart-data">' . "\r\n" . json_encode($data) . "\r\n" . '</script>' . "\r\n");
        }
    }

    /**
     * Format price
     *
     * @param float                 $value        Price
     * @param \XLite\Model\Currency $currency     Currency OPTIONAL
     * @param boolean               $strictFormat Flag if the price format is strict (trailing zeroes and so on options)
     *
     * @return string
     */
    public static function formatPrice($value, \XLite\Model\Currency $currency = null, $strictFormat = false)
    {
        if (null === $currency) {
            $currency = \XLite::getInstance()->getCurrency();
        }

        $parts = $currency->formatParts($value);

        if (isset($parts['sign']) && '-' === $parts['sign']) {
            $parts['sign'] = '− ';
        }

        if ($strictFormat) {
            $parts = static::formatPartsStrictly($parts);
        }

        return implode('', $parts);
    }

    /**
     * Format weight
     *
     * @param float $value Weight
     *
     * @return string
     */
    public static function formatWeight($value)
    {
        list($thousandDelimiter, $decimalDelimiter)
            = explode('|', \XLite\Core\Config::getInstance()->Units->weight_format);

        $result = number_format($value, 4, $decimalDelimiter, $thousandDelimiter);

        if (\XLite\Core\Config::getInstance()->Units->weight_trailing_zeroes) {
            $result = rtrim(rtrim($result, '0'), $decimalDelimiter);
        }

        return $result . ' ' . \XLite\Core\Translation::translateWeightSymbol();
    }

    /**
     * Format price as HTML block
     *
     * @param float                 $value    Value
     * @param \XLite\Model\Currency $currency Currency OPTIONAL
     * @param boolean               $strictFormat Flag if the price format is strict (trailing zeroes and so on options)
     *
     * @return string
     */
    public function formatPriceHTML($value, \XLite\Model\Currency $currency = null, $strictFormat = false)
    {
        if (null === $currency) {
            $currency = \XLite::getInstance()->getCurrency();
        }

        $parts = $currency->formatParts($value);

        if (isset($parts['sign']) && '-' === $parts['sign']) {
            $parts['sign'] = '&minus;&#8197;';
        }

        if ($strictFormat) {
            $parts = static::formatPartsStrictly($parts);
        }

        foreach ($parts as $name => $value) {
            $class = 'part-' . $name;
            $parts[$name] = '<span class="' . $class . '">' . func_htmlspecialchars($value) . '</span>';
        }

        return implode('', $parts);
    }

    /**
     * Print tag attributes
     *
     * @param array $attributes Attributes
     *
     * @return string
     */
    protected function printTagAttributes(array $attributes)
    {
        $pairs = array();
        foreach ($attributes as $name => $value) {
            if (is_array($value)) {
                $value = implode(' ', $value);
            }

            $pairs[] = func_htmlspecialchars(strtolower($name)) . '="' . func_htmlspecialchars(trim($value)) . '"';
        }

        return implode(' ', $pairs);
    }
    /**
     * Check - view list is visible or not
     *
     * @param string $list      List name
     * @param array  $arguments List common arguments OPTIONAL
     *
     * @return boolean
     */
    public function isViewListVisible($list, array $arguments = array())
    {
        return 0 < count($this->getViewList($list, $arguments));
    }

    /**
     * Format file size
     *
     * @param integer $size Size in bytes
     *
     * @return string
     */
    protected function formatSize($size)
    {
        if (1024 > $size) {
            $result = static::t('X bytes', array('value' => $size));

        } elseif (1048576 > $size) {
            $result = static::t('X kB', array('value' => round($size / 1024, 1)));

        } elseif (1073741824 > $size) {
            $result = static::t('X MB', array('value' => round($size / 1048576, 1)));

        } else {
            $result = static::t('X GB', array('value' => round($size / 1073741824, 1)));

        }

        return $result;
    }

    /**
     * Return specific CSS class for dialog wrapper
     *
     * @return string
     */
    protected function getDialogCSSClass()
    {
        return 'dialog-content';
    }

    /**
     * Change parts of format price if it is necessary
     *
     * @param array $parts
     *
     * @return array
     */
    protected static function formatPartsStrictly($parts)
    {
        if (1 == \XLite\Core\Config::getInstance()->General->trailing_zeroes
            && '00' == $parts['decimal']
        ) {
            unset($parts['decimal'], $parts['decimalDelimiter']);
        }

        return $parts;
    }

    /**
     * Build list item class
     *
     * @param string $listName List name
     *
     * @return string
     */
    protected function buildListItemClass($listName)
    {
        $indexName = $listName . 'ArrayPointer';
        $countName = $listName . 'ArraySize';

        $class = array();

        if (1 == $this->$indexName) {
            $class[] = 'first';
        }

        if ($this->$countName == $this->$indexName) {
            $class[] = 'last';
        }

        return implode(' ', $class);
    }

    /**
     * Prepare human-readable output for file size
     * @todo: add twig filter
     *
     * @param integer $size Size in bytes
     *
     * @return string
     */
    protected function formatFileSize($size)
    {
        return \XLite\Core\Converter::formatFileSize($size);
    }

    /**
     * Compares two values
     * @deprecated Used only in deprecated class (\XLite\View\MembershipSelect)
     *
     * @param mixed $val1 Value 1
     * @param mixed $val2 Value 2
     * @param mixed $val3 Value 3 OPTIONAL
     *
     * @return boolean
     */
    protected function isSelected($val1, $val2, $val3 = null)
    {
        if (null !== $val1 && null !== $val3) {
            $method = 'get';

            if ($val1 instanceof \XLite\Model\AEntity) {
                $method .= \Includes\Utils\Converter::convertToPascalCase($val2);
            }

            // Get value with get() method and compare it with third value
            $result = $val1->$method() == $val3;

        } else {
            $result = $val1 == $val2;
        }

        return $result;
    }

    /**
     * Helper to get array field values
     *
     * @param array  $array Array to get field value
     * @param string $field Field name
     *
     * @return mixed
     */
    protected function getArrayField(array $array, $field)
    {
        return \Includes\Utils\ArrayManager::getIndex($array, $field, true);
    }

    /**
     * Helper to get object field values
     *
     * @param object  $object   Object to get field value
     * @param string  $field    Field name
     * @param boolean $isGetter Flag OPTIONAL
     *
     * @return mixed
     */
    protected function getObjectField($object, $field, $isGetter = true)
    {
        return \Includes\Utils\ArrayManager::getObjectField($object, $field, $isGetter);
    }

    /**
     * Truncates the baseObject property value to specified length
     * @todo: add twig filter
     *
     * @param mixed   $base       String or object instance to get field value from
     * @param mixed   $field      String length or field to get value
     * @param integer $length     Field length to truncate to OPTIONAL
     * @param string  $etc        String to add to truncated field value OPTIONAL
     * @param mixed   $breakWords Word wrap flag OPTIONAL
     *
     * @return string
     */
    protected function truncate($base, $field, $length = 0, $etc = '...', $breakWords = false)
    {
        if (is_scalar($base)) {
            $string = $base;
            $length = $field;

        } else {
            if ($base instanceof \XLite\Model\AEntity) {
                $string = $base->{'get' . \XLite\Core\Converter::convertToCamelCase($field)}();
            } else {
                $string = $base->get($field);
            }
        }

        if (0 == $length) {
            $string = '';

        } elseif (strlen($string) > $length) {
            $length -= strlen($etc);
            if (!$breakWords) {
                $string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length + 1));
            }

            $string = substr($string, 0, $length) . $etc;
        }

        return $string;
    }

    /**
     * Format date
     * @todo: add twig filter
     *
     * @param mixed  $base   String or object instance to get field value from
     * @param string $field  Field to get value OPTIONAL
     * @param string $format Date format OPTIONAL
     *
     * @return string
     */
    protected function formatDate($base, $field = null, $format = null)
    {
        if (is_object($base)) {
            $base = $base instanceof \XLite\Model\AEntity
                ? $base->$field
                : $base->get($field);
        }

        return \XLite\Core\Converter::formatDate($base, $format);
    }

    /**
     * Format timestamp
     * @todo: add twig filter
     *
     * @param mixed  $base   String or object instance to get field value from
     * @param string $field  Field to get value OPTIONAL
     * @param string $format Date format OPTIONAL
     *
     * @return string
     */
    protected function formatTime($base, $field = null, $format = null)
    {
        if (is_object($base)) {
            $base = $base instanceof \XLite\Model\AEntity
                ? $base->$field
                : $base->get($field);
        }

        return \XLite\Core\Converter::formatTime($base, $format);
    }

    /**
     * Format timestamp as day time
     * @todo: add twig filter
     *
     * @param mixed  $base   String or object instance to get field value from
     * @param string $field  Field to get value OPTIONAL
     * @param string $format Time format OPTIONAL
     *
     * @return string
     */
    protected function formatDayTime($base, $field = null, $format = null)
    {
        if (is_object($base)) {
            $base = $base instanceof \XLite\Model\AEntity
                ? $base->$field
                : $base->get($field);
        }

        return \XLite\Core\Converter::formatDayTime($base, $format);
    }

    /**
     * Add slashes
     * @todo: add twig filter
     *
     * @param mixed  $base  String or object instance to get field value from
     * @param string $field Field to get value OPTIONAL
     *
     * @return string
     */
    protected function addSlashes($base, $field = null)
    {
        return addslashes(is_scalar($base) ? $base : $base->get($field));
    }

    /**
     * Check if data is empty
     *
     * @param mixed $data Data to check
     *
     * @return boolean
     */
    protected function isEmpty($data)
    {
        return empty($data);
    }

    /**
     * Split an array into chunks
     *
     * @param array   $array Array to split
     * @param integer $count Chunks count
     *
     * @return array
     */
    protected function split(array $array, $count)
    {
        $result = array_chunk($array, $count);

        $lastKey   = count($result) - 1;
        $lastValue = $result[$lastKey];

        $count -= count($lastValue);

        if (0 < $count) {
            $result[$lastKey] = array_merge($lastValue, array_fill(0, $count, null));
        }

        return $result;
    }

    /**
     * Increment
     *
     * @param integer $value Value to increment
     * @param integer $inc   Increment OPTIONAL
     *
     * @return integer
     */
    protected function inc($value, $inc = 1)
    {
        return $value + $inc;
    }

    /**
     * For the "zebra" tables
     *
     * @param integer $row          Row index
     * @param string  $oddCSSClass  First CSS class
     * @param string  $evenCSSClass Second CSS class OPTIONAL
     *
     * @return string
     */
    protected function getRowClass($row, $oddCSSClass, $evenCSSClass = null)
    {
        return 0 === ($row % 2) ? $oddCSSClass : $evenCSSClass;
    }

    /**
     * Get view list
     *
     * @param string $list      List name
     * @param array  $arguments List common arguments OPTIONAL
     *
     * @return array
     */
    protected function getViewList($list, array $arguments = array())
    {
        if (!isset($this->viewLists[$list])) {
            $this->viewLists[$list] = $this->defineViewList($list);
        }

        if (!empty($arguments)) {
            foreach ($this->viewLists[$list] as $widget) {
                $widget->setWidgetParams($arguments);
            }
        }

        $result = array();
        foreach ($this->viewLists[$list] as $widget) {
            if ($widget->checkVisibility() || $widget instanceof DynamicWidgetInterface) {
                $result[] = $widget;
            }
        }

        return $result;
    }

    /**
     * getViewListChildren
     *
     * @param string $list List name
     *
     * @return array
     */
    protected function getViewListChildren($list)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\ViewList')->findClassList(
            $list,
            static::detectCurrentViewZone()
        );
    }

    /**
     * Detect current view zone
     *
     * @return string
     */
    protected static function detectCurrentViewZone()
    {
        if (\XLite\Core\Layout::getInstance()->getInterface() === \XLite::MAIL_INTERFACE) {
            $zone = \XLite\Model\ViewList::INTERFACE_MAIL;

        } elseif (\XLite\Core\Layout::getInstance()->getInterface() === \XLite::PDF_INTERFACE) {
            $zone = \XLite\Model\ViewList::INTERFACE_PDF;

        } elseif (\XLite\Core\Layout::getInstance()->getInterface() === \XLite::CONSOLE_INTERFACE) {
            $zone = \XLite\Model\ViewList::INTERFACE_CONSOLE;

        } elseif (\XLite\Core\Layout::getInstance()->getInterface() === \XLite::ADMIN_INTERFACE) {
            $zone = \XLite\Model\ViewList::INTERFACE_ADMIN;

        } else {
            $zone = \XLite\Model\ViewList::INTERFACE_CUSTOMER;
        }

        return $zone;
    }

    /**
     * addViewListChild
     *
     * @param array   &$list      List to modify
     * @param array   $properties Node properties
     * @param integer $weight     Node position OPTIONAL
     *
     * @return void
     */
    protected function addViewListChild(array &$list, array $properties, $weight = 0)
    {
        // Search node to insert after
        foreach ($list as $key => $node) {
            if ($node->getWeight() > $weight) {
                break;
            }
        }

        // Prepare properties
        $properties['tpl']    = substr(
            \XLite\Singletons::$handler->layout->getResourceFullPath($properties['tpl']),
            strlen(LC_DIR_SKINS)
        );
        $properties['weight'] = $weight;
        $properties['list']   = $node->getList();

        // Add element to the array
        array_splice($list, $key, 0, array(new \XLite\Model\ViewList($properties)));
    }

    /**
     * Define view list
     *
     * @param string $list List name
     *
     * @return array
     */
    protected function defineViewList($list)
    {
        $widgets = array();

        foreach ($this->getViewListChildren($list) as $widget) {
            /** @var \XLite\View\AView $widgetClass */
            $widgetClass = $widget->getChild();
            $metadata = $this->getListItemMetadata($widget);

            if ($widgetClass && $widgetClass::checkTarget()) {
                // List child is widget
                $widgets[] = $this->getWidget(
                    array(
                        'viewListClass' => $this->getViewListClass(),
                        'viewListName'  => $list,
                        'metadata'      => $metadata,
                    ),
                    $widget->getChild()
                );

            } elseif ($widget->getTpl()) {
                // List child is template
                $widgets[] = $this->getWidget(
                    array(
                        'viewListClass' => $this->getViewListClass(),
                        'viewListName'  => $list,
                        'metadata'      => $metadata,
                        'template'      => $widget->getTpl(),
                    )
                );
            }
        }

        return $widgets;
    }

    /**
     * Define view list item metadata
     *
     * @param \XLite\Model\ViewList $item ViewList item
     *
     * @return array
     */
    protected function getListItemMetadata($item)
    {
        return array();
    }

    /**
     * Get view list class name
     *
     * @return string
     */
    protected function getViewListClass()
    {
        return get_class($this);
    }

    /**
     * Get XPath by content
     * @todo: remove -> used only in \XLite\View\AView::insertViewListByXpath()
     *
     * @param string $content Content
     *
     * @return \DOMXPath
     */
    protected function getXpathByContent($content)
    {
        $dom = new \DOMDocument();
        $dom->formatOutput = true;

        return @$dom->loadHTML($content) ? new \DOMXPath($dom) : null;
    }

    /**
     * Get view list content
     *
     * @param string $list      List name
     * @param array  $arguments List common arguments OPTIONAL
     *
     * @return string
     */
    protected function getViewListContent($list, array $arguments = array())
    {
        ob_start();
        $this->displayViewListContent($list, $arguments);
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    /**
     * Get view list content as nodes list
     * @todo: remove -> used only in \XLite\View\AView::insertViewListByXpath()
     *
     * @param string $list List name
     *
     * @return \DOMNamedNodeMap|void
     */
    protected function getViewListContentAsNodes($list)
    {
        $d = new \DOMDocument();
        $content = $this->getViewListContent($list);
        $result = null;
        if ($content && @$d->loadHTML($content)) {
            $result = $d->documentElement->childNodes->item(0)->childNodes;
        }

        return $result;
    }

    /**
     * Insert view list by XPath query
     * @todo: check for usage
     *
     * @param string $content        Content
     * @param string $query          XPath query
     * @param string $list           List name
     * @param string $insertPosition Insert position code OPTIONAL
     *
     * @return string
     */
    protected function insertViewListByXpath($content, $query, $list, $insertPosition = self::INSERT_BEFORE)
    {
        $xpath = $this->getXpathByContent($content);
        if ($xpath) {
            $places = $xpath->query($query);
            $patches = $this->getViewListContentAsNodes($list);
            if (0 < $places->length && $patches && 0 < $patches->length) {
                $this->applyXpathPatches($places, $patches, $insertPosition);
                $content = $xpath->document->saveHTML();
            }
        }

        return $content;
    }

    /**
     * Apply XPath-based patches
     * @todo: remove -> used only in \XLite\View\AView::insertViewListByXpath() (also in Flexy compiler)
     *
     * @param \DOMNamedNodeMap $places         Patch placeholders
     * @param \DOMNamedNodeMap $patches        Patches
     * @param string           $baseInsertType Patch insert type
     *
     * @return void
     */
    protected function applyXpathPatches(\DOMNamedNodeMap $places, \DOMNamedNodeMap $patches, $baseInsertType)
    {
        foreach ($places as $place) {
            $insertType = $baseInsertType;
            foreach ($patches as $node) {
                $node = $node->cloneNode(true);

                if (static::INSERT_BEFORE === $insertType) {
                    // Insert patch node before XPath result node
                    $place->parentNode->insertBefore($node, $place);

                } elseif (static::INSERT_AFTER === $insertType) {
                    // Insert patch node after XPath result node
                    if ($place->nextSibling) {
                        $place->parentNode->insertBefore($node, $place->nextSibling);
                        $insertType = self::INSERT_BEFORE;
                        $place = $place->nextSibling;

                    } else {
                        $place->parentNode->appendChild($node);
                    }

                } elseif (static::REPLACE === $insertType) {
                    // Replace XPath result node to patch node
                    $place->parentNode->replaceChild($node, $place);

                    if ($node->nextSibling) {
                        $place = $node->nextSibling;
                        $insertType = self::INSERT_BEFORE;

                    } else {
                        $place = $node;
                        $insertType = self::INSERT_AFTER;
                    }
                }
            }
        }
    }

    /**
     * Insert view list by regular expression pattern
     * @todo: check for usage
     *
     * @param string $content Content
     * @param string $pattern Pattern (PCRE)
     * @param string $list    List name
     * @param string $replace Replace pattern OPTIONAL
     *
     * @return string
     */
    protected function insertViewListByPattern($content, $pattern, $list, $replace = '%s')
    {
        return preg_replace(
            $pattern,
            sprintf($replace, $this->getViewListContent($list)),
            $content
        );
    }

    /**
     * Return internal list name
     *
     * @return string
     */
    protected function getListName()
    {
        return null;
    }

    /**
     * getNamePostedData
     *
     * @param string  $field Field name
     * @param integer $id    Model object ID OPTIONAL
     *
     * @return string
     */
    protected function getNamePostedData($field, $id = null)
    {
        $args  = func_get_args();
        $field = $args[0];
        $tail  = '';

        if (2 <= count($args)) {
            $id = $args[1];
        }

        if (2 < count($args)) {
            $tail = '[' . implode('][', array_slice($args, 2)) . ']';
        }

        return $this->getPrefixPostedData() . (null !== $id ? '[' . $id . ']' : '') . '[' . $field . ']' . $tail;
    }

    /**
     * getNameToDelete
     *
     * @param integer $id Model object ID
     *
     * @return string
     */
    protected function getNameToDelete($id)
    {
        return $this->getPrefixSelected() . '[' . $id . ']';
    }

    /**
     * Checks if specific developer mode is defined
     *
     * @return boolean
     */
    protected function isDeveloperMode()
    {
        return LC_DEVELOPER_MODE;
    }

    /**
     * @return string
     */
    public function getAjaxPrefix()
    {
        $result  = '';

        if (LC_USE_CLEAN_URLS
            && \XLite\Core\Router::getInstance()->isUseLanguageUrls()
            && !\XLite::isAdminZone()
        ) {
            $language = \XLite\Core\Session::getInstance()->getLanguage();
            if ($language && !$language->getDefaultAuth()) {
                $result = $language->getCode();
            }
        }

        return $result;
    }

    // }}}

    /**
     * Get logo
     *
     * @return string
     */
    public function getLogo()
    {
        return \XLite\Core\Layout::getInstance()->getLogo();
    }

    /**
     * Get apple icon
     *
     * @return string
     */
    public function getAppleIcon()
    {
        return \XLite\Core\Layout::getInstance()->getAppleIcon();
    }

    /**
     * Get invoice logo
     *
     * @return string
     */
    public function getInvoiceLogo()
    {
        return $this->getLogo();
    }

    /**
     * Return specific data for address entry. Helper.
     *
     * @param \XLite\Model\Address $address   Address
     * @param boolean              $showEmpty Show empty fields OPTIONAL
     *
     * @return array
     */
    protected function getAddressSectionData(\XLite\Model\Address $address, $showEmpty = false)
    {
        $result = array();
        $hasStates = $address->getCountry() ? $address->getCountry()->hasStates() : false;

        foreach (\XLite\Core\Database::getRepo('XLite\Model\AddressField')->findAllEnabled() as $field) {
            $method = 'get'
                . \Includes\Utils\Converter::convertToCamelCase(
                    $field->getViewGetterName() ?: $field->getServiceName()
                );
            $addressFieldValue = $address->{$method}();
            $cssFieldName = $field->getCSSFieldName();

            switch ($field->getServiceName()) {
                case 'state_id':
                    $addressFieldValue = $hasStates ? $addressFieldValue : null;
                    if (null === $addressFieldValue && $hasStates) {
                        $addressFieldValue = $address->getCustomState();
                    }
                    break;

                case 'custom_state':
                    $addressFieldValue = $hasStates ? null : $address->getCustomState();
                    $cssFieldName      = $hasStates ? $cssFieldName : 'address-state';
                    break;
                default:
            }

            if (strlen($addressFieldValue) || $showEmpty) {
                $result[$field->getServiceName()] = array(
                    'css_class' => $cssFieldName,
                    'title'     => $field->getName(),
                    'value'     => $addressFieldValue,
                );
            }
        }

        return $result;
    }

    /**
     * Get escaped widget parameter
     * @todo: check for usage
     *
     * @param string $name Parameters name
     *
     * @return string
     */
    protected function getEscapedParam($name)
    {
        $value = $this->getParam($name);

        return func_htmlspecialchars($value);
    }

    // {{{ SVG

    /**
     * Get SVG image
     *
     * @param string $path Path
     * @param string $interface Interface code OPTIONAL
     *
     * @return string
     */
    protected function getSVGImage($path, $interface = null)
    {
        $content = null;

        $path = \XLite\Core\Layout::getInstance()->getResourceFullPath($path, $interface);

        if ($path && file_exists($path)) {
            $content = file_get_contents($path);
            $content = preg_replace(
                array(
                    '/<\?xml [^>]+>/Ssi',
                    '/<!ENTITY [^>]+>/Ss',
                    '/<!DOCTYPE svg [^>]*>/Ssi',
                    '/<metadata>.+<\/metadata>/Ss',
                    '/xmlns:(?:x|i|graph)="[^"]+"/Ss',
                    '/>\s+</Ss',
                    '/<!--\s.+\s-->/USs',
                ),
                array('', '', '', '', '', '><', ''),
                $content
            );
            $content = trim($content);
        }

        return $content;
    }

    /**
     * Display SVG image
     * @todo: add twig filter or function
     *
     * @param string $path Path
     *
     * @return void
     */
    protected function displaySVGImage($path)
    {
        print $this->getSVGImage($path);
    }

    // }}}

    // {{{ Widget Cache todo: remove with trait

    /**
     * Get widget cache instance
     *
     * @return WidgetCache
     */
    protected function getCache()
    {
        return $this->getContainer()->get('widget_cache');
    }

    /**
     * Check cached content
     *
     * @return boolean
     */
    public function hasCachedContent()
    {
        return $this->isCacheAllowed() && $this->getCache()->has($this->getCacheParameters());
    }

    /**
     * Cache allowed
     *
     * @return boolean
     */
    protected function isCacheAllowed()
    {
        return $this->isCacheAvailable()
            && \XLite\Core\Config::getInstance()->Performance->use_view_cache;
    }

    /**
     * Cache availability
     *
     * @return boolean
     */
    protected function isCacheAvailable()
    {
        return false;
    }

    /**
     * Get cached widget html and assets.
     *
     * @return RenderedWidget rendered widget data transfer object
     */
    protected function getRenderedWidgetFromCache()
    {
        return $this->getCache()->get($this->getCacheParameters());
    }

    /**
     * Store widget html and assets in cache.
     *
     * @param RenderedWidget $widget rendered widget data transfer object
     *
     * @return void
     */
    protected function storeRenderedWidgetInCache(RenderedWidget $widget)
    {
        $this->getCache()->set($this->getCacheParameters(), $widget, $this->getCacheTTL());
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        return array(
            \Includes\Utils\URLManager::isHTTPS(),
            \XLite\Core\Session::getInstance()->getLanguage()->getCode(),
            \XLite\Core\Config::getInstance()->General->default_language,
            substr(get_called_class(), 6),
        );
    }

    /**
     * Get cache TTL (seconds)
     *
     * @return integer
     */
    protected function getCacheTTL()
    {
        return null;
    }

    /**
     * Execute callable and cache the return value (or retrieve the value from cache).
     * Caching policy matches the current widget's caching policy.
     *
     * @param callable $function      Function being cached
     * @param array    $cacheKeyParts Array of strings that will be concatenated and used as a key
     *
     * @return mixed
     */
    protected function executeCached(callable $function, array $cacheKeyParts)
    {
        $result = null;

        array_push($cacheKeyParts, 'executeCachedDoNotIntersectPlease');

        if ($this->isCacheAllowed()) {
            if ($this->getCache()->has($cacheKeyParts)) {
                $result = $this->getCache()->get($cacheKeyParts);

            } else {
                $result = $function();

                $this->getCache()->set($cacheKeyParts, $result, $this->getCacheTTL());
            }

        } else {
            $result = $function();
        }

        return $result;
    }

    /**
     * Get an instance of CacheKeyPartsGenerator
     *
     * @return \XLite\Core\Cache\CacheKeyPartsGenerator
     */
    protected function getCacheKeyPartsGenerator()
    {
        return $this->getContainer()->get('XLite\Core\Cache\CacheKeyPartsGenerator');
    }

    // }}}

    // {{{ Service

    /**
     * Get view class name as keys list
     *
     * @return array
     */
    protected function getViewClassKeys()
    {
        return \XLite\Core\Operator::getInstance()->getClassNameAsKeys(get_called_class());
    }

    /**
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getBlockClasses()
    {
        return 'block block-block';
    }

    /**
     * Check 'Admin welcome' block visibility
     *
     * @return boolean
     */
    protected function isAdminWelcomeBlockVisible()
    {
        return 1 != \XLite\Core\Session::getInstance()->hide_welcome_block
            && 1 != \XLite\Core\Config::getInstance()->Internal->hide_welcome_block;
    }

    /**
     * URL of the page where free license can be activated
     *
     * @return string
     */
    protected function getActivateFreeLicenseURL()
    {
        return $this->buildURL('activate_free_license');
    }

    /**
     * Check if the store has any license
     *
     * @return boolean
     */
    protected function hasLicense()
    {
        return (bool)\XLite::getXCNLicense();
    }

    /**
     * Return affiliate URL
     * @todo: add twig function
     *
     * @param string $url Url part to add OPTIONAL
     *
     * @return string
     */
    protected function getXCartURL($url = '')
    {
        return \XLite::getXCartURL($url);
    }

    /**
     * Defines the admin URL
     * @todo: add twig function
     *
     * @return string
     */
    protected function getAdminURL()
    {
        return \XLite::getInstance()->getShopURL(\XLite::getAdminScript());
    }

    /**
     * Define the tag name
     * Currently we use the rule: '<tagName>' must have 'tag-<tagName>' language variable or it will be proceeded as is
     *
     * @param string $tag
     *
     * @return string
     */
    protected function getTagName($tag)
    {
        $label = 'tag-' . $tag;
        $translation = static::t($label);

        return ($translation === $label) ? $tag : $translation;
    }

    /**
     * Get view class name as keys list
     *
     * @param string $className Class name
     *
     * @return string
     */
    protected function formatClassNameToString($className)
    {
        return str_replace('\\', '', $className);
    }

    /**
     * Get translated weight symbol
     *
     * @return string
     */
    protected function getWeightSymbol()
    {
        return \XLite\Core\Translation::translateWeightSymbol();
    }

    /**
     * Get translated dim symbol
     *
     * @return string
     */
    protected function getDimSymbol()
    {
        return \XLite\Core\Translation::translateDimSymbol();
    }

    // }}}


    // {{{ Rendering context

    /** @var RenderingContextInterface */
    protected $renderingContext;

    /**
     * @return RenderingContextInterface
     */
    protected function getRenderingContext()
    {
        if ($this->renderingContext === null) {
            $this->renderingContext = RenderingContextFactory::createContext();
        }

        return $this->renderingContext;
    }

    public function setRenderingContext(RenderingContextInterface $renderingContext)
    {
        $this->renderingContext = $renderingContext;
    }

    // }}}

    /**
     * @return DynamicWidgetRenderer
     */
    protected function getDynamicWidgetRenderer()
    {
        return $this->getContainer()->get('dynamic_widget_renderer');
    }

    /**
     * Dispatch WidgetBeforeRenderEvent
     */
    protected function dispatchBeforeRenderEvent()
    {
        $eventDispatcher = $this->getContainer()->get('event_dispatcher');

        $eventDispatcher->dispatch(Events::WIDGET_BEFORE_RENDER, new WidgetBeforeRenderEvent($this));
    }

    /**
     * Dispatch WidgetAfterRenderEvent
     *
     * @param string $template
     * @param int    $state See WidgetAfterRenderEvent::STATE_* for possible state values
     */
    protected function dispatchAfterRenderEvent($template, $state = 0)
    {
        $eventDispatcher = $this->getContainer()->get('event_dispatcher');

        $eventDispatcher->dispatch(Events::WIDGET_AFTER_RENDER, new WidgetAfterRenderEvent($this, $state, $template));
    }
}
