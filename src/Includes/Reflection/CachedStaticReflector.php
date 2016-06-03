<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Reflection;

use Includes\Annotations\Parser\AnnotationParserInterface;

class CachedStaticReflector extends StaticReflector implements SerializableState
{
    private $cachedState = [];

    public function __construct(AnnotationParserInterface $annotationParser, $pathname)
    {
        parent::__construct($annotationParser, $pathname);
    }

    public function getNamespace()
    {
        if (!isset($this->cachedState['namespace'])) {
            $this->cachedState['namespace'] = parent::getNamespace();
        }

        return $this->cachedState['namespace'];
    }

    public function isAbstract()
    {
        if (!isset($this->cachedState['isAbstract'])) {
            $this->cachedState['isAbstract'] = parent::isAbstract();
        }

        return $this->cachedState['isAbstract'];
    }

    public function isClass()
    {
        if (!isset($this->cachedState['isClass'])) {
            $this->cachedState['isClass'] = parent::isClass();
        }

        return $this->cachedState['isClass'];
    }

    public function isInterface()
    {
        if (!isset($this->cachedState['isInterface'])) {
            $this->cachedState['isInterface'] = parent::isInterface();
        }

        return $this->cachedState['isInterface'];
    }

    public function getClassName()
    {
        if (!isset($this->cachedState['className'])) {
            $this->cachedState['className'] = parent::getClassName();
        }

        return $this->cachedState['className'];
    }

    public function getFQCN()
    {
        if (!isset($this->cachedState['fqcn'])) {
            $this->cachedState['fqcn'] = parent::getFQCN();
        }

        return $this->cachedState['fqcn'];
    }

    public function getDocCommentText()
    {
        if (!isset($this->cachedState['docCommentText'])) {
            $this->cachedState['docCommentText'] = parent::getDocCommentText();
        }

        return $this->cachedState['docCommentText'];
    }

    public function getClassAnnotations()
    {
        if (!isset($this->cachedState['classAnnotations'])) {
            $this->cachedState['classAnnotations'] = parent::getClassAnnotations();
        }

        return $this->cachedState['classAnnotations'];
    }

    public function getParent()
    {
        if (!isset($this->cachedState['parent'])) {
            $this->cachedState['parent'] = parent::getParent();
        }

        return $this->cachedState['parent'];
    }

    public function getImplements()
    {
        if (!isset($this->cachedState['implements'])) {
            $this->cachedState['implements'] = parent::getImplements();
        }

        return $this->cachedState['implements'];
    }

    public function isDecorator()
    {
        if (!isset($this->cachedState['isDecorator'])) {
            $this->cachedState['isDecorator'] = parent::isDecorator();
        }

        return $this->cachedState['isDecorator'];
    }

    public function getModule()
    {
        if (!isset($this->cachedState['module'])) {
            $this->cachedState['module'] = parent::getModule();
        }

        return $this->cachedState['module'];
    }

    public function getPositiveDependencies()
    {
        if (!isset($this->cachedState['positiveDependencies'])) {
            $this->cachedState['positiveDependencies'] = parent::getPositiveDependencies();
        }

        return $this->cachedState['positiveDependencies'];
    }

    public function getAfterModules()
    {
        if (!isset($this->cachedState['afterModules'])) {
            $this->cachedState['afterModules'] = parent::getAfterModules();
        }

        return $this->cachedState['afterModules'];
    }

    public function getBeforeModules()
    {
        if (!isset($this->cachedState['beforeModules'])) {
            $this->cachedState['beforeModules'] = parent::getBeforeModules();
        }

        return $this->cachedState['beforeModules'];
    }

    public function getNegativeDependencies()
    {
        if (!isset($this->cachedState['negativeDependencies'])) {
            $this->cachedState['negativeDependencies'] = parent::getNegativeDependencies();
        }

        return $this->cachedState['negativeDependencies'];
    }

    public function isEntity()
    {
        if (!isset($this->cachedState['isEntity'])) {
            $this->cachedState['isEntity'] = parent::isEntity();
        }

        return $this->cachedState['isEntity'];
    }

    public function isMappedSuperclass()
    {
        if (!isset($this->cachedState['isMappedSuperclass'])) {
            $this->cachedState['isMappedSuperclass'] = parent::isMappedSuperclass();
        }

        return $this->cachedState['isMappedSuperclass'];
    }

    public function hasLifecycleCallbacks()
    {
        if (!isset($this->cachedState['hasLifecycleCallbacks'])) {
            $this->cachedState['hasLifecycleCallbacks'] = parent::hasLifecycleCallbacks();
        }

        return $this->cachedState['hasLifecycleCallbacks'];
    }

    public function serializeState()
    {
        return serialize($this->cachedState);
    }

    public function unserializeState($data)
    {
        $this->cachedState = unserialize($data);
    }
}