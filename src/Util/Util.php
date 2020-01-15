<?php

namespace Kanvas\Sdk\Util;

use Kanvas\Sdk\KanvasObject;
use StdClass;

abstract class Util
{
    private static $isMbstringAvailable = null;

    /**
     * Whether the provided array (or other) is a list rather than a dictionary.
     * A list is defined as an array for which all the keys are consecutive
     * integers starting at 0. Empty arrays are considered to be lists.
     *
     * @param array|mixed $array
     * @return boolean true if the given object is a list.
     */
    public static function isList($array)
    {
        if (!is_array($array)) {
            return false;
        }
        if ($array === []) {
            return true;
        }
        if (array_keys($array) !== range(0, count($array) - 1)) {
            return false;
        }
        return true;
    }

    /**
     * Converts a response from the Canvas API to a simple PHP object.
     *
     * @param array $response The response from the Canvas API.
     * @param RequestOptions $opts
     * @param string $object
     * @return object|object[]
     */
    public static function convertToSimpleObject(array $response, RequestOptions $opts, string $object = null)
    {
        $types = [
            \Kanvas\Sdk\Users::OBJECT_NAME => \Kanvas\Sdk\Users::class,
            \Kanvas\Sdk\Companies::OBJECT_NAME => \Kanvas\Sdk\Companies::class,
        ];

        if (self::isList($response)) {
            $mapped = [];
            foreach ($response as $i) {
                array_push($mapped, self::convertToSimpleObject($i, $opts, $object));
            }
            return $mapped;
        } elseif (is_array($response)) {
            if (!is_null($object) && isset($types[$object])) {
                $class = $types[$object];
            } else {
                $class = KanvasObject::class;
            }
            return $class::constructFrom($response, $opts);
        } else {
            return (object) $response;
        }

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
