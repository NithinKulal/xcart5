<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 *  Quick data
 */
class QuickData extends \XLite\Base\Singleton implements \Countable
{
    /**
     * Processing chunk length
     */
    const CHUNK_LENGTH = 100;

    /**
     * Memberships
     *
     * @var array
     */
    protected $memberships;

    /**
     * Update quick data
     *
     * @return void
     */
    public function update()
    {
        $i = 0;
        do {
            $processed = $this->updateChunk($i, static::CHUNK_LENGTH);
            if (0 < $processed) {
                \XLite\Core\Database::getEM()->clear();
            }
            $i += $processed;

        } while (0 < $processed);
    }

    /**
     * Update chunk
     *
     * @param integer $position Position OPTIONAL
     * @param integer $length   Length OPTIONAL
     *
     * @return integer
     */
    public function updateChunk($position = 0, $length = self::CHUNK_LENGTH)
    {
        $processed = 0;
        foreach (\XLite\Core\Database::getRepo('XLite\Model\Product')->findFrame($position, $length) as $product) {
            $this->updateProductDataInternal($product);
            $processed++;
        }

        if (0 < $processed) {
            \XLite\Core\Database::getEM()->flush();
        }

        return $processed;
    }

    /**
     * Update chunk
     *
     * @param integer $length Length OPTIONAL
     *
     * @return integer
     */
    public function updateUnprocessedChunk($length = self::CHUNK_LENGTH)
    {
        $processed = 0;
        foreach (\XLite\Core\Database::getRepo('XLite\Model\Product')->findUnprocessedChunk($length) as $product) {
            $this->updateProductDataInternal($product);
            $processed++;
        }
        \XLite\Core\Database::getEM()->flush();
        
        return $processed;
    }

    /**
     * Count
     *
     * @return integer
     */
    public function count()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Product')->count();
    }

    /**
     * Count unprocessed
     *
     * @return integer
     */
    public function countUnprocessed()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Product')->countUnprocessed();
    }

    /**
     * Update membership quick data
     *
     * @param \XLite\Model\Membership $membership Membership
     *
     * @return void
     */
    public function updateMembershipData(\XLite\Model\Membership $membership)
    {
        $i = 0;
        do {
            $processed = 0;
            $products = \XLite\Core\Database::getRepo('XLite\Model\Product')->findFrame($i, static::CHUNK_LENGTH);
            foreach ($products as $product) {
                $this->updateData($product, $membership);
                $processed++;
            }

            if (0 < $processed) {
                \XLite\Core\Database::getEM()->flush();
                \XLite\Core\Database::getEM()->clear();

                $membership = \XLite\Core\Database::getEM()->merge($membership);
            }
            $i += $processed;

        } while (0 < $processed);
    }

    /**
     * Update product quick data
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return void
     */
    public function updateProductData(\XLite\Model\Product $product)
    {
        $this->updateProductDataInternal($product);
        \XLite\Core\Database::getEM()->flush();
    }

    /**
     * Update product quick data
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return void
     */
    public function updateProductDataInternal(\XLite\Model\Product $product)
    {
        foreach ($this->getMemberships() as $membership) {
            if (!isset($membership) || \XLite\Core\Database::getEM()->contains($membership)) {
                $this->updateData($product, $membership);
            }
        }
        $product->updateSales();
        $product->setNeedProcess(false);
    }

    /**
     * Get memberships
     *
     * @param \XLite\Model\Product $product    Product
     * @param mixed                $membership Membership
     *
     * @return \XLite\Model\QuickData
     */
    public function updateData(\XLite\Model\Product $product, $membership)
    {
        $data = null;

        $quickData = $product->getQuickData() ?: array();

        foreach ($quickData as $qd) {
            if (($qd->getMembership()
                    && $membership
                    && $qd->getMembership()->getMembershipId() == $membership->getMembershipId()
                )
                || (!$qd->getMembership() && !$membership)
            ) {
                $data = $qd;
                break;
            }
        }

        if (!$data) {
            $data = new \XLite\Model\QuickData;
            $data->setProduct($product);
            $data->setMembership($membership);
            $product->addQuickData($data);
        }
        $data->setPrice($product->getQuickDataPrice());

        return $data;
    }

    /**
     * Detach products
     *
     * @param array $products Products
     *
     * @return void
     */
    protected function detachProducts(array $products)
    {
        foreach ($products as $product) {
            \XLite\Core\Database::getEM()->detach($product);
        }
    }

    /**
     * Get memberships
     *
     * @return array
     */
    protected function getMemberships()
    {
        if (!isset($this->memberships)) {
            $this->memberships = \XLite\Core\Database::getRepo('XLite\Model\Membership')->findAll();
            $this->memberships[] = null;
        }

        return $this->memberships;
    }
}
