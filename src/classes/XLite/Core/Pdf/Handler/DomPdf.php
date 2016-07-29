<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Pdf\Handler;

use XLite\View\APdfPage;

/**
 * Pdf handler based on dompdf library
 * https://github.com/dompdf/dompdf/wiki
 */
class DomPdf extends \XLite\Core\Pdf\Handler
{
    /**
     * Dompdf instance
     *
     * @var \Dompdf
     */
    protected $dompdfInstance;

    /**
     * Returns Dompdf document instance
     *
     * @param  string $format      Page format OPTIONAL
     *
     * @return \Dompdf\Dompdf
     */
    public function getDompdfInstance()
    {
        if (!$this->dompdfInstance) {
            require_once (LC_DIR_LIB . 'dompdf' . LC_DS . 'autoload.inc.php');
            $this->dompdfInstance = new \Dompdf\Dompdf();
        }

        return $this->dompdfInstance;
    }

    /**
     * Resets handler state to be ready to next input
     *
     * @return void
     */
    public function reset()
    {
        $this->dompdfInstance = null;
    }

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
        parent::handleText($input, $title, $html);

        $this->prepareDocument($this->getDefaultDocumentSettings());
        $pdf = $this->getDompdfInstance();

        $pdf->loadHtml($input);
        $pdf->render();
    }

    /**
     * Prepares pdf page to outputting
     *
     * @param  \XLite\View\APdfPage $input Pdf page to output
     * @return void
     */
    public function handlePdfPage(\XLite\View\APdfPage $input)
    {
        parent::handlePdfPage($input);

        $this->prepareDocument($input->getDocumentSettings());

        $html = $input->getHtml();

        $pdf = $this->getDompdfInstance();

        $pdf->loadHtml($html);
        $pdf->render();
    }

    /**
     * Prepares page settings
     *
     * @param  array  $settings Hashmap of settings
     * @return void
     */
    protected function prepareDocument(array $settings)
    {
        $pdf = $this->getDompdfInstance();
        switch ($settings['orientation']) {
            case 'L':
                $orientation = 'landscape';
                break;

            case 'P':
            default:
                $orientation = 'portrait';
                break;
        }
        $pdf->setBasePath(LC_DIR_ROOT);
        $pdf->setPaper($settings['format'], $orientation);
    }

    /**
     * Outputs the document as string from handler
     *
     * @return mixed
     */
    public function output()
    {
        return $this->getDompdfInstance()->output();
    }
}
