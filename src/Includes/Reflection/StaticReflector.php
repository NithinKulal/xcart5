<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Reflection;

use Doctrine\Common\Annotations\AnnotationException;
use Includes\Annotations\Parser\AnnotationParserInterface;
use Includes\Decorator\Utils\Tokenizer;
use XLite\Logger;

class StaticReflector implements StaticReflectorInterface
{
    const DECORATOR_MARKER_INTERFACE = 'XLite\Base\IDecorator';

    /**
     * @var string
     */
    private $pathname;

    /**
     * @var AnnotationParserInterface
     */
    private $annotationParser;

    private $annotationsByType;

    public function __construct(AnnotationParserInterface $annotationParser, $pathname)
    {
        $this->pathname         = $pathname;
        $this->annotationParser = $annotationParser;
    }

    public function getPathname()
    {
        return $this->pathname;
    }

    public function getNamespace()
    {
        return Tokenizer::getNamespace($this->pathname);
    }

    public function isAbstract()
    {
        return Tokenizer::isAbstract($this->pathname);
    }

    public function isClass()
    {
        return Tokenizer::getClassName($this->pathname) !== null;
    }

    public function isInterface()
    {
        return Tokenizer::getInterfaceName($this->pathname) !== null;
    }

    public function getClassName()
    {
        return Tokenizer::getClassName($this->pathname);
    }

    public function getFQCN()
    {
        $namespace = $this->getNamespace();

        return $namespace
            ? $namespace . '\\' . $this->getClassName()
            : $this->getClassName();
    }

    public function getDocCommentText()
    {
        return Tokenizer::getDocBlock($this->pathname);
    }

    public function getClassAnnotations()
    {
        try {
            return $this->annotationParser->parse($this->getDocCommentText());
        } catch (AnnotationException $e) {
            $this->getLogger()->log(sprintf('AnnotationException: %s (%s)', $e->getMessage(), $this->getPathname()), LOG_WARNING);

            return [];
        }
    }

    public function getClassAnnotationsOfType($type)
    {
        if (!isset($this->annotationsByType)) {
            foreach ($this->getClassAnnotations() as $annotation) {
                $annotationClass = get_class($annotation);

                if (!isset($this->annotationsByType[$annotationClass])) {
                    if ($annotationClass == 'Includes\Annotations\LC_Dependencies') {
                        $error = sprintf('@LC_Dependencies annotation is deprecated, use @Decorator\Depend instead (%s)', $this->getPathname());

                        trigger_error($error, E_USER_DEPRECATED);

                        $this->getLogger()->log($error, LOG_WARNING);
                    }

                    $this->annotationsByType[$annotationClass] = [];
                }

                $this->annotationsByType[$annotationClass][] = $annotation;
            }
        }

        return isset($this->annotationsByType[$type]) ? $this->annotationsByType[$type] : [];
    }

    public function getParent()
    {
        $class = Tokenizer::getParentClassName($this->pathname);

        return ltrim($class, '\\');
    }

    public function getImplements()
    {
        return array_map(
            function ($interface) {
                return ltrim($interface, '\\');
            },
            Tokenizer::getInterfaces($this->pathname)
        );
    }

    public function isDecorator()
    {
        return in_array(self::DECORATOR_MARKER_INTERFACE, $this->getImplements());
    }

    public function getModule()
    {
        $parts = explode('\\', $this->getNamespace());

        return count($parts) > 3 && $parts[1] == 'Module'
            ? $parts[2] . '\\' . $parts[3]
            : null;
    }

    public function getPositiveDependencies()
    {
        $pos = [];

        /** @var \Includes\Annotations\LC_Dependencies $annotation */
        foreach ($this->getClassAnnotationsOfType('Includes\Annotations\LC_Dependencies') as $annotation) {
            $pos = array_merge($pos, $annotation->dependencies);
        }

        /** @var \Includes\Annotations\Decorator\Depend $annotation */
        foreach ($this->getClassAnnotationsOfType('Includes\Annotations\Decorator\Depend') as $annotation) {
            $pos = array_merge($pos, $annotation->dependencies);
        }

        return $pos;
    }

    public function getNegativeDependencies()
    {
        $neg = [];

        /** @var \Includes\Annotations\LC_Dependencies $annotation */
        foreach ($this->getClassAnnotationsOfType('Includes\Annotations\LC_Dependencies') as $annotation) {
            $neg = array_merge($neg, $annotation->incompatibilities);
        }

        /** @var \Includes\Annotations\Decorator\Depend $annotation */
        foreach ($this->getClassAnnotationsOfType('Includes\Annotations\Decorator\Depend') as $annotation) {
            $neg = array_merge($neg, $annotation->incompatibilities);
        }

        return $neg;
    }

    public function getAfterModules()
    {
        $modules = [];

        /** @var \Includes\Annotations\LC_Dependencies $annotation */
        foreach ($this->getClassAnnotationsOfType('Includes\Annotations\LC_Dependencies') as $annotation) {
            $modules = array_merge($modules, $annotation->dependencies);
        }

        /** @var \Includes\Annotations\Decorator\Depend $annotation */
        foreach ($this->getClassAnnotationsOfType('Includes\Annotations\Decorator\Depend') as $annotation) {
            $modules = array_merge($modules, $annotation->dependencies);
        }

        /** @var \Includes\Annotations\Decorator\After $annotation */
        foreach ($this->getClassAnnotationsOfType('Includes\Annotations\Decorator\After') as $annotation) {
            $modules = array_merge($modules, $annotation->modules);
        }

        return $modules;
    }

    public function getBeforeModules()
    {
        $modules = [];

        /** @var \Includes\Annotations\Decorator\Before $annotation */
        foreach ($this->getClassAnnotationsOfType('Includes\Annotations\Decorator\Before') as $annotation) {
            $modules = array_merge($modules, $annotation->modules);
        }

        return $modules;
    }

    public function isEntity()
    {
        return $this->isModel() && $this->getClassAnnotationsOfType('Doctrine\ORM\Mapping\Entity');
    }

    public function isMappedSuperclass()
    {
        return $this->isModel() && $this->getClassAnnotationsOfType('Doctrine\ORM\Mapping\MappedSuperclass');
    }

    public function hasLifecycleCallbacks()
    {
        return $this->isModel() && $this->getClassAnnotationsOfType('Doctrine\ORM\Mapping\HasLifecycleCallbacks');
    }

    private function isModel()
    {
        $parts = explode('\\', $this->getNamespace());

        return count($parts) > 1 && $parts[1] == 'Model'
               || count($parts) > 4 && $parts[1] == 'Module' && $parts[4] == 'Model';
    }

    /**
     * @return Logger
     */
    protected function getLogger()
    {
        return Logger::getInstance();
    }
}
