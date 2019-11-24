<?php

namespace Canvas\Util;

use Canvas\Util\CanvasObject;
use StdClass;

abstract class Util
{
    private static $isMbstringAvailable = null;

    /**
     * Converts a response from the Canvas API to a simple PHP object.
     *
     * @param array $response The response from the Canvas API.
     * @return object|object[]
     */
    public static function convertToSimpleObject(array $response)
    {
        /**
         * if we get the key 0, it means we have a list response ,
         * so we overwrite the array properties as object
         */
        if (isset($response[0])) {
            foreach ($response as $key => $item) {
                $response[$key] = (object) $item;
            }

            return $response;
        }

        return (object) $response;
        //return json_decode(json_encode($response));
    }

    /**
     * @param string|mixed $value A string to UTF8-encode.
     *
     * @return string|mixed The UTF8-encoded string, or the object passed in if
     *    it wasn't a string.
     */
    public static function utf8(string $value): ?string
    {
        if (self::$isMbstringAvailable === null) {
            self::$isMbstringAvailable = function_exists('mb_detect_encoding');
        }
        if (is_string($value) && self::$isMbstringAvailable && mb_detect_encoding($value, 'UTF-8', true) != 'UTF-8') {
            return utf8_encode($value);
        } else {
            return $value;
        }
    }

    /**
     * Given a ID return its as a array?
     *
     * @param mixed $id
     * @return array
     */
    public static function normalizeId($id): array
    {
        if (is_array($id)) {
            $params = $id;
            $id = $params['id'];
            unset($params['id']);
        } else {
            $params = [];
        }
        return [$id, $params];
    }

    /**
     * Returns UNIX timestamp in milliseconds.
     *
     * @return integer current time in millis
     */
    public static function currentTimeMillis(): int
    {
        return (int) round(microtime(true) * 1000);
    }
}
