<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Concierge\View\Model;

use XLite\Module\XC\Concierge\Core\Mediator;
use XLite\Module\XC\Concierge\Core\Track\Category as CategoryTrack;

/**
 * Category view model
 */
abstract class Category extends \XLite\View\Model\Category implements \XLite\Base\IDecorator
{
    protected function postprocessSuccessAction()
    {
        parent::postprocessSuccessAction();

        $action = $this->currentAction;
        if (in_array($action, ['create', 'update', 'modify'], true)) {
            Mediator::getInstance()->addMessage(
                new CategoryTrack(
                    $action === 'create' ? 'Create Category' : 'Update Category',
                    $this->getModelObject()
                )
            );
        }
    }
}
