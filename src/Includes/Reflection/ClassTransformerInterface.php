<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Reflection;

interface ClassTransformerInterface
{
    /**
     * @param $class
     * @return ClassTransformerInterface
     */
    public function setClassName($class);

    /**
     * @param $isAbstract
     * @return ClassTransformerInterface
     */
    public function setAbstract($isAbstract);

    /**
     * @param $class
     * @return ClassTransformerInterface
     */
    public function setParent($class);

    /**
     * @param $text
     * @return ClassTransformerInterface
     */
    public function setDocComment($text);

    /**
     * @return ClassTransformerInterface
     */
    public function removeAnnotations(array $annotations);

    /**
     * @return ClassTransformerInterface
     */
    public function removeEntityAnnotations();

    /**
     * @return string
     */
    public function getSource();
}