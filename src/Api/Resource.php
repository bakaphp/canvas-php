<?php

namespace Canvas\Api;

use Canvas\Canvas;
use Canvas\CanvasObject;
use Canvas\Api\Requestor;
use Canvas\Util\Util;
use Canvas\Exception\UnexpectedValueException;
use Canvas\Util\Set;

/**
 * Class Resource
 *
 * @package Canvas
 */
abstract class Resource extends CanvasObject
{
    use Operations\Request;

    /**
     * @var boolean A flag that can be set a behavior that will cause this
     * resource to be encoded and sent up along with an update of its parent
     * resource. This is usually not desirable because resources are updated
     * individually on their own endpoints, but there are certain cases,
     * replacing a customer's source for example, where this is allowed.
     */
    public $saveWithParent = false;

    /**
     * @return \Canvas\Util\Set A list of fields that can be their own type of
     * API resource (say a nested card under an account for example), and if
     * that resource is set, it should be transmitted to the API on a create or
     * update. Doing so is not the default behavior because API resources
     * should normally be persisted on their own RESTful endpoints.
     */
    public static function getSavedNestedResources(): Set
    {
        static $savedNestedResources = null;
        if ($savedNestedResources === null) {
            $savedNestedResources = new Util\Set();
        }
        return $savedNestedResources;
    }

    /**
     * Setter
     *
     * @param string $k
     * @param string $v
     */
    public function __set($k, $v)
    {
        parent::__set($k, $v);
        $v = $this->$k;
        if ((static::getSavedNestedResources()->includes($k)) &&
            ($v instanceof Resource)) {
            $v->saveWithParent = true;
        }
        return $v;
    }

    /**
     * @return Resource The refreshed resource.
     *
     * @throws Exception\ApiErrorException
     */
    public function refresh(): Resource
    {
        $requestor = new Requestor($this->_opts->apiKey, static::baseUrl());
        $url = $this->instanceUrl();
        list($response, $this->_opts->apiKey) = $requestor->request(
            'get',
            $url,
            $this->_retrieveOptions,
            $this->_opts->headers
        );
        $this->setLastResponse($response);
        $this->refreshFrom($response->json, $this->_opts);
        return $this;
    }

    /**
     * @return string The base URL for the given class.
     */
    public static function baseUrl(): string
    {
        return Canvas::$apiBase;
    }

    /**
     * @return string The endpoint URL for the given class.
     */
    public static function classUrl(): string
    {
        // Replace dots with slashes for namespaced resources, e.g. if the object's name is
        // "foo.bar", then its URL will be "/v1/foo/bars".
        $base = str_replace('.', '/', static::OBJECT_NAME);
        return "/v1/${base}";
    }

    /**
     * @return string The instance endpoint URL for the given class.
     */
    public static function resourceUrl($id): string
    {
        if ($id === null) {
            $class = get_called_class();
            $message = "Could not determine which URL to request: "
               . "$class instance has invalid ID: $id";
            throw new UnexpectedValueException($message);
        }
        $id = Util::utf8($id);
        $base = static::classUrl();
        $extn = urlencode($id);
        return "$base/$extn";
    }

    /**
     * @return string The full API URL for this API resource with specific identifier.
     */
    public function instanceUrl($id): string
    {
        return static::resourceUrl($id);
    }


}