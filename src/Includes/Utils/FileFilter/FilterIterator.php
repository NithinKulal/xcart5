<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Utils\FileFilter;

/**
 * FilterIterator
 *
 * @package XLite
 */
class FilterIterator extends \FilterIterator
{
    /**
     * Pattern to filter paths
     *
     * @var string
     */
    protected $pattern;

    /**
     * List of filtering callbacks
     *
     * @var array
     */
    protected $callbacks = array();


    /**
     * Constructor
     *
     * @param \Iterator $iterator iterator to use
     * @param string    $pattern  pattern to filter paths
     *
     * @return void
     */
    public function __construct(\Iterator $iterator, $pattern = null)
    {
        parent::__construct($iterator);

        $this->pattern = $pattern;
    }

    /**
     * Add callback to filter files
     *
     * @param array $callback Callback to register
     *
     * @return void
     */
    public function registerCallback(array $callback)
    {
        if (!is_callable($callback)) {
            \Includes\ErrorHandler::fireError('Filtering callback is not valid');
        }

        $this->callbacks[] = $callback;
    }

    /**
     * Check if current element of the iterator is acceptable through this filter
     *
     * @return bool
     */
    public function accept()
    {
        if (!($result = !isset($this->pattern))) {
            $result = preg_match($this->pattern, $this->getPathname());
        }

        if (!empty($this->callbacks)) {

            while ($result && (list(, $callback) = each($this->callbacks))) {
                $result = call_user_func_array($callback, array($this));
            }

            reset($this->callbacks);
        }

        return $result;
    }
}
