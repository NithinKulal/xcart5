<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * Statement
 */
class Statement extends \Doctrine\DBAL\Statement
{

    /**
     * Executes the statement with the currently bound parameters
     *
     * @param array $params Parameters OPTIONAL
     *
     * @return boolean
     * @throws \XLite\Core\PDOException
     */
    public function execute($params = null)
    {
        try {
            $result = parent::execute($params);

        } catch (\PDOException $e) {
            $sql = $this->sql;
            if (!$sql && is_object($this->stmt) && $this->stmt->queryString) {
                $sql = $this->stmt->queryString;
            }

            throw new \XLite\Core\PDOException($e, $sql, $params ?: $this->params);
        }

        return $result;
    }
}
