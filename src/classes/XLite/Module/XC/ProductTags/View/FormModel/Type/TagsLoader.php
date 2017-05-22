<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductTags\View\FormModel\Type;

use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;

/**
 * Loader for {@link TagsType}
 */
class TagsLoader implements ChoiceLoaderInterface
{
    /**
     * @var array
     */
    protected $selected = [];
    protected $labels   = [];
    protected $list     = [];

    /**
     * @param null $value
     *
     * @return ChoiceListInterface
     */
    public function loadChoiceList($value = null)
    {
        /** @var \XLite\Module\XC\ProductTags\Model\Tag $tags */
        $tags = \XLite\Core\Database::getRepo('XLite\Module\XC\ProductTags\Model\Tag')->findAllTags();

        $list = [];
        foreach ($tags as $tag) {
            if (!$value || !in_array((string) $tag->getId(), $value, true)) {
                $list[$tag->getId()] = $tag->getName();
            }
        }

        $this->labels = array_replace($this->labels, $list);

        return new ArrayChoiceList(array_keys($this->labels));
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
        /**
         * Prepare choices list to be compatible with selected check
         */
        $selected = [];
        foreach ($choices as $choice) {
            $selected[$choice] = (string) $choice;

            /**
             * Add all selected to labels to create newbies
             */
            $this->labels[$choice] = (string) $choice;
        }

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
