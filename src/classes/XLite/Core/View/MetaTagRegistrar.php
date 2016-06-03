<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Core\View;

use RuntimeException;
use XLite\Core\Layout;


/**
 * Meta tag registrar allows widgets to register additional meta tags for the page.
 */
class MetaTagRegistrar implements MetaTagRegistrarInterface
{
    /** @var array */
    protected $tagBuffers = [];

    protected $layout;

    public function __construct()
    {
        $this->layout = Layout::getInstance();
    }

    /**
     * {@inheritdoc}
     */
    public function register(array $tags)
    {
        if (!empty($this->tagBuffers)) {
            foreach ($this->tagBuffers as &$buffer) {
                $buffer = array_merge($buffer, $tags);
            }
        } else {
            $this->layout->registerMetaTags($tags);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function startBuffering()
    {
        array_push($this->tagBuffers, []);
    }

    /**
     * {@inheritdoc}
     */
    public function stopBuffering()
    {
        if (empty($this->tagBuffers)) {
            throw new RuntimeException('Unbalanced startBuffering()/stopBuffering() calls');
        }

        return array_pop($this->tagBuffers);
    }
}