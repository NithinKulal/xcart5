<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Model;

/**
 * Product view model
 */
class Product extends \XLite\View\Model\AModel
{
    /**
     * Schema default
     *
     * @var array
     */
    protected $schemaDefault = array(
        'sku' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\SKU',
            self::SCHEMA_LABEL    => 'SKU',
            self::SCHEMA_REQUIRED => false,
        ),
        'name' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Product Name',
            self::SCHEMA_REQUIRED => true,
        ),
        'categories' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Select\Categories',
            self::SCHEMA_LABEL    => 'Category',
            self::SCHEMA_REQUIRED => false,
        ),
        'images' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\FileUploader\Image',
            self::SCHEMA_LABEL    => 'Images',
            self::SCHEMA_REQUIRED => false,
            \XLite\View\FormField\FileUploader\Image::PARAM_MULTIPLE => true,
        ),
        'memberships' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Select\Memberships',
            self::SCHEMA_LABEL    => 'Memberships',
            self::SCHEMA_REQUIRED => false,
            self::SCHEMA_COMMENT  => 'Do not select anything if you want to make the product visible to all customers.',
        ),
        'taxClass' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Select\TaxClass',
            self::SCHEMA_LABEL    => 'Tax class',
            self::SCHEMA_REQUIRED => false,
        ),
        'price' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\Price',
            self::SCHEMA_LABEL    => 'Price',
            self::SCHEMA_REQUIRED => false,
        ),
        'qty' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\ProductQuantity',
            self::SCHEMA_LABEL    => 'Quantity in stock',
            self::SCHEMA_REQUIRED => false,
        ),
        'weight' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\Weight',
            self::SCHEMA_LABEL    => 'Weight X',
            self::SCHEMA_REQUIRED => false,
        ),
        'shippable' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Select\YesNo',
            self::SCHEMA_LABEL    => 'Requires shipping',
            self::SCHEMA_REQUIRED => false,
        ),
        'useSeparateBox' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Select\Product\UseSeparateBox',
            self::SCHEMA_LABEL    => 'Ship in a separate box',
            self::SCHEMA_REQUIRED => false,
            self::SCHEMA_DEPENDENCY => array(
                self::DEPENDENCY_SHOW => array(
                    'shippable' => array(\XLite\View\FormField\Select\YesNo::YES),
                ),
            ),
        ),
        'enabled' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\Enabled',
            self::SCHEMA_LABEL    => 'Available for sale',
            self::SCHEMA_REQUIRED => false,
        ),
        'arrivalDate' => array(
            self::SCHEMA_CLASS    => 'XLite\View\DatePicker',
            self::SCHEMA_LABEL    => 'Arrival date',
            \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => false,
            self::SCHEMA_REQUIRED => false,
        ),
        'brief_description' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Textarea\Advanced',
            self::SCHEMA_LABEL    => 'Brief description',
            self::SCHEMA_REQUIRED => false,
            \XLite\View\FormField\Textarea\Advanced::PARAM_STYLE => 'product-description',
        ),
        'description' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Textarea\Advanced',
            self::SCHEMA_LABEL    => 'Full description',
            self::SCHEMA_REQUIRED => false,
            \XLite\View\FormField\Textarea\Advanced::PARAM_STYLE => 'product-description',
        ),
        'meta_title' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Product page title',
            self::SCHEMA_REQUIRED => false,
            self::SCHEMA_COMMENT  => 'Leave blank to use product name as Page Title.',
        ),
        'meta_tags' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Meta keywords',
            self::SCHEMA_REQUIRED => false,
        ),
        'meta_desc_type' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Select\MetaDescriptionType',
            self::SCHEMA_LABEL    => 'Meta description',
            self::SCHEMA_REQUIRED => false,
        ),
        'meta_desc' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Textarea\Simple',
            self::SCHEMA_LABEL    => '',
            \XLite\View\FormField\AFormField::PARAM_USE_COLON => false,
            self::SCHEMA_REQUIRED => true,
            self::SCHEMA_DEPENDENCY => array(
                self::DEPENDENCY_SHOW => array (
                    'meta_desc_type' => array('C'),
                )
            ),
        ),
        'cleanURL' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\CleanURL',
            self::SCHEMA_LABEL    => 'Clean URL',
            self::SCHEMA_REQUIRED => false,
            \XLite\View\FormField\AFormField::PARAM_LABEL_HELP => 'Human readable and SEO friendly web address for the page.',
            \XLite\View\FormField\Input\Text\CleanURL::PARAM_OBJECT_CLASS_NAME => 'XLite\Model\Product',
            \XLite\View\FormField\Input\Text\CleanURL::PARAM_OBJECT_ID_NAME    => 'product_id',
            \XLite\View\FormField\Input\Text\CleanURL::PARAM_ID                => 'cleanurl',
            \XLite\View\FormField\Input\Text\CleanURL::PARAM_EXTENSION         => \XLite\Model\Repo\CleanURL::CLEAN_URL_DEFAULT_EXTENSION,
        ),
    );

    /**
     * Return current model ID
     *
     * @return integer
     */
    public function getModelId()
    {
        return \XLite\Core\Request::getInstance()->product_id;
    }

    /**
     * getDefaultFieldValue
     *
     * @param string $name Field name
     *
     * @return mixed
     */
    public function getDefaultFieldValue($name)
    {
        $value = parent::getDefaultFieldValue($name);

        // Categories can be provided via request
        if ('categories' === $name) {
            $categoryId = \XLite\Core\Request::getInstance()->category_id;
            $value = $categoryId ? array(
                \XLite\Core\Database::getRepo('XLite\Model\Category')->find($categoryId),
            ) : $value;
        }

        return $value;
    }

    /**
     * This object will be used if another one is not passed
     *
     * @return \XLite\Model\Category
     */
    protected function getDefaultModelObject()
    {
        $model = $this->getModelId()
            ? \XLite\Core\Database::getRepo('XLite\Model\Product')->find($this->getModelId())
            : null;

        return $model ?: new \XLite\Model\Product;
    }

    /**
     * Return fields' saved values for current form (saved data itself)
     *
     * @param string $name Parameter name OPTIONAL
     *
     * @return array
     */
    public function getSavedData($name = null)
    {
        return 'images' == $name
            ? null
            : parent::getSavedData($name);
    }

    /**
     * Defines the category products links collection
     *
     * @param \XLite\Model\Product $product     Product
     * @param array                $categories  Categories
     * @param array                $categoryIds Category IDs to filter categories
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    protected function getCategoryProducts($product, $categories, $categoryIds)
    {
        $links = array();
        foreach ($categories as $category) {
            if (in_array($category->getCategoryId(), $categoryIds)) {
                $links[] = new \XLite\Model\CategoryProducts(
                    array(
                        'category'    => $category,
                        'product'     => $product,
                        'orderby'     => $product->getOrderby($category->getCategoryId()),
                    )
                );
            }
        }

        return new \Doctrine\Common\Collections\ArrayCollection($links);
    }

    /**
     * getFieldBySchema
     *
     * @param string $name Field name
     * @param array  $data Field description
     *
     * @return \XLite\View\FormField\AFormField
     */
    protected function getFieldBySchema($name, array $data)
    {
        $field = null;

        if ('meta_title' === $name) {
            $data[static::SCHEMA_PLACEHOLDER] = static::t('Default');
        }

        if ('weight' == $name) {
            $data[static::SCHEMA_LABEL_PARAMS] = array(
                'symbol' => \XLite\Core\Config::getInstance()->Units->weight_symbol,
            );
        }

        switch ($name) {
            case 'qty':
                if ($this->getModelObject()
                    && $this->getModelObject()->getInventoryEnabled()
                ) {
                    $field = parent::getFieldBySchema($name, $data);
                }
                break;

            default:
                $field = parent::getFieldBySchema($name, $data);
                break;
        }

        return $field;
    }

    /**
     * Preparing data for qty param
     *
     * @param array $data Field description
     *
     * @return array
     */
    protected function prepareFieldParamsQty($data)
    {
        if ('product' === \XLite\Core\Request::getInstance()->target
            && $this->get('entity')->isPersistent()
        ) {
            $data[self::SCHEMA_LINK_HREF] = $this->buildURL(
                'product',
                '',
                array(
                    'product_id'    => $this->getProductId(),
                    'page'          => 'inventory',
                )
            );
            $data[self::SCHEMA_LINK_TEXT] = 'Inventory tracking options';
        }
        return $data;
    }

    /**
     * Populate model object properties by the passed data
     *
     * @param array $data Data to set
     *
     * @return void
     */
    protected function setModelProperties(array $data)
    {
        if ($this->isValid()) {
            $this->updateModelProperties($data);
        }
    }

    /**
     * Populate model object properties by the passed data.
     * Specific wrapper for setModelProperties method.
     *
     * @param array $data Data to set
     *
     * @return void
     */
    protected function updateModelProperties(array $data)
    {
        $categoryIds = isset($data['categories']) ? array_map('intval', $data['categories']) : array();
        unset($data['categories']);

        $memberships = isset($data['memberships']) ? $data['memberships'] : array();
        unset($data['memberships']);

        // Flag variables
        foreach (array('shippable', 'useSeparateBox') as $value) {
            if (isset($data[$value]) && is_string($data[$value])) {
                $data[$value] = 'Y' === $data[$value];
            }
        }

        if (isset($data['useSeparateBox']) && $data['useSeparateBox']) {
            foreach (array('boxLength', 'boxWidth', 'boxHeight', 'itemsPerBox') as $var) {
                $data[$var] = $this->getPostedData($var);
            }
        }

        if (in_array('arrivalDate', array_keys($data))) {

            // If $data has 'arrivalDate' key...

            if (isset($data['arrivalDate']) && !is_numeric($data['arrivalDate'])) {
                // Try to get timestamp
                $time = \XLite\Core\Converter::time();
                $data['arrivalDate'] = mktime(0, 0, 0, date('m', $time), date('j', $time), date('Y', $time));
            }

            if (is_null($data['arrivalDate'])) {
                // Remove 'arrivalDate' from model parameters if value is null (wrong timestamp)
                unset($data['arrivalDate']);
                \XLite\Core\TopMessage::addWarning('Wrong value specified for arrival date field. The field was not updated.');
            }
        }

        if (isset($data['productClass'])) {
            $data['productClass'] = \XLite\Core\Database::getRepo('XLite\Model\ProductClass')
                ->find($data['productClass']);
        }

        if (isset($data['taxClass'])) {
            $data['taxClass'] = \XLite\Core\Database::getRepo('XLite\Model\TaxClass')->find($data['taxClass']);
        }

        if (isset($data['qty'])) {
            if ($this->getModelObject()) {
                $this->getModelObject()->setAmount($data['qty']);
            }
            unset($data['qty']);
        }

        parent::setModelProperties($data);

        /** @var \XLite\Model\Product $model */
        $model = $this->getModelObject();

        $isNew = !$model->isPersistent();
        $model->update();

        if ($isNew) {
            \XLite\Core\Database::getRepo('XLite\Model\Attribute')->generateAttributeValues($model);
        }

        // Update product categories
        $this->updateProductCategories($model, $categoryIds);

        // Update SKU
        if(
            !trim($model->getSku())
            || null === $model->getSku()
        ) {
            $model->setSku(\XLite\Core\Database::getRepo('XLite\Model\Product')->generateSKU($model));
        }

        // Update memberships
        foreach ($model->getMemberships() as $membership) {
            $membership->getProducts()->removeElement($model);
        }

        $model->getMemberships()->clear();

        if (null !== $memberships && $memberships) {
            // Add new links
            foreach ($memberships as $mid) {
                $membership = \XLite\Core\Database::getRepo('XLite\Model\Membership')->find($mid);
                if ($membership) {
                    $model->addMemberships($membership);
                    $membership->addProduct($model);
                }
            }
        }

        // Set the controller model product
        $this->setProduct($model);
    }

    /**
     * Update product categories
     *
     * @param \XLite\Model\Product $model       Product model
     * @param array                $categoryIds List of IDs of new categories
     *
     * @return void
     */
    protected function updateProductCategories($model, $categoryIds)
    {
        // List of old category IDs
        $oldCategoryIds = array();

        // Get old category IDs list
        $oldCategoryProducts = $model->getCategoryProducts()->toArray();

        if (!empty($oldCategoryProducts)) {
            $categoriesToDelete = array();

            foreach ($oldCategoryProducts as $cp) {
                $oldCategoryIds[] = $cp->getCategory()->getCategoryId();

                if (!in_array($cp->getCategory()->getCategoryId(), $categoryIds)) {
                    // Add old category to the remove queue
                    $categoriesToDelete[] = $cp;
                }
            }

            if ($categoriesToDelete) {
                // Remove links between product and old categories
                \XLite\Core\Database::getRepo('XLite\Model\CategoryProducts')->deleteInBatch(
                    $categoriesToDelete
                );
            }
        }

        // Get list of category IDs which must be added to product
        $categoriesToAdd = array_diff($categoryIds, $oldCategoryIds);

        // Get list of categories (entities) from category IDs
        $categories = \XLite\Core\Database::getRepo('XLite\Model\Category')->findByIds($categoryIds);

        // Get list of category products
        $categoryProducts = $this->getCategoryProducts($model, $categories, $categoriesToAdd);

        if ($categoryProducts) {
            // Update category products list
            \XLite\Core\Database::getRepo('XLite\Model\Product')->update(
                $model,
                array('categoryProducts' => $categoryProducts)
            );
        }
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return 'XLite\View\Form\Model\Product';
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        $label = $this->getModelObject()->getId() ? 'Update product' : 'Add product';
        $result['submit'] = new \XLite\View\Button\Submit(
            array(
                \XLite\View\Button\AButton::PARAM_LABEL => $label,
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                \XLite\View\Button\AButton::PARAM_STYLE => 'action',
            )
        );

        if ($this->getModelObject()->isPersistent()) {
            $url = $this->buildURL(
                'product',
                'clone',
                array(
                    'product_id' => $this->getModelObject()->getId(),
                )
            );
            $result['clone-product'] = new \XLite\View\Button\Link(
                array(
                    \XLite\View\Button\AButton::PARAM_LABEL => 'Clone this product',
                    \XLite\View\Button\AButton::PARAM_STYLE => 'model-button always-enabled',
                    \XLite\View\Button\Link::PARAM_LOCATION => $url,
                )
            );

            $url = $this->buildProductPreviewURL($this->getModelObject()->getId());
            $result['preview-product'] = new \XLite\View\Button\SimpleLink(
                array(
                    \XLite\View\Button\AButton::PARAM_LABEL => 'Preview product page',
                    \XLite\View\Button\AButton::PARAM_STYLE => 'model-button link action',
                    \XLite\View\Button\Link::PARAM_BLANK    => true,
                    \XLite\View\Button\Link::PARAM_LOCATION => $url,
                )
            );
        }

        return $result;
    }

    /**
     * Add top message
     *
     * @return void
     */
    protected function addDataSavedTopMessage()
    {
        if ('create' !== $this->currentAction) {
            \XLite\Core\TopMessage::addInfo('The product has been updated');

        } else {
            \XLite\Core\TopMessage::addInfo('The product has been added');
        }
    }
}
