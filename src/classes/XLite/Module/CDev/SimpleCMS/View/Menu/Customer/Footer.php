<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SimpleCMS\View\Menu\Customer;

/**
 * Footer menu
 */
class Footer extends \XLite\View\Menu\Customer\Footer implements \XLite\Base\IDecorator
{
    /**
     * Define menu items
     *
     * @return array
     */
    protected function defineItems()
    {
        $menu = array();

        $cnd = new \XLite\Core\CommonCell;
        $cnd->type = \XLite\Module\CDev\SimpleCMS\Model\Menu::MENU_TYPE_FOOTER;
        $cnd->enabled = true;
        $cnd->visibleFor = array(
            'AL',
            (\XLite\Core\Auth::getInstance()->isLogged() ? 'L' : 'A'),
        );

        $menus = \XLite\Core\Database::getRepo('XLite\Module\CDev\SimpleCMS\Model\Menu')->getMenusPlainList($cnd);
        foreach ($menus as $menuItem) {
            $menu[] = array(
                'id'         => $menuItem->getId(),
                'label'      => $menuItem->getName(),
                'depth'      => $menuItem->getDepth(),
                'controller' => $menuItem->getLinkController(),
                'url'        => $menuItem->getUrl(),
            );
        }

        return $menu ?: parent::defineItems();
    }

    /**
     * Previous menu depth
     *
     * @var integer
     */
    protected $prevMenuDepth = 0;

    /**
     * Is first element
     *
     * @var integer
     */
    protected $isFirst = true;

    /**
     * Return the CSS files for the menu
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/SimpleCMS/css/footer_menu.css';

        return $list;
    }
    
    /**
     * Return the JS files for the menu
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/CDev/SimpleCMS/js/jquery_footer.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/SimpleCMS/footer_menu.twig';
    }

    /**
     * Return next menu level or not
     *
     * @param integer $menuDepth Level depth
     *
     * @return boolean
     */
    protected function isLevelUp($menuDepth)
    {
        $result = false;
        if ($menuDepth > $this->prevMenuDepth) {
            $result = true; 
            $this->prevMenuDepth = $menuDepth;
        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * Return previous menu level or not
     *
     * @param integer $menuDepth Level depth
     *
     * @return boolean
     */
    protected function isLevelBelow($menuDepth)
    {
        $result = false;
        if ($menuDepth < $this->prevMenuDepth) {
            $result = true; 
            $this->prevMenuDepth = $menuDepth;
        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * Return is level changed
     *
     * @return boolean
     */
    protected function closeMenuList($menuDepth = 0)
    {
        $result = '';
        for ($i = $menuDepth;$i<$this->prevMenuDepth;$i++) {
            $result .= '</ul></li>';
        }
        $this->prevMenuDepth = $menuDepth;

        return $result;
    }

    /**
     * Return is first element
     *
     * @return boolean
     */
    protected function isFirstElement()
    {
        $result = $this->isFirst;

        return $result;
    }

    /**
     * Return is higher element
     *
     * @return boolean
     */
    protected function isHigherElement($menuDepth = 0)
    {
        $this->isFirst = false;

        return (0 == $menuDepth)?true:false;
    }
}

