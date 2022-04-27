<?php

namespace Radiate\Http;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class JsonResponse extends Response
{
    /**
     * The JSON encoding options
     *
     * @var integer
     */
    protected $encodingOptions = 0;

    /**
     * The original JSON data
     *
     * @var mixed
     */
    protected $original;

    /**
     * Create the JSON response
     *
     * @param mixed $data
     * @param integer $status
     * @param array $headers
     * @param integer $options
     */
    public function __construct($data = [], int $status = 200, array $headers = [], int $options = 0)
    {
        $this->encodingOptions = $options;

        parent::__construct('', $status, $headers);

        $this->setData($data)->header('content-type', 'application/json');
    }

    /**
     * Get the original JSON data
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->original;
    }

    /**
     * Set the JSON data
     *
     * @param mixed $data
     * @return self
     */
    public function setData($data = []): self
    {
        $this->original = $data;

        if ($data instanceof Jsonable) {
            return $this->setContent($data->toJson($this->encodingOptions));
        } elseif ($data instanceof JsonSerializable) {
            $content = $data->jsonSerialize();
        } elseif ($data instanceof Arrayable) {
            $content = $data->toArray();
        } elseif (is_array($data)) {
            $content = $data;
        } elseif (method_exists($data, 'to_array')) {
            $content = $data->to_array();
        } else {
            $content = $data;
        }

        return $this->setContent(
            wp_json_encode($content, $this->encodingOptions)
        );
    }
}
