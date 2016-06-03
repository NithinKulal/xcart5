<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Model profile selector controller
 */
class ModelProfileSelector extends \XLite\Controller\Admin\ModelSelector\AModelSelector
{
    const MAX_PROFILE_COUNT = 10;

    /**
     * Define specific data structure which will be sent in the triggering event (model.selected)
     *
     * @param type $item
     *
     * @return string
     */
    protected function defineDataItem($item)
    {
        $data = parent::defineDataItem($item);
        $data['selected_value'] = $item->getName();
        $data['selected_login'] = '&lt;' . $item->getLogin() . '&gt;';

        return $data;
    }

    /**
     * Get data of the model request
     *
     * @return \Doctrine\ORM\PersistentCollection | array
     */
    protected function getData()
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->{\XLite\Model\Repo\Profile::SEARCH_PATTERN} = $this->getKey();
        $cnd->{\XLite\Model\Repo\Profile::SEARCH_LOGIN}   = $this->getKey();
        $cnd->{\XLite\Model\Repo\Profile::P_LIMIT}   = array(0, static::MAX_PROFILE_COUNT);

        return \XLite\Core\Database::getRepo('XLite\Model\Profile')->search($cnd);
    }

    /**
     * Format model text presentation
     *
     * @param mixed $item Model item
     *
     * @return string
     */
    protected function formatItem($item)
    {
        return $item->getName() . ' &lt;' . $item->getLogin() . '&gt;';
    }

    /**
     * Defines the model value
     *
     * @param mixed $item Model item
     *
     * @return string
     */
    protected function getItemValue($item)
    {
        return $item->getProfileId();
    }
}
