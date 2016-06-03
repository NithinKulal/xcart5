<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * X-Cart
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the software license agreement
 * that is bundled with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@x-cart.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not modify this file if you wish to upgrade X-Cart to newer versions
 * in the future. If you wish to customize X-Cart for your needs please
 * refer to http://www.x-cart.com/ for more information.
 *
 * @category  X-Cart 5
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @link      http://www.x-cart.com/
 */
namespace XLite\Module\QSL\SpecialOffersBase\View;

/**
 * Brand page widget.
 *
 * @ListChild (list="center", zone="customer")
 */
class SpecialOfferPage extends \XLite\View\AView
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'special_offer';

        return $result;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/QSL/SpecialOffersBase/offer_page/styles.css';

        return $list;
    }

    /**
     * Check if the special offer has the image.
     *
     * @return boolean
     */
    public function hasImage()
    {
        return !is_null($this->getImage());
    }

    /**
     * Get the special offer image.
     *
     * @return \XLite\Model\Image
     */
    public function getImage()
    {
        return $this->getOffer()->getImage();
    }

    /**
     * Get the description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->getOffer()->getDescription();
    }

    /**
     * Get the title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getOffer()->getTitle();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/SpecialOffersBase/offer_page/body.tpl';
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getOffer();
    }
}
