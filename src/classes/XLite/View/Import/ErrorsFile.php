<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Import;

/**
 * Failed section
 */
class ErrorsFile extends \XLite\View\Import\Failed
{
    /**
     * Hack for flexy, \r \n symbols
     */
    protected function getEOL()
    {
        return PHP_EOL;
    }

    /**
     * Get human readable message type
     *
     * @param string $type
     *
     * @return string
     */
    protected function getMessageType($type)
    {
        return 'E' === $type
            ? static::t('Errors')
            : static::t('Warnings');
    }

    /**
     * Get human readable message type
     *
     * @param string $file File
     *
     * @return array
     */
    protected function getMessagesGroups($file)
    {
        $errorsAll = $this->getErrorsGroups($file);

        $errors = array_filter($errorsAll, function($item) {
            return $item['type'] === 'E';
        });

        $warnings = array_filter($errorsAll, function($item) {
            return $item['type'] === 'W';
        });

        return array(
            'E' => $errors,
            'W' => $warnings,
        );
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'import/errors-file.twig';
    }
}
