<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormModel\Type;

use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;

/**
 * Loader for {@link ProductCategoryType}
 */
class ProductCategoryLoader implements ChoiceLoaderInterface
{
    /**
     * @var array
     */
    protected $selected = [];
    protected $labels   = [];

    /**
     * @param null $value
     *
     * @return ChoiceListInterface
     */
    public function loadChoiceList($value = null)
    {
        return new ArrayChoiceList(array_flip($this->selected));
    }

    /**
     * @param array $values
     * @param null  $value
     *
     * @return array
     */
    public function loadChoicesForValues(array $values, $value = null)
    {
        return $values;
    }

    /**
     * @param array         $choices
     * @param null|callable $value
     *
     * @return string[]
     */
    public function loadValuesForChoices(array $choices, $value = null)
    {
        /** @var \XLite\Model\Repo\Category $repo */
        $repo = \XLite\Core\Database::getRepo('XLite\Model\Category');

        /**
         * Load labels for selected choices {@see getValueLabels}
         */
        $labels = [];
        foreach ($repo->findByIds($choices) as $category) {
            $name = [];
            foreach ($repo->getCategoryPath($category->getId()) as $cateogryInPath) {
                $name[] = $cateogryInPath->getName();
            }

            $labels[$category->getId()] = implode('/', $name);
        }
        $this->labels = $labels;

        /**
         * Prepare choices list to be compatible with selected check
         */
        $selected = [];
        foreach ($choices as $choice) {
            $selected[$choice] = (string) $choice;
        }
        $this->selected = $selected;

        return $selected;
    }

    /**
     * @param string|int $value
     *
     * @return string
     */
    public function getValueLabel($value)
    {
        return (string) (isset($this->labels[$value]) ? $this->labels[$value] : $value);
    }
}
