<?php

namespace TsMedia\LaravelInstagramScraper\InstagramScraper\Model;

use TsMedia\LaravelInstagramScraper\InstagramScraper\Traits\ArrayLikeTrait;
use TsMedia\LaravelInstagramScraper\InstagramScraper\Traits\InitializerTrait;

/**
 * Class AbstractModel
 * @package TsMedia\LaravelInstagramScraper\InstagramScraper\Model
 */
abstract class AbstractModel implements \ArrayAccess
{
    use InitializerTrait, ArrayLikeTrait;

    /**
     * @var array
     */
    protected static $initPropertiesMap = [];

    /**
     * @return array
     */
    public static function getColumns()
    {
        return \array_keys(static::$initPropertiesMap);
    }
}