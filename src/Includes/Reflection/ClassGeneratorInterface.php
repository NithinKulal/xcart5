<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Reflection;

interface ClassGeneratorInterface
{
    /**
     * @param $namespace
     * @return ClassGeneratorInterface
     */
    public function setNamespace($namespace);

    /**
     * @param $class
     * @return ClassGeneratorInterface
     */
    public function setClassName($class);

    /**
     * @param $isAbstract
     * @return ClassGeneratorInterface
     */
    public function setAbstract($isAbstract);

    /**
     * @param $class
     * @return ClassGeneratorInterface
     */
    public function setParent($class);

    /**
     * @param $text
     * @return ClassGeneratorInterface
     */
    public function setDocComment($text);

    /**
     * @param $annotation
     * @return ClassGeneratorInterface
     */
    public function addAnnotation($annotation);

    /**
     * @return string
     */
    public function getSource();
}