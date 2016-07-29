<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Pdf page template
 */
abstract class APdfPage extends \XLite\View\AView
{
    /**
     * Widget parameter names
     */
    const PARAM_INTERFACE = 'interface';

    /**
     * PDF page settings keys
     *
     * ORIENTATION: Page orientation (portrait or album).
     * UNIT: Page dimension unit.
     * FORMAT: Page paper format (e.g. A4).
     * ENCODING: Page encoding (e.g. UTF-8).
     * MARGINS: Array of page margins in given order: LEFT, TOP, RIGHT, BOTTOM margin.
     */
    const ORIENTATION = 'orientation';
    const UNIT = 'unit';
    const FORMAT = 'format';
    const ENCODING = 'encoding';
    const MARGINS = 'margins';

    /**
     * Get pdf interface
     *
     * @return string
     */
    public function getInterface()
    {
        return $this->getParam(self::PARAM_INTERFACE);
    }

    /**
     * Get pdf language
     *
     * @return string
     */
    public function getLanguageCode()
    {
        if ($this->getInterface()) {
            $code = (\XLite::CUSTOMER_INTERFACE === $this->getInterface())
                ? \XLite\Core\Config::getInstance()->General->default_language
                : \XLite\Core\Config::getInstance()->General->default_admin_language;
        }

        return $code;
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_INTERFACE => new \XLite\Model\WidgetParam\TypeString(
                'Pdf interface',
                \XLite::CUSTOMER_INTERFACE
            ),
        );
    }

    /**
     * Returns PDF document title
     *
     * @return string
     */
    public function getDocumentTitle()
    {
        return '';
    }

    /**
     * Returns PDF specific styles
     *
     * @return array
     */
    public function getPdfStylesheets()
    {
        return array(
            'reset.css',
        );
    }

    /**
     * Returns PDF-specific stylesheets
     *
     * @return array
     */
    public function getStylesheetPaths()
    {
        $styles = $this->getPdfStylesheets();

        $paths = array_map(
            function ($style) {
                if ($style) {
                    $path = \XLite\Core\Layout::getInstance()
                        ->getResourceFullPath($style, \XLite::PDF_INTERFACE);

                    if (!$path) {
                        $path = \XLite\Core\Layout::getInstance()
                            ->getResourceFullPath($style, \XLite::COMMON_INTERFACE);
                    }
                    return $path;
                }
            },
            $styles
        );

        return $paths;
    }

    /**
     * Compiles template to HTML
     *
     * @return string
     */
    public function compile()
    {
        $layout = \XLite\Core\Layout::getInstance();
        $currentSkin = $layout->getSkin();
        $currentInterface = $layout->getInterface();
        $layout->setPdfSkin();

        $tempTranslation = \XLite\Core\Translation::getTmpMailTranslationCode();
        \XLite\Core\Translation::setTmpTranslationCode($this->getLanguageCode());

        $this->init();
        $text = $this->getContent();

        // restore old skin
        switch ($currentInterface) {
            default:
            case \XLite::ADMIN_INTERFACE:
                $layout->setAdminSkin();
                break;

            case \XLite::CUSTOMER_INTERFACE:
                $layout->setCustomerSkin();
                break;

            case \XLite::CONSOLE_INTERFACE:
                $layout->setConsoleSkin();
                break;

            case \XLite::MAIL_INTERFACE:
                $layout->setMailSkin();
                break;

            case \XLite::PDF_INTERFACE:
                $layout->setPdfSkin();
                break;
        }

        $layout->setSkin($currentSkin);
        \XLite\Core\Translation::setTmpTranslationCode($tempTranslation);

        return $text;
    }

    /**
     * Compiles body, title and stylesheets in complete html string
     *
     * @return string
     */
    public function getHtml()
    {
        return $this->buildHtml($this->compile(), $this->mergeStylesheets(), $this->getDocumentTitle());
    }

    /**
     * Loads each stylesheet file and merges them together
     *
     * @return string
     */
    protected function mergeStylesheets()
    {
        $stylesheet = '';

        foreach ($this->getStylesheetPaths() as $path) {
            $pathinfo = pathinfo($path);

            $text = '';
            if (isset($pathinfo['extension'])
                && $pathinfo['extension'] === 'less'
            ) {
                $lessRaw = \XLite\Core\LessParser::getInstance()
                    ->makeCSS(
                        array(
                            array(
                                'file'          => $path,
                                'original'      => $path,
                                'less'          => true,
                                'media'         => 'all',
                                'interface'     => 'pdf'
                            )
                        )
                    );
                if ($lessRaw && isset($lessRaw['file'])) {
                    $text = \Includes\Utils\FileManager::read($lessRaw['file']);
                }
            } else {
                $text = \Includes\Utils\FileManager::read($path);
            }
            if ($text) {
                $stylesheet .= $text . PHP_EOL;
            }
        }

        return $stylesheet;
    }

    /**
     * Inline styles
     *
     * @param  string  $styles CSS code
     *
     * @return string
     */
    protected function buildHtml($body, $styles, $title)
    {
        $root = LC_DIR_ROOT;
        $html =
"<html>
    <head>
        <title>$title</title>
        <style type='text/css'>
            body { font-family: OpenSans !important; }
            $styles
        </style>
    </head>
    <body>
        $body
    </body>
</html>";

        return $html;
    }

    /**
     * Default page settings
     *
     * @return array
     */
    public static function getDocumentSettings()
    {
        return array(
            static::FORMAT => 'A4',
            static::ORIENTATION => 'P',
        );
    }
}
