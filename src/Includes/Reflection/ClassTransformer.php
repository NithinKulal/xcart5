<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Reflection;

use Includes\Decorator\Utils\Tokenizer;

class ClassTransformer implements ClassTransformerInterface
{
    private $sourcePathname;

    private $isAbstract;

    private $className;

    private $docComment;

    private $extends;

    public function __construct($sourcePathname)
    {
        $this->sourcePathname = $sourcePathname;
    }

    /**
     * @param $class
     * @return ClassTransformerInterface
     */
    public function setClassName($class)
    {
        $this->className = $class;

        return $this;
    }

    /**
     * @param $isAbstract
     * @return ClassTransformerInterface
     */
    public function setAbstract($isAbstract)
    {
        $this->isAbstract = $isAbstract;

        return $this;
    }

    /**
     * @param $class
     * @return ClassTransformerInterface
     */
    public function setParent($class)
    {
        $this->extends = $class;

        return $this;
    }

    /**
     * @param $text
     * @return ClassTransformerInterface
     */
    public function setDocComment($text)
    {
        $this->docComment = $text;

        return $this;
    }

    public function removeAnnotations(array $annotations)
    {
        $text = $this->getDocCommentText();

        foreach ($annotations as $annotation) {
            $text = preg_replace('/@(' . $annotation . '\b)/', ' $1', $text);
        }

        return $this->setDocComment($text);
    }

    public function removeEntityAnnotations()
    {
        $removeAnnotations = [
            'Entity',
            'Table',
            'Index',
            'UniqueConstraint',
            'InheritanceType',
            'DiscriminatorColumn',
            'DiscriminatorMap',
        ];

        return $this->removeAnnotations($removeAnnotations);
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return Tokenizer::getSourceCode(
            $this->sourcePathname,
            null,
            $this->getClassName(),
            $this->getExtends(),
            $this->getDocCommentText(),
            $this->isAbstract() ? 'abstract' : ''
        );
    }

    private function getClassName()
    {
        if ($this->className == null) {
            $this->className = Tokenizer::getClassName($this->sourcePathname);
        }

        return $this->className;
    }

    private function getDocCommentText()
    {
        if ($this->docComment == null) {
            $this->docComment = Tokenizer::getDocBlock($this->sourcePathname);
        }

        return $this->docComment;
    }

    private function getExtends()
    {
        if ($this->extends == null) {
            $this->extends = Tokenizer::getParentClassName($this->sourcePathname);
        }

        return $this->extends;
    }

    private function isAbstract()
    {
        if ($this->isAbstract == null) {
            $this->isAbstract = Tokenizer::isAbstract($this->sourcePathname);
        }

        return $this->isAbstract;
    }
}