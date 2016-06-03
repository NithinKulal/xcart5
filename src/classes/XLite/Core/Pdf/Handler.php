<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Pdf;

use XLite\View\APdfPage;

/**
 * Abstract Pdf outputting handler
 */
abstract class Handler
{
    /**
     * Default handler instance
     *
     * @var \XLite\Core\Pdf\Handler
     */
    public static $instance;

    /**
     * Returns default handler (DomPdf currently)
     *
     * @return \XLite\Core\Pdf\Handler
     */
    public static function getDefault()
    {
        if (!static::$instance) {
            static::$instance = new Handler\DomPdf();
        }

        return static::$instance;
    }

    /**
     * Raw input
     *
     * @var mixed
     */
    protected $input;

    /**
     * Performs input handling operations like resetting the handler.
     *
     * @param  mixed    $input  Input
     */
    protected function handle($input)
    {
        if ($this->input) {
            $this->reset();
        }

        $this->input = $input;
    }

    /**
     * Resets handler state to be ready to next input
     *
     * @return void
     */
    abstract public function reset();

    /**
     * Prepares string input to outputting
     *
     * @param  string   $input  Input data
     * @param  string   $title  Document title OPTIONAL
     * @param  boolean  $html   Is HTML content? OPTIONAL
     * @return void
     */
    public function handleText($input, $title = '', $html = false)
    {
        $this->handle($input);
    }

    /**
     * Prepares pdf page to outputting
     *
     * @param  \XLite\View\APdfPage $input Pdf page to output
     * @return void
     */
    public function handlePdfPage(\XLite\View\APdfPage $input)
    {
        $this->handle($input);
    }

    /**
     * Returns default document settings in hashmap
     *
     * @return array
     */
    protected function getDefaultDocumentSettings()
    {
        return APdfPage::getDocumentSettings();
    }

    /**
     * Outputs the document from handler
     *
     * @return mixed
     */
    abstract public function output();
}
