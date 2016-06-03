<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormModel\Type\Base;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Util\StringUtil;

abstract class AType extends AbstractType
{
    /**
     * Language label translation short method
     *
     * @param string $name      Label name
     * @param array  $arguments Substitution arguments OPTIONAL
     * @param string $code      Language code OPTIONAL
     *
     * @return string
     */
    protected static function t($name, array $arguments = [], $code = null)
    {
        return \XLite\Core\Translation::getInstance()->translate($name, $arguments, $code);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        $fqcn = get_class($this);
        $name = $this->getName();

        if ($fqcn === $name) {
            $name = $this->generateBlockPrefix() ?: parent::getBlockPrefix();
        }

        return $name;
    }

    /**
     * @return null|string
     */
    protected function generateBlockPrefix()
    {
        $prefix = '';
        $fqcn = get_class($this);

        $modulePrefix = '';
        if (preg_match('/XLite\\\\Module\\\\(\w+\\\\\w+)/i', $fqcn, $matches)) {
            $modulePrefix .= $matches[1];
        }

        if (preg_match('/View\\\\FormModel\\\\Type\\\\([\w+\\\\]+)/', $fqcn, $matches)) {
            $prefix = StringUtil::fqcnToBlockPrefix(str_replace('\\', '', $modulePrefix . $matches[1]));
        }

        return $prefix;
    }
}
