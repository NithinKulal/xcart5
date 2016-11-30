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

        $baseSkin = $layout->getSkin();
        $baseInterface = $layout->getInterface();
        $baseInnerInterface = $layout->getInnerInterface();
        $baseTmpTranslation = \XLite\Core\Translation::getTmpTranslationCode();

        $layout->setPdfSkin($this->getInterface());
        \XLite\Core\Translation::setTmpTranslationCode($this->getLanguageCode());

        $this->init();
        $text = $this->getContent();

        // restore old skin
        switch ($baseInterface) {
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
                $layout->setMailSkin($baseInnerInterface);
                break;

            case \XLite::PDF_INTERFACE:
                $layout->setPdfSkin();
                break;
        }

        $layout->setSkin($baseSkin);
        \XLite\Core\Translation::setTmpTranslationCode($baseTmpTranslation);

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
        $html =
"<html>
    <head>
        <title>$title</title>
        <style type='text/css'>
            @font-face {
                font-family: 'DejaVu Sans';
                src: url('https://fontlibrary.org/assets/fonts/dejavu-sans/f5ec8426554a3a67ebcdd39f9c3fee83/21546d802d508f3d358082d85bc0d9f1/DejaVuSansBold.ttf') format('truetype');
                font-weight: bold;
                font-style: normal;
            }
            @font-face {
                font-family: 'DejaVu Sans';
                src: url('https://fontlibrary.org/assets/fonts/dejavu-sans/f5ec8426554a3a67ebcdd39f9c3fee83/e5947ee873600dd1cae20e30cf80ee68/DejaVuSansBoldOblique.ttf') format('truetype');
                font-weight: bold;
                font-style: italic;
            }
            @font-face {
                font-family: 'DejaVu Sans';
                src: url('https://fontlibrary.org/assets/fonts/dejavu-sans/f5ec8426554a3a67ebcdd39f9c3fee83/49c0f03ec2fa354df7002bcb6331e106/DejaVuSansBook.ttf') format('truetype');
                font-weight: normal;
                font-style: normal;
            }
            @font-face {
                font-family: 'DejaVu Sans';
                src: url('https://fontlibrary.org/assets/fonts/dejavu-sans/f5ec8426554a3a67ebcdd39f9c3fee83/deed2dec8b2a429759183d4ce25ccd39/DejaVuSansCondensed.ttf') format('truetype');
                font-weight: normal;
                font-style: normal;
            }
            @font-face {
                font-family: 'DejaVu Sans';
                src: url('https://fontlibrary.org/assets/fonts/dejavu-sans/f5ec8426554a3a67ebcdd39f9c3fee83/f05d91a4bf97b24878103a3cdf8787d0/DejaVuSansCondensedBold.ttf') format('truetype');
                font-weight: bold;
                font-style: normal;
            }
            @font-face {
                font-family: 'DejaVu Sans';
                src: url('https://fontlibrary.org/assets/fonts/dejavu-sans/f5ec8426554a3a67ebcdd39f9c3fee83/d5e8cfd6145aec5c1c6059484c896b88/DejaVuSansCondensedBoldOblique.ttf') format('truetype');
                font-weight: bold;
                font-style: italic;
            }
            @font-face {
                font-family: 'DejaVu Sans';
                src: url('https://fontlibrary.org/assets/fonts/dejavu-sans/f5ec8426554a3a67ebcdd39f9c3fee83/01fef11654a41d40b5aa1d9564eeb16f/DejaVuSansCondensedOblique.ttf') format('truetype');
                font-weight: normal;
                font-style: italic;
            }
            @font-face {
                font-family: 'DejaVu Sans';
                src: url('https://fontlibrary.org/assets/fonts/dejavu-sans/f5ec8426554a3a67ebcdd39f9c3fee83/5213563f6868aea7a7b5dd9ab2581ec9/DejaVuSansExtraLight.ttf') format('truetype');
                font-weight: normal;
                font-style: normal;
            }
            @font-face {
                font-family: 'DejaVu Sans';
                src: url('https://fontlibrary.org/assets/fonts/dejavu-sans/f5ec8426554a3a67ebcdd39f9c3fee83/8723fc16d3649200d6179f391dd43f9f/DejaVuSansOblique.ttf') format('truetype');
                font-weight: normal;
                font-style: italic;
            }
            body { font-family: 'DejaVu Sans' !important; letter-spacing: -.5px;}
            $styles
        </style>
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"/>
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
