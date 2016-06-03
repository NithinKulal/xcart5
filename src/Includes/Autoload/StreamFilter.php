<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Autoload;

use php_user_filter;
use Includes\SourceToTargetPathMapperInterface;

class StreamFilter extends php_user_filter
{
    /** @var SourceToTargetPathMapperInterface */
    protected static $sourceToTargetPathMapper;

    /** @var StreamWrapperInterface */
    protected static $redirectingStreamFilter;

    public function filter($in, $out, &$consumed, $closing)
    {
        while ($bucket = stream_bucket_make_writeable($in)) ;

        if ($closing || feof($this->stream)) {
            $streamUri = stream_get_meta_data($this->stream)['uri'];

            $source = static::$redirectingStreamFilter->unwrapStreamUri($streamUri);

            $target = static::$sourceToTargetPathMapper->map($this->modifyStreamUri($source));

            $buffer = file_get_contents($target);

            $bucket = stream_bucket_new($this->stream, $buffer);
            stream_bucket_append($out, $bucket);

            return PSFS_PASS_ON;
        }

        return PSFS_FEED_ME;
    }

    protected function modifyStreamUri($stream)
    {
        return $stream;
    }

    public static function initialize(
        SourceToTargetPathMapperInterface $sourceToTargetPathMapper,
        StreamWrapperInterface $redirectingStreamFilter
    ) {
        static::$sourceToTargetPathMapper = $sourceToTargetPathMapper;
        static::$redirectingStreamFilter  = $redirectingStreamFilter;
    }
}