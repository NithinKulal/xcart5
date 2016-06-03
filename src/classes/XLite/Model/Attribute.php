<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Attribute
 *
 * @Entity
 * @Table  (name="attributes")
 */
class Attribute extends \XLite\Model\Base\I18n
{
    /*
     * Attribute types
     */
    const TYPE_TEXT     = 'T';
    const TYPE_CHECKBOX = 'C';
    const TYPE_SELECT   = 'S';

    /*
     * Add to new products or class’s assigns automatically with select value
     */
    const ADD_TO_NEW_YES    = 'Y'; // 'Yes'
    const ADD_TO_NEW_NO     = 'N'; // 'NO'
    const ADD_TO_NEW_YES_NO = 'B'; // 'YES/NO' (BOTH)

    /*
     * Attribute delimiter
     */
    const DELIMITER = ', ';

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
     * Position
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $position = 0;

    /**
     * Decimals
     *
     * @var integer
     *
     * @Column (type="integer", length=1)
     */
    protected $decimals = 0;

    /**
     * Product class
     *
     * @var \XLite\Model\ProductClass
     *
     * @ManyToOne  (targetEntity="XLite\Model\ProductClass", inversedBy="attributes")
     * @JoinColumn (name="product_class_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $productClass;

    /**
     * Attribute group
     *
     * @var \XLite\Model\AttributeGroup
     *
     * @ManyToOne  (targetEntity="XLite\Model\AttributeGroup", inversedBy="attributes")
     * @JoinColumn (name="attribute_group_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $attributeGroup;

    /**
     * Attribute options
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Model\AttributeOption", mappedBy="attribute", cascade={"all"})
     */
    protected $attribute_options;

    /**
     * Product
     *
     * @var \XLite\Model\Product
     *
     * @ManyToOne  (targetEntity="XLite\Model\Product", inversedBy="attributes")
     * @JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * Option type
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $type = self::TYPE_SELECT;

    /**
     * Add to new products or class’s assigns automatically
     *
     * @var boolean
     *
     * @Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $addToNew = '';

    /**
     * Attribute properties
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Model\AttributeProperty", mappedBy="attribute")
     */
    protected $attribute_properties;

    /**
     * Return name of widget class
     *
     * @param string $type      Attribute type
     * @param string $interface Interface (Admin | Customer) OPTIONAL
     *
     * @return string
     */
    public static function getWidgetClass($type, $interface = null)
    {
        if (null === $interface) {
            $interface = \XLite::isAdminZone() ? 'Admin' : 'Customer';
        }

        return '\XLite\View\Product\AttributeValue\\'
            . $interface
            . '\\'
            . static::getTypes($type, true);
    }

