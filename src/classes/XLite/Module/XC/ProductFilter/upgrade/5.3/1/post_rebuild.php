<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return function()
{
    $em = \XLite\Core\Database::getEM();
    $query = $em->createQuery('SELECT c FROM XLite\Model\Config c WHERE c.name = :attribute_type AND c.category = :config_category')
        ->setParameter('attribute_type', 'attributes_soting_type')
        ->setParameter('config_category', 'XC\ProductFilter')
        ->setMaxResults(1);
    try {
        if ($attributes_sorting_type_entry = $query->getSingleResult()) {
            $attributes_sorting_type_entry->setName('attributes_sorting_type');
            $em->flush($attributes_sorting_type_entry);
        }
    } catch (\Doctrine\ORM\NoResultException $exception) {}
};
