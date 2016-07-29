<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return function () {
    $repo = \XLite\Core\Database::getRepo('XLite\Model\Config');
    $qb = $repo->createPureQueryBuilder()
        ->update()
        ->set('c.type', ':newType')
        ->where('c.type = :oldType')
        ->orWhere('c.type = :oldType2')
        ->setParameter('oldType', 'XLite\View\FormField\Input\Text\Float')
        ->setParameter('oldType2', '\XLite\View\FormField\Input\Text\Float')
        ->setParameter('newType', 'XLite\View\FormField\Input\Text\FloatInput');

    $qb->getQuery()->execute();

    \XLite\Core\Database::getEM()->flush();
    \XLite\Core\Config::updateInstance();
};
