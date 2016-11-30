<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\Base;

/**
 * Common translation repository
 */
class Translation extends \XLite\Model\Repo\ARepo
{
    /**
     * Find one by record
     *
     * @param array                $data   Record
     * @param \XLite\Model\AEntity $parent Parent model OPTIONAL
     *
     * @return \XLite\Model\AEntity|void
     */
    public function findOneByRecord(array $data, \XLite\Model\AEntity $parent = null)
    {
        if (empty($data['code'])) {
            $data['code'] = \XLite\Model\Base\Translation::DEFAULT_LANGUAGE;
        }

        return isset($parent) ? $parent->getTranslation($data['code']) : parent::findOneByRecord($data, $parent);
    }

    /**
     * Get repository type
     *
     * @return string
     */
    public function getRepoType()
    {
        return isset($this->_class->associationMappings['owner'])
            ? \XLite\Core\Database::getRepo($this->_class->associationMappings['owner']['targetEntity'])->getRepoType()
            : parent::getRepoType();
    }

    /**
     * Get used language codes 
     * 
     * @return array
     */
    public function getUsedLanguageCodes()
    {
        $result = array();

        foreach ($this->defineGetUsedLanguageCodesQuery()->getResult() as $row) {
            $result[] = $row['code'];
        }

        return $result;
    }

    /**
     * Define query for getUsedLanguageCodes() methods
     * 
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineGetUsedLanguageCodesQuery()
    {
        $qb = $this->createQueryBuilder();

        return $qb->select('DISTINCT ' . $qb->getMainAlias() . '.code');
    }
}
