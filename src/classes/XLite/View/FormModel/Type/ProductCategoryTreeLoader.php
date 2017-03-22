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
use XLite\Core\Cache\ExecuteCachedTrait;

/**
 * Loader for {@link ProductCategoryTreeType}
 */
class ProductCategoryTreeLoader implements ChoiceLoaderInterface
{
    use ExecuteCachedTrait;

    /**
     * @param null $value
     *
     * @return ChoiceListInterface
     */
    public function loadChoiceList($value = null)
    {
        return new ArrayChoiceList(array_keys($this->getCategories()));
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
        return array_map('strval', $choices);
    }

    /**
     * @param string|int $value
     *
     * @return string
     */
    public function getValueLabel($value)
    {
        $categories = $this->getCategories();

        return (string) (isset($categories[$value]) ? $categories[$value] : $value);
    }

    /**
     * @return array
     */
    protected function getCategories()
    {
        return $this->executeCachedRuntime(function() {
            $repo = \XLite\Core\Database::getRepo('XLite\Model\Category');

            $categories = $repo->getAllCategoriesAsDTO();
            $result     = [];
            foreach ($categories as $category) {
                $result[$category['id']] = str_repeat('---', $category['depth']) . $category['name'];
            }

            return $result;
        });
    }
}
