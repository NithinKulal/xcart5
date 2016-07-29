<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Templating\Twig;

use ArrayAccess;
use BadMethodCallException;
use Closure;
use ReflectionClass;
use ReflectionMethod;
use Twig_Error;
use Twig_Error_Runtime;
use Twig_Markup;
use Twig_TemplateInterface;
use XLite\Core\Converter;
use XLite\View\AView;

/**
 * Base class for compiled templates.
 *
 * TODO: Move widget instantiation logic from AView to a separate WidgetFactory
 */
abstract class Template extends \Twig_Template
{
    protected $formWidgets;

    protected function renderWidget($class, $params = array())
    {
        $this->getThis()->getWidget($params, $class)->display();
    }

    protected function renderWidgetList($name, $params = array())
    {
        $type = isset($params['type']) ? strtolower($params['type']) : null;

        unset($params['type']);

        if ($type == 'inherited') {
            $this->getThis()->displayInheritedViewListContent($name, $params);
        } else if ($type == 'nested') {
            $this->getThis()->displayNestedViewListContent($name, $params);
        } else {
            $this->getThis()->displayViewListContent($name, $params);
        }
    }

    protected function startForm($class, $params = array())
    {
        $formWidget = $this->getThis()->getWidget($params, $class);

        $formWidget->display();

        $this->formWidgets[] = $formWidget;
    }

    protected function endForm()
    {
        $formWidget = array_pop($this->formWidgets);

        $formWidget->setWidgetParams(array('end' => '1'));

        $formWidget->display();
    }

    /**
     * Closure::bind doen't work with internal classes
     *
     * @param  mixed    $object Object to check
     * @return boolean
     */
    protected function isObjectBindable($object)
    {
        $class = get_class($object);
        if (!isset(static::$cache[$class]['isUserDefined'])) {
            $reflection = new ReflectionClass($object);
            static::$cache[$class]['isUserDefined'] = $reflection->isUserDefined();
        }

        return static::$cache[$class]['isUserDefined'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getAttribute(
        $object, $item, array $arguments = array(), $type = self::ANY_CALL, $isDefinedTest = false,
        $ignoreStrictCheck = false
    ) {
        if ($type == self::METHOD_CALL) {
            if (LC_IS_PHP_7) {
                // Since PHP 7.0.0 we can't bind closure to InternalClass,
                // see http://php.net/manual/en/closure.bind.php
                // The only way to check if class is internal is reflection
                if(!$this->isObjectBindable($object)) {
                   return call_user_func_array(array($object, $item), $arguments);
                }
            }

            $closure = Closure::bind(function () use ($object, $item, $arguments) {
                return call_user_func_array(array($object, $item), $arguments);
            }, null, $object);

            return $closure();
        } else {

            if ($object instanceof \XLite\Model\AEntity) {
                return $object->{'get' . Converter::convertToCamelCase($item)}();

            } elseif ($object instanceof \XLite\Base) {
                return $object->get($item);

            } elseif ($object instanceof \XLite\Core\CommonCell) {
                return $object->$item;

            } elseif ($object instanceof \stdClass) {
                return isset($object->$item) ? $object->$item : null;

            } elseif (is_array($object)) {
                return isset($object[$item]) ? $object[$item] : null;
            }
        }

        return parent::getAttribute($object, $item, $arguments, $type, $isDefinedTest, $ignoreStrictCheck);
    }

    protected function displayWithErrorHandling(array $context, array $blocks = array())
    {
        try {
            $this->doDisplay($context, $blocks);
        } catch (Twig_Error $e) {
            if (!$e->getTemplateFile()) {
                $e->setTemplateFile($this->getTemplateName());
            }

            // this is mostly useful for Twig_Error_Loader exceptions
            // see Twig_Error_Loader
            if (false === $e->getTemplateLine()) {
                $e->setTemplateLine(-1);
                $e->guess();
            }

            throw $e;
        }

        // Do not catch non-Twig exceptions

        /*catch (Exception $e) {
            throw new Twig_Error_Runtime(sprintf('An exception has been thrown during the rendering of a template ("%s").', $e->getMessage()), -1, $this->getTemplateName(), $e);
        }*/
    }

    /**
     * Get current bound AView object
     *
     * @return AView
     */
    protected function getThis()
    {
        return $this->env->getGlobals()['this'];
    }
}