    /**
     * Return name of value class
     *
     * @param string $type Type
     *
     * @return string
     */
    public static function getAttributeValueClass($type)
    {
        return '\XLite\Model\AttributeValue\AttributeValue'
            . static::getTypes($type, true);
    }

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = array())
    {
        $this->attribute_options = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Return number of products associated with this attribute
     *
     * @return integer
     */
    public function getProductsCount()
    {
        return $this->getClass()->getProductsCount();
    }

    /**
     * Return list of types or type
     *
     * @param string  $type              Type OPTIONAL
     * @param boolean $returnServiceType Return service type OPTIONAL
     *
     * @return array | string
     */
    public static function getTypes($type = null, $returnServiceType = false)
    {
        $list = array(
            self::TYPE_SELECT   => static::t('Plain field'),
            self::TYPE_TEXT     => static::t('Textarea'),
            self::TYPE_CHECKBOX => static::t('Yes/No'),
        );

        $listServiceTypes = array(
            self::TYPE_SELECT   => 'Select',
            self::TYPE_TEXT     => 'Text',
            self::TYPE_CHECKBOX => 'Checkbox',
        );

        $list = $returnServiceType ? $listServiceTypes : $list;

        return null !== $type
            ? (isset($list[$type]) ? $list[$type] : null)
            : $list;
    }

    /**
     * Return list of 'addToNew' types
     *
     * @return array
     */
    public static function getAddToNewTypes()
    {
        return array(
            self::ADD_TO_NEW_YES,
            self::ADD_TO_NEW_NO,
            self::ADD_TO_NEW_YES_NO,
        );
    }

    /**
     * Return values associated with this attribute
     *
     * @return mixed
     */
    public function getAttributeValues()
    {
        $cnd = new \XLite\Core\CommonCell;
        $cnd->attribute = $this;

        return \XLite\Core\Database::getRepo(static::getAttributeValueClass($this->getType()))
            ->search($cnd);
    }

    /**
     * Return number of values associated with this attribute
     *
     * @return integer
     */
    public function getAttributeValuesCount()
    {
        $cnd = new \XLite\Core\CommonCell;
        $cnd->attribute = $this;

        return \XLite\Core\Database::getRepo(static::getAttributeValueClass($this->getType()))
            ->search($cnd, true);
    }

    /**
     * Set 'addToNew' value
     *
     * @param string|array $value Value
     *
     * @return void
     */
    public function setAddToNew($value)
    {
        if (is_array($value)
            && static::TYPE_CHECKBOX === $this->getType()
        ) {
            if (2 === count($value)) {
                $value = static::ADD_TO_NEW_YES_NO;

            } elseif (1 === count($value)) {
                $value = array_shift($value) ? static::ADD_TO_NEW_YES : static::ADD_TO_NEW_NO;
            }
        }

        $this->addToNew = in_array($value, static::getAddToNewTypes()) ? $value : '';
    }

    /**
     * Get 'addToNew' value
     *
     * @return array
     */
    public function getAddToNew()
    {
        $value = null;
        if (self::TYPE_CHECKBOX === $this->getType()) {
            switch ($this->addToNew) {
                case self::ADD_TO_NEW_YES:
                    $value = array(1);
                    break;

                case self::ADD_TO_NEW_NO:
                    $value = array(0);
                    break;

                case self::ADD_TO_NEW_YES_NO:
                    $value = array(0, 1);
                    break;

                default:
            }
        }

        return $value;
    }

    /**
     * Set type
     *
     * @param string $type Type
     *
     * @return void
     */
    public function setType($type)
    {
        $types = static::getTypes();

        if (isset($types[$type])) {
            if ($this->type
                && $type != $this->type
                && $this->getId()
            ) {
                foreach ($this->getAttributeOptions() as $option) {
                    \XLite\Core\Database::getEM()->remove($option);
                }
                foreach ($this->getAttributeValues() as $value) {
                    \XLite\Core\Database::getEM()->remove($value);
                }
            }
            $this->type = $type;
        }
    }

    /**
     * Return property
     *
     * @param \XLite\Model\Product $product Product OPTIONAL
     *
     * @return \XLite\Model\AttributeProperty
     */
    protected function getProperty($product)
    {
        $result = \XLite\Core\Database::getRepo('XLite\Model\AttributeProperty')->findOneBy(
            array(
                'attribute' => $this,
                'product'   => $product,
            )
        );

        if (!$result) {
            $result = new \XLite\Model\AttributeProperty();
            $result->setAttribute($this);
            $result->setProduct($product);
            $this->addAttributeProperty($result);
        }

        return $result;
    }

    /**
     * Returns position
     *
     * @param \XLite\Model\Product $product Product OPTIONAL
     *
     * @return integer
     */
    public function getPosition($product = null)
    {
        if ($product) {
            $result = $this->getProperty($product);
            $result = $result ? $result->getPosition() : 0;

        } else {
            $result = $this->position;
        }

        return $result;
    }

    /**
     * Set the position
     *
     * @param integer|array $value
     *
     * @return void
     */
    public function setPosition($value)
    {
        if (is_array($value)) {
            $property = $this->getProperty($value['product']);
            $property->setPosition($value['position']);
            \XLite\Core\Database::getEM()->persist($property);

        } else {
            $this->position = $value;
        }
    }

    /**
     * Add to new product
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return void
     */
    public function addToNewProduct(\XLite\Model\Product $product)
    {
        if ($this->getAddToNew()) {
            foreach ($this->getAddToNew() as $value) {
                $av = $this->createAttributeValue($product);
                if ($av) {
                    $av->setValue($value);
                }
            }

        } elseif (self::TYPE_SELECT === $this->getType()) {
            $attributeOptions = \XLite\Core\Database::getRepo('XLite\Model\AttributeOption')->findBy(
                array(
                    'attribute' => $this,
                    'addToNew'  => true,
                )
            ) ;

            foreach ($attributeOptions as $attributeOption) {
                $av = $this->createAttributeValue($product);
                if ($av) {
                    $av->setAttributeOption($attributeOption);
                }
            }
        } elseif (self::TYPE_TEXT === $this->getType()) {
            $av = $this->createAttributeValue($product);
            if ($av) {
                $av->setEditable(true);
                $av->setValue('');
            }
        }
    }

    /**
     * Apply changes
     *
     * @param \XLite\Model\Product $product Product
     * @param mixed                $changes Changes
     *
     * @return void
     */
    public function applyChanges(\XLite\Model\Product $product, $changes)
    {
        if ((
                !$this->getProductClass()
                && !$this->getProduct()
            )
            || (
                $this->getProductClass()
                && $product->getProductClass()
                && $this->getProductClass()->getId() == $product->getProductClass()->getId()
            )
            || (
                $this->getProduct()
                && $this->getProduct()->getId() == $product->getId()
            )
        ) {
            $class = static::getAttributeValueClass($this->getType());
            $repo = \XLite\Core\Database::getRepo($class);

            switch ($this->getType()) {
                case self::TYPE_TEXT:
                    $this->setAttributeValue($product, $changes);
                    break;

                case self::TYPE_CHECKBOX:
                case self::TYPE_SELECT:
                    foreach ($repo->findBy(array('product' => $product, 'attribute' => $this)) as $av) {
                        $uniq = self::TYPE_CHECKBOX === $this->getType()
                            ? $av->getValue()
                            : $av->getAttributeOption()->getId();

                        if (in_array($uniq, $changes['deleted'])) {
                            $repo->delete($av, false);

                        } elseif (isset($changes['changed'][$uniq])
                            || isset($changes['added'][$uniq])
                        ) {
                            $data = isset($changes['changed'][$uniq])
                                ? $changes['changed'][$uniq]
                                : $changes['added'][$uniq];

                            if (isset($data['defaultValue'])
                                && $data['defaultValue']
                                && !$av->getDefaultValue()
                            ) {
                                $pr = $repo->findOneBy(
                                    array(
                                        'product'      => $product,
                                        'attribute'    => $this,
                                        'defaultValue' => true
                                    )
                                );
                                if ($pr) {
                                    $pr->setDefaultValue(false);
                                }
                            }

                            $repo->update($av, $data);

                            if (isset($changes['added'][$uniq])) {
                                unset($changes['added'][$uniq]);
                            }
                        }
                    }

                    if ($changes['added']) {
                        foreach ($changes['added'] as $uniq => $data) {
                            if (isset($data['defaultValue'])
                                && $data['defaultValue']
                            ) {
                                $pr = $repo->findOneBy(
                                    array(
                                        'product'      => $product,
                                        'attribute'    => $this,
                                        'defaultValue' => true
                                    )
                                );
                                if ($pr) {
                                    $pr->setDefaultValue(false);
                                }
                            }
                            $av = $this->createAttributeValue($product);

                            if ($av) {
                                if (self::TYPE_CHECKBOX === $this->getType()) {
                                    $av->setValue($uniq);

                                } else {
                                    $av->setAttributeOption(
                                        \XLite\Core\Database::getRepo('XLite\Model\AttributeOption')->find($uniq)
                                    );
                                }
                                $repo->update($av, $data);
                            }
                        }
                    }
                    break;

                default:
            }
            \XLite\Core\Database::getEM()->flush();
        }
    }

    /**
     * Set attribute value
     *
     * @param \XLite\Model\Product $product Product
     * @param mixed                $data    Value
     *
     * @return void
     */
    public function setAttributeValue(\XLite\Model\Product $product, $data)
    {
        $repo = \XLite\Core\Database::getRepo(
            static::getAttributeValueClass($this->getType())
        );

        $method = $this->defineSetAttributeValueMethodName($data);
        $this->$method($repo, $product, $data);
    }

    /**
     * Get attribute value
     *
     * @param \XLite\Model\Product $product  Product
     * @param boolean              $asString As string flag OPTIONAL
     *
     * @return mixed
     */
    public function getAttributeValue(\XLite\Model\Product $product, $asString = false)
    {
        $repo = \XLite\Core\Database::getRepo(static::getAttributeValueClass($this->getType()));

        if (self::TYPE_SELECT === $this->getType() || self::TYPE_CHECKBOX === $this->getType()) {
            $attributeValue = $repo->findBy(array('product' => $product, 'attribute' => $this));

            if ($attributeValue
                && $asString
            ) {
                if (is_array($attributeValue)) {
                    foreach ($attributeValue as $k => $v) {
                        $attributeValue[$k] = $v->asString();
                    }

                } elseif (is_object($attributeValue)) {
                    $attributeValue = $attributeValue->asString();

                } elseif (self::TYPE_CHECKBOX === $this->getType()) {
                    $attributeValue = static::t('Yes');
                }
            }

        } else {
            $attributeValue = $repo->findOneBy(
                array('product' => $product, 'attribute' => $this)
            );
            if ($attributeValue && $asString) {
                $attributeValue = $attributeValue->getValue();
            }
        }

        return $attributeValue;
    }

    /**
     * Get attribute value
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return mixed
     */
    public function getDefaultAttributeValue(\XLite\Model\Product $product)
    {
        $repo = \XLite\Core\Database::getRepo(static::getAttributeValueClass($this->getType()));

        $attributeValue = $repo->findOneBy(array('product' => $product, 'attribute' => $this, 'defaultValue' => true));
        if (!$attributeValue) {
            $attributeValue = $repo->findDefaultAttributeValue(array('product' => $product, 'attribute' => $this));
        }

        return $attributeValue;
    }

    /**
     * This attribute is multiple or not flag
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return boolean
     */
    public function isMultiple(\XLite\Model\Product $product)
    {
        $repo = \XLite\Core\Database::getRepo(static::getAttributeValueClass($this->getType()));

        return (!$this->getProduct() || $this->getProduct()->getId() == $product->getId())
            && (!$this->getProductClass()
                || ($product->getProductClass()
                    && $this->getProductClass()->getId() == $product->getProductClass()->getId()
                )
            )
            && 1 < count($repo->findBy(array('product' => $product, 'attribute' => $this)));
    }

    /**
     * Create attribute value
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return mixed
     */
    protected function createAttributeValue(\XLite\Model\Product $product)
    {
        $class = static::getAttributeValueClass($this->getType());

        $attributeValue = new $class();
        $attributeValue->setProduct($product);
        $attributeValue->setAttribute($this);
        \XLite\Core\Database::getEM()->persist($attributeValue);

        return $attributeValue;
    }

    /**
     * Create attribute option
     *
     * @param string $value Option name
     *
     * @return \XLite\Model\AttributeOption
     */
    protected function createAttributeOption($value)
    {
        $attributeOption = new \XLite\Model\AttributeOption();
        $attributeOption->setAttribute($this);
        $attributeOption->setName($value);

        \XLite\Core\Database::getEM()->persist($attributeOption);

        return $attributeOption;
    }

    // {{{ Set attribute value

    /**
     * Define method name for 'setAttributeValue' operation
     *
     * @param mixed $data Data
     *
     * @return string
     */
    protected function defineSetAttributeValueMethodName($data)
    {
        if (static::TYPE_SELECT === $this->getType()) {
            $result = 'setAttributeValueSelect';

        } elseif (static::TYPE_CHECKBOX === $this->getType() && isset($data['multiple']) && $data['multiple']) {
            $result = 'setAttributeValueCheckbox';

        } else {
            $result = 'setAttributeValueDefault';
        }

        return $result;
    }

    /**
     * Set attribute value (select)
     *
     * @param \XLite\Model\Repo\ARepo $repo    Repository
     * @param \XLite\Model\Product    $product Product
     * @param array                   $data    Data
     *
     * @return void
     */
    protected function setAttributeValueSelect(
        \XLite\Model\Repo\ARepo $repo,
        \XLite\Model\Product $product,
        array $data
    ) {
        $ids = array();
        foreach ($data['value'] as $id => $value) {
            $value = trim($value);
            if (strlen($value) > 0 && is_int($id)) {
                if (!isset($data['deleteValue'][$id])) {
                    list($avId) = $this->setAttributeValueSelectItem($repo, $product, $data, $id, $value);
                    $ids[$avId] = $avId;
                }

                if (!isset($data['multiple'])) {
                    break;
                }
            }
        }

        foreach ($repo->findBy(array('product' => $product, 'attribute' => $this)) as $data) {
            if ($data->getId() && !isset($ids[$data->getId()])) {
                $repo->delete($data, false);
            }
        }
    }

    /**
     * Set select attribute item
     *
     * @param \XLite\Model\Repo\ARepo $repo    Repository
     * @param \XLite\Model\Product    $product Product
     * @param array                   $data    Data
     * @param integer                 $id      Attribute value ID
     * @param mixed                   $value   Attribute value
     *
     * @return array
     */
    protected function setAttributeValueSelectItem(
        \XLite\Model\Repo\ARepo $repo,
        \XLite\Model\Product $product,
        array $data,
        $id,
        $value
    ) {
        $result = array(null, null, null);

        $attributeValue = $attributeOption = null;

        if ($this->getProduct() && 0 < $id && !isset($data['ignoreIds'])) {
            $attributeValue = $repo->find($id);
            if ($attributeValue) {
                $attributeOption = $attributeValue->getAttributeOption();
                $attributeOption->setName($value);
            }
        }

        if (!$attributeOption) {
            $attributeOption = \XLite\Core\Database::getRepo('XLite\Model\AttributeOption')
                ->findOneByNameAndAttribute($value, $this);
        }

        if (!$attributeOption) {
            $attributeOption = $this->createAttributeOption($value);

        } else {
            $attributeValue = $repo->findOneBy(
                array(
                    'attribute_option' => $attributeOption,
                    'product' => $product,
                )
            );
        }

        if (!$attributeValue && 0 < $id && !isset($data['ignoreIds'])) {
            $attributeValue = $repo->find($id);
        }

        if ($attributeValue) {
            $result[0] = $attributeValue->getId();

        } elseif ($attributeOption) {
            $attributeValue = $this->createAttributeValue($product);
        }

        if ($attributeValue) {
            $attributeValue->setAttributeOption($attributeOption);
            $attributeValue->setDefaultValue(isset($data['default'][$id]));
            foreach ($attributeValue::getModifiers() as $modifier => $options) {
                if (isset($data[$modifier]) && isset($data[$modifier][$id])) {
                    $attributeValue->setModifier($data[$modifier][$id], $modifier);
                }
            }

            \XLite\Core\Database::getEM()->flush();
            $result = array(
                $attributeValue->getId(),
                $attributeValue,
                $attributeOption,
            );
        }

        return $result;
    }

    /**
     * Set attribute value (checkbox)
     *
     * @param \XLite\Model\Repo\ARepo $repo    Repository
     * @param \XLite\Model\Product    $product Product
     * @param array                   $data    Data
     *
     * @return void
     */
    protected function setAttributeValueCheckbox(
        \XLite\Model\Repo\ARepo $repo,
        \XLite\Model\Product $product,
        array $data
    ) {
        foreach (array(true, false) as $value) {
            $this->setAttributeValueCheckboxItem($repo, $product, $data, $value);
        }
    }

    /**
     * Set attribute value (checkbox item)
     *
     * @param \XLite\Model\Repo\ARepo $repo    Repository
     * @param \XLite\Model\Product    $product Product
     * @param array                   $data    Data
     * @param boolean|int             $value   Item value
     *
     * @return \XLite\Model\AttributeValue\AttributeValueCheckbox
     */
    protected function setAttributeValueCheckboxItem(
        \XLite\Model\Repo\ARepo $repo,
        \XLite\Model\Product $product,
        array $data,
        $value
    ) {
        $attributeValue = $repo->findOneBy(
            array(
                'product'   => $product,
                'attribute' => $this,
                'value'     => $value,
            )
        );

        if (!$attributeValue) {
            $attributeValue = $this->createAttributeValue($product);
            $attributeValue->setValue($value);
        }

        $value = (int) $value;
        $attributeValue->setDefaultValue(isset($data['default'][$value]));
        foreach ($attributeValue::getModifiers() as $modifier => $options) {
            if (isset($data[$modifier]) && isset($data[$modifier][$value])) {
                $attributeValue->setModifier($data[$modifier][$value], $modifier);
            }
        }

        return $attributeValue;
    }

    /**
     * Set attribute value (default)
     *
     * @param \XLite\Model\Repo\ARepo $repo    Repository
     * @param \XLite\Model\Product    $product Product
     * @param mixed                   $data    Data
     *
     * @return \XLite\Model\AttributeValue\AttributeValueText
     */
    protected function setAttributeValueDefault(\XLite\Model\Repo\ARepo $repo, \XLite\Model\Product $product, $data)
    {
        $editable = is_array($data) && self::TYPE_TEXT === $this->getType() && isset($data['editable'])
            ? (bool) preg_match('/^1|yes|y|on$/iS', $data['editable'])
            : null;
        $value = is_array($data) ? $data['value'] : $data;
        if (is_array($value)) {
            $value = array_shift($value);
        }
        $delete = true;
        $attributeValue = null;

        if ('' !== $value || null !== $editable) {
            $attributeValue = $repo->findOneBy(array('product' => $product, 'attribute' => $this));

            if (!$attributeValue) {
                $attributeValue = $this->createAttributeValue($product);
                $delete = false;
            }

            $attributeValue->setValue($value);
            if (null !== $editable) {
                $attributeValue->setEditable($editable);
            }
        }

        if ($delete) {
            foreach ($repo->findBy(array('product' => $product, 'attribute' => $this)) as $data) {
                if (!$attributeValue || $attributeValue->getId() != $data->getId()) {
                    $repo->delete($data, false);
                }
            }
        }

        return $attributeValue;
    }

    // }}}

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set decimals
     *
     * @param integer $decimals
     * @return Attribute
     */
    public function setDecimals($decimals)
    {
        $this->decimals = $decimals;
        return $this;
    }

    /**
     * Get decimals
     *
     * @return integer 
     */
    public function getDecimals()
    {
        return $this->decimals;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set productClass
     *
     * @param \XLite\Model\ProductClass $productClass
     * @return Attribute
     */
    public function setProductClass(\XLite\Model\ProductClass $productClass = null)
    {
        $this->productClass = $productClass;
        return $this;
    }

    /**
     * Get productClass
     *
     * @return \XLite\Model\ProductClass 
     */
    public function getProductClass()
    {
        return $this->productClass;
    }

    /**
     * Set attributeGroup
     *
     * @param \XLite\Model\AttributeGroup $attributeGroup
     * @return Attribute
     */
    public function setAttributeGroup(\XLite\Model\AttributeGroup $attributeGroup = null)
    {
        $this->attributeGroup = $attributeGroup;
        return $this;
    }

    /**
     * Get attributeGroup
     *
     * @return \XLite\Model\AttributeGroup 
     */
    public function getAttributeGroup()
    {
        return $this->attributeGroup;
    }

    /**
     * Add attribute_options
     *
     * @param \XLite\Model\AttributeOption $attributeOptions
     * @return Attribute
     */
    public function addAttributeOptions(\XLite\Model\AttributeOption $attributeOptions)
    {
        $this->attribute_options[] = $attributeOptions;
        return $this;
    }

    /**
     * Get attribute_options
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAttributeOptions()
    {
        return $this->attribute_options;
    }

    /**
     * Set product
     *
     * @param \XLite\Model\Product $product
     * @return Attribute
     */
    public function setProduct(\XLite\Model\Product $product = null)
    {
        $this->product = $product;
        return $this;
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
     * Add attribute_properties
     *
     * @param \XLite\Model\AttributeProperty $attributeProperties
     * @return Attribute
     */
    public function addAttributeProperties(\XLite\Model\AttributeProperty $attributeProperties)
    {
        $this->attribute_properties[] = $attributeProperties;
        return $this;
    }

    /**
     * Get attribute_properties
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAttributeProperties()
    {
        return $this->attribute_properties;
    }
}
