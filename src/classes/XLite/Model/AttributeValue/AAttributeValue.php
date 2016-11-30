<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\AttributeValue;

/**
 * Abstract attribute value
 *
 * @MappedSuperclass
 */
abstract class AAttributeValue extends \XLite\Model\Base\I18n
{
    /**
     * Rate type codes
     */
    const TYPE_ABSOLUTE = 'a';
    const TYPE_PERCENT  = 'p';

    /**
     * ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Product
     *
     * @var \XLite\Model\Product
     *
     * @ManyToOne  (targetEntity="XLite\Model\Product")
     * @JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * Attribute
     *
     * @var \XLite\Model\Attribute
     *
     * @ManyToOne  (targetEntity="XLite\Model\Attribute")
     * @JoinColumn (name="attribute_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $attribute;

    /**
     * Return attribute value as string
     *
     * @return string
     */
    abstract public function asString();

    /**
     * Return diff
     * @todo: add test
     *
     * @param array $oldValues Old values
     * @param array $newValues New values
     *
     * @return array
     */
    public static function getDiff(array $oldValues, array $newValues)
    {
        $diff = [];
        if ($newValues) {
            foreach ($newValues as $attributeId => $attributeValues) {
                $changed = false;
                $changes = [
                    'deleted' => [],
                    'added'   => [],
                    'changed' => [],
                ];

                foreach ($attributeValues as $id => $value) {
                    if (!isset($oldValues[$attributeId][$id])) {
                        $changed               = true;
                        $changes['added'][$id] = $value;

                    } else {
                        $c = [];
                        foreach ($value as $k => $v) {
                            if ($v != $oldValues[$attributeId][$id][$k]) {
                                $c[$k] = $v;
                            }
                        }
                        if ($c) {
                            $changed                 = true;
                            $changes['changed'][$id] = $c;
                        }
                    }
                }

                if (!empty($oldValues[$attributeId])) {
                    foreach ($oldValues[$attributeId] as $id => $value) {
                        if (!isset($newValues[$attributeId][$id])) {
                            $changed              = true;
                            $changes['deleted'][] = $id;
                        }
                    }
                }

                if ($changed) {
                    $diff[$attributeId] = $changes;
                }
            }
        }

        return $diff;
    }

    /**
     * Clone
     *
     * @return static
     */
    public function cloneEntity()
    {
        /** @var static $newEntity */
        $newEntity = parent::cloneEntity();
        $newEntity->setAttribute($this->getAttribute());

        return $newEntity;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return (int) $this->id;
    }

    /**
     * Set product
     *
     * @param \XLite\Model\Product $product
     */
    public function setProduct(\XLite\Model\Product $product = null)
    {
        $this->product = $product;
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set attribute
     *
     * @param \XLite\Model\Attribute $attribute
     */
    public function setAttribute(\XLite\Model\Attribute $attribute = null)
    {
        $this->attribute = $attribute;
    }

    /**
     * Get attribute
     *
     * @return \XLite\Model\Attribute
     */
    public function getAttribute()
    {
        return $this->attribute;
    }
}
