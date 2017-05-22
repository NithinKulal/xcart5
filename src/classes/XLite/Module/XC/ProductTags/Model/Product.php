<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductTags\Model;

/**
 * Product
 */
class Product extends \XLite\Model\Product implements \XLite\Base\IDecorator
{
    /**
     * Tags
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ManyToMany (targetEntity="XLite\Module\XC\ProductTags\Model\Tag", inversedBy="products")
     * @JoinTable (name="product_tags",
     *      joinColumns={@JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")},
     *      inverseJoinColumns={@JoinColumn (name="tag_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     * @OrderBy({"position" = "ASC"})
     */
    protected $tags;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Clone
     *
     * @return \XLite\Model\AEntity
     */
    public function cloneEntity()
    {
        /** @var static $product */
        $product = parent::cloneEntity();

        /** @var \XLite\Module\XC\ProductTags\Model\Tag $tag */
        foreach ($this->getTags() as $tag) {
            $tag->addProducts($product);
            $product->addTags($tag);
        }

        return $product;
    }

    /**
     * Add tags
     *
     * @param \XLite\Module\XC\ProductTags\Model\Tag $tags
     *
     * @return Product
     */
    public function addTags(\XLite\Module\XC\ProductTags\Model\Tag $tags)
    {
        $this->tags[] = $tags;

        return $this;
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param \XLite\Module\XC\ProductTags\Model\Tag[] $tags
     */
    public function addTagsByTags($tags)
    {
        foreach ($tags as $tag) {
            if (!$this->hasTagByTag($tag)) {
                $this->addTags($tag);
            }
        }
    }

    /**
     * @param \XLite\Module\XC\ProductTags\Model\Tag[] $tags
     */
    public function removeTagsByTags($tags)
    {
        foreach ($tags as $tag) {
            if ($this->hasTagByTag($tag)) {
                $this->getTags()->removeElement($tag);
            }
        }
    }

    public function replaceTagsByTags($tags)
    {
        $ids = array_map(function ($item) {
            /** @var \XLite\Module\XC\ProductTags\Model\Tag $item */
            return (int) $item->getId();
        }, $tags);

        $toRemove = [];
        foreach ($this->getTags() as $tag) {
            if (!in_array((int) $tag->getId(), $ids, true)) {
                $toRemove[] = $tag;
            }
        }

        $this->addTagsByTags($tags);
        $this->removeTagsByTags($toRemove);
    }

    /**
     * @param \XLite\Module\XC\ProductTags\Model\Tag $tag
     *
     * @return boolean
     */
    public function hasTagByTag($tag)
    {
        return (boolean) $this->getTagByTag($tag);
    }

    /**
     * @param \XLite\Module\XC\ProductTags\Model\Tag $tag
     *
     * @return mixed|null
     */
    public function getTagByTag($tag)
    {
        foreach ($this->getTags() as $tagObject) {
            if ((int) $tag->getId() === (int) $tagObject->getId()) {
                return $tagObject;
            }
        }

        return null;
    }
}
