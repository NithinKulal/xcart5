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

namespace XLite\Module\QSL\SpecialOffersBase\Controller\Customer;

/**
 * Individual special offer page.
 */
class SpecialOffer extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Define and set handler attributes; initialize handler
     *
     * @param array $params Handler params OPTIONAL
     */
    public function __construct(array $params = array())
    {
        parent::__construct($params);

        $this->params[] = 'offer_id';
    }

    /**
     * Check if current page is accessible.
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return $this->getOffer() && parent::checkAccess();
    }

    /**
     * Get the special offer that we are rendering the page for.
     *
     * @return \XLite\Module\QSL\SpecialOffersBase\Model\SpecialOffer
     */
    public function getOffer()
    {
        return $this->getOfferId()
            ? \XLite\Core\Database::getRepo('XLite\Module\QSL\SpecialOffersBase\Model\SpecialOffer')->find($this->getOfferId())
            : null;
    }

    /**
     * Return the page title (for the content area).
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->checkAccess()
            ? $this->getOffer()->getTitle()
            : '';
    }

    /**
     * Get meta description
     *
     * @return string
     */
    public function getMetaDescription()
    {
        $entity = $this->getOffer();

        return strip_tags(str_replace('&nbsp;', ' ', str_replace("\n", ' ', $entity->getDescription())));
    }

    /**
     * Return the model that we are rendering the page for.
     *
     * @return \XLite\Module\QSL\SpecialOffersBase\Model\SpecialOffer
     */
    public function getModelObject()
    {
        return $this->getOffer();
    }

    /**
     * Common method to determine current location.
     *
     * @return string
     */
    protected function getLocation()
    {
        return $this->checkAccess()
            ? $this->getOffer()->getTitle()
            : 'Page not found';
    }

    /**
     * Return the ID of the special offer that we are rendering the page for.
     *
     * @return integer
     */
    protected function getOfferId()
    {
        return intval(\XLite\Core\Request::getInstance()->offer_id);
    }

}
