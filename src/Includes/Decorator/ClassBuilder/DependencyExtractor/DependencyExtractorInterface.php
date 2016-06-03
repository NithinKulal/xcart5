<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\ClassBuilder\DependencyExtractor;

interface DependencyExtractorInterface
{
    /**
     * Get a list of files that are possible decorators.
     * It is used for performance reasons, to perform a quick check of mtimes of these files before calling a slower getDecorators.
     *
     * @return array Array of pathnames
     */
    public function getDecoratorCandidates();

    /**
     * Get all defined decorators.
     *
     * @return array Array of pathnames
     */
    public function getDecorators();

    /**
     * Get a list of files that are possible decorators of the given class.
     * It is used for performance reasons, to perform a quick check of mtimes of these files before calling a slower getClassDecorators.
     *
     * @param $class
     *
     * @return array Array of pathnames
     */
    public function getClassDecoratorCandidates($class);

    /**
     * Get a list of class decorators.
     *
     * @param $class
     *
     * @return array Array of pathnames
     */
    public function getClassDecorators($class);

    /**
     * Check if class decorators were changed since they've been cached.
     *
     * @param $class
     *
     * @return bool
     */
    public function areClassDecoratorsChanged($class);
}