<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Image;

/**
 * Category
 *
 * @Entity
 * @Table  (name="banner_rotation_image")
 */
class BannerRotationImage extends \XLite\Model\Base\Image
{
    /**
     * Relation to a BannerRotationSlide entity
     *
     * @var \XLite\Model\BannerRotationSlide
     *
     * @OneToOne   (targetEntity="XLite\Model\BannerRotationSlide", inversedBy="image")
     * @JoinColumn (name="banner_rotation_slide_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $bannerRotationSlide;

    /**
     * Alternative image text
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $alt = '';

    /**
     * Set alt
     *
     * @param string $alt
     * @return BannerRotationImage
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;
        return $this;
    }

    /**
     * Get alt
     *
     * @return string 
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * Set bannerRotationSlide
     *
     * @param \XLite\Model\BannerRotationSlide $bannerRotationSlide
     * @return BannerRotationImage
     */
    public function setBannerRotationSlide(\XLite\Model\BannerRotationSlide $bannerRotationSlide = null)
    {
        $this->bannerRotationSlide = $bannerRotationSlide;
        return $this;
    }

    /**
     * Get bannerRotationSlide
     *
     * @return \XLite\Model\BannerRotationSlide 
     */
    public function getBannerRotationSlide()
    {
        return $this->bannerRotationSlide;
    }
}
