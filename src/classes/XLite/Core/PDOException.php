<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * Extended PDO exception
 */
class PDOException extends \PDOException
{
    /**
     * Constructor
     *
     * @param \PDOException $e      PDO exception
     * @param string        $query  SQL query OPTIONAL
     * @param array         $params SQL query parameters OPTIONAL
     *
     * @return void
     */
    public function __construct(\PDOException $e, $query = null, array $params = array())
    {
        $code = $e->getCode();
        $message = $e->getMessage();

        // Remove user credentials
        if (
            strstr($message, 'SQLSTATE[')
            && preg_match('/SQLSTATE\[(\w+)\] \[(\w+)\] (.*)/', $message, $matches)
        ) {
            $code = 'HT000' == $matches[1] ? $matches[2] : $matches[1];
            $message = $matches[3];
        }

        // Add additional information
        if ($query) {
            $message .= PHP_EOL . 'SQL query: ' . $query;
        }

        if ($params) {
            $message .= PHP_EOL . 'SQL query parameters: ' . var_export($params, true);
        }

        $this->code = intval($code);
        $this->message = $message;
    }
}
