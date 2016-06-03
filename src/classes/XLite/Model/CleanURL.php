<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * CleanURL
 *
 * @Entity
 * @Table (name="clean_urls",
 *      indexes={
 *          @Index (name="cleanURL", columns={"cleanURL"}),
 *      }
 * )
 */
class CleanURL extends \XLite\Model\AEntity
{
    /**
     * Unique id
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={"unsigned": true })
     */
    protected $id;

    /**
     * Relation to a product entity
     *
     * @var \XLite\Model\Product
     *
     * @ManyToOne  (targetEntity="XLite\Model\Product", inversedBy="cleanURLs")
     * @JoinColumn (name="product_id", referencedColumnName="product_id")
     */
    protected $product;

    /**
     * Relation to a category entity
     *
     * @var \XLite\Model\Category
     *
     * @ManyToOne  (targetEntity="XLite\Model\Category", inversedBy="cleanURLs")
     * @JoinColumn (name="category_id", referencedColumnName="category_id")
     */
    protected $category;

    /**
     * Clean URL
     *
     * @var string
     *
     * @Column (type="string", length=255, nullable=true)
     */
    protected $cleanURL;

    /**
     * Set entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return void
     */
    public function setEntity($entity)
    {
        $entityType = \XLite\Model\Repo\CleanURL::getEntityType($entity);

        $method = 'set' . \XLite\Core\Converter::convertToCamelCase($entityType);
        if (method_exists($this, $method)) {
            $this->{$method}($entity);
        }
    }

    /**
     * Get entity
     *
     * @return \XLite\Model\AEntity
     */
    public function getEntity()
    {
        $entity = null;

        foreach (\XLite\Model\Repo\CleanURL::getEntityTypes() as $type) {
            $method = 'get' . \XLite\Core\Converter::convertToCamelCase($type);
            if (method_exists($this, $method)) {
                $entity = $this->{$method}();

                if ($entity) {
                    break;
                }
            }
        }

        return $entity;
    }

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
     * Set cleanURL
     *
     * @param string $cleanURL
     * @return CleanURL
     */
    public function setCleanURL($cleanURL)
    {
        $this->cleanURL = $cleanURL;
        return $this;
    }

    /**
     * Get cleanURL
     *
     * @return string 
     */
    public function getCleanURL()
    {
        return $this->cleanURL;
    }

    /**
     * Set product
     *
     * @param \XLite\Model\Product $product
     * @return CleanURL
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
     * Set category
     *
     * @param \XLite\Model\Category $category
     * @return CleanURL
     */
    public function setCategory(\XLite\Model\Category $category = null)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Get category
     *
     * @return \XLite\Model\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }
}
