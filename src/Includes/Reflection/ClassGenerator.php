<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Reflection;


class ClassGenerator implements ClassGeneratorInterface
{
    private $namespace;

    private $isAbstract;

    private $className;

    private $docComment;

    private $extends;

    private $annotations = [];

    /**
     * @param $namespace
     * @return ClassGeneratorInterface
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * @param $class
     * @return ClassGeneratorInterface
     */
    public function setClassName($class)
    {
        $this->className = $class;

        return $this;
    }

    /**
     * @param $isAbstract
     * @return ClassGeneratorInterface
     */
    public function setAbstract($isAbstract)
    {
        $this->isAbstract = $isAbstract;

        return $this;
    }

    /**
     * @param $class
     * @return ClassGeneratorInterface
     */
    public function setParent($class)
    {
        $this->extends = $class;

        return $this;
    }

    /**
     * @param $text
     * @return ClassGeneratorInterface
     */
    public function setDocComment($text)
    {
        $this->docComment = $text;

        return $this;
    }

    /**
     * @param $annotation
     * @return ClassGeneratorInterface
     */
    public function addAnnotation($annotation)
    {
        $this->annotations[$annotation] = $annotation;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        $namespace = $this->namespace ? "namespace $this->namespace;" : '';
        $class     = $this->isAbstract ? 'abstract class' : 'class';

        if (!empty($this->annotations)) {
            $docComment = empty($this->docComment) ? "/**\n */" : $this->docComment;

            foreach ($this->annotations as $annotation) {
                $docComment = preg_replace('/\*\/$/', "* @$annotation\n */", $docComment);
            }
        } else {
            $docComment = $this->docComment;
        }

        return <<<PHP
<?php
$namespace
$docComment
$class $this->className extends \\$this->extends {}
PHP;
    }
}