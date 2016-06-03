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
     *
     * @param array oldValues Old values
     * @param array newValues New values
     *
     * @return array
     */
    static public function getDiff(array $oldValues, array $newValues)
    {
        $diff = array();
        if ($newValues) {
            foreach ($newValues as $attributeId => $attributeValues) {
                $changed = false;
                $changes = array(
                    'deleted' => array(),
                    'added'   => array(),
                    'changed' => array(),
                );

                foreach ($attributeValues as $id => $value) {
                    if (
                        !isset($oldValues[$attributeId])
                        || !isset($oldValues[$attributeId][$id])
                    ) {
                        $changes['added'][$id] = $value;
                        $changed = true;

                    } else {
                        $c = array();
                        foreach ($value as $k => $v) {
                            if ($v != $oldValues[$attributeId][$id][$k]) {
                                $c[$k] = $v;
                            }
                        }
                        if ($c) {
                            $changes['changed'][$id] = $c;
                            $changed = true;
                        }
                    }
                }

                if (
                    isset($oldValues[$attributeId])
                    || $oldValues[$attributeId]
                ) {
                    foreach ($oldValues[$attributeId] as $id => $value) {
                        if (!isset($newValues[$attributeId][$id])) {
                            $changes['deleted'][] = $id;
                            $changed = true;
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
     * @return \XLite\Model\AEntity
     */
    public function cloneEntity()
    {
        $newEntity = parent::cloneEntity();
        $newEntity->setAttribute($this->getAttribute());

        return $newEntity;
    }
}
