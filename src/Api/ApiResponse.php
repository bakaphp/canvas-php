<?php
namespace Canvas\Api;

/**
 * Class ApiResponse
 *
 * @package Stripe
 */
class ApiResponse
{
    public $headers;
    public $body;
    public $data;
    public $code;
    /**
     * @param string $body
     * @param integer $code
     * @param array|CaseInsensitiveArray|null $headers
     * @param array|null $data
     */
    public function __construct($body, $code, $headers, $data)
    {
        $this->body = $body;
        $this->code = $code;
        $this->headers = $headers;
        $this->data = $data;
    }
}
