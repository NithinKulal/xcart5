<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Sitemap\View\Sitemap;

/**
 *  This widget draws a tree's branch
 *
 * @Decorator\Depend ("CDev\SimpleCMS")
 */
class BranchSimpleCMS extends \XLite\Module\XC\Sitemap\View\Sitemap\Branch implements \XLite\Base\IDecorator
{

    /**
     * Page types
     */
    const PAGE_STATIC_PAGE = 'A';

    /**
     * Return existence of children of this category
     *
     * @param string  $type Page type
     * @param integer $id   Page ID
     *
     * @return boolean
     */
    protected function hasChild($type, $id)
    {
        if (static::PAGE_STATIC_PAGE == $type) {
            $cnt = \XLite\Core\Database::getRepo('XLite\Module\CDev\SimpleCMS\Model\Page')
                ->countBy(array('enabled' => true));
            $result = $cnt > 0;

        } else {
            $result = parent::hasChild($type, $id);
        }

        return $result;
    }

    /**
     * Get children
     * 
     * @param string  $type Page type
     * @param integer $id   Page ID
     *  
     * @return array
     */
    protected function getChildren($type, $id)
    {
        if (static::PAGE_STATIC_PAGE == $type) {
            $result = array();
            if (!$id) {
                $pages = \XLite\Core\Database::getRepo('XLite\Module\CDev\SimpleCMS\Model\Page')->findBy(array('enabled' => true));
                foreach ($pages as $page) {
                    $result[] = array(
                        'type' => static::PAGE_STATIC_PAGE,
                        'id'   => $page->getId(),
                        'name' => $page->getName(),
                        'url'  => static::buildURL('page', null, array('id' => $page->getId())),
                    );
                }
            }

        } else {
            $result = parent::getChildren($type, $id);

            if (empty($type)) {
                $result[] = array(
                    'type' => static::PAGE_STATIC_PAGE,
                    'id'   => 0,
                    'name' => static::t('Static pages'),
                );
            }
        }

        return $result;
    }

}
