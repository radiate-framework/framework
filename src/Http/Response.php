<?php

namespace Radiate\Http;

use JsonSerializable;
use WP_REST_Response;

class Response extends WP_REST_Response implements JsonSerializable
{
    /**
     * Create the response
     *
     * @param string $content
     * @param integer $status
     * @param array $headers
     */
    public function __construct(string $content = '', int $status = 200, array $headers = [])
    {
        $this->header('content-type', 'text/html');

        parent::__construct($content, $status, $headers);
    }

    /**
     * Get the status code for the response.
     *
     * @return int
     */
    public function status(): int
    {
        return $this->get_status();
    }

    /**
     * Set the status code for the response.
     *
     * @param int $status
     * @return self
     */
    public function setStatusCode(int $status): self
    {
        $this->set_status($status);

        return $this;
    }

    /**
     * Get the content of the response.
     *
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->get_data();
    }

    /**
     * Set the content of the response.
     *
     * @param string|null $content
     * @return self
     */
    public function setContent(?string $content): self
    {
        $this->set_data($content);

        return $this;
    }

    /**
     * Is the response empty?
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return in_array($this->status(), [204, 304]);
    }

    /**
     * Get the headers of the response.
     *
     * @return array
     */
    public function headers(): array
    {
        return $this->get_headers();
    }

    /**
     * Set the headers
     *
     * @param array $headers
     * @return self
     */
    public function set_headers($headers): self
    {
        foreach ($headers as $key => $value) {
            $this->header($key, $value);
        }

        return $this;
    }

    /**
     * Set the headers
     *
     * @param array $headers
     * @return self
     */
    public function withHeaders(array $headers): self
    {
        return $this->set_headers($headers);
    }

    /**
     * Set a header
     *
     * @param string $key
     * @param string $value
     * @param boolean $replace
     * @return self
     */
    public function header($key, $value, $replace = true): self
    {
        parent::header(ucwords(strtolower($key), '-'), $value, $replace);

        return $this;
    }

    /**
     * Send the response
     *
     * @return self
     */
    public function send(): self
    {
        return $this->sendHeaders()->sendContent();
    }

    /**
     * Send the response headers
     *
     * @return self
     */
    public function sendHeaders(): self
    {
        foreach ($this->headers() as $key => $value) {
            $value = preg_replace('/\s+/', ' ', $value);
            header("{$key}: {$value}");
        }

        status_header($this->status());

        return $this;
    }

    /**
     * Send the response content
     *
     * @return self
     */
    public function sendContent(): self
    {
        echo $this->getContent();

        return $this;
    }
}
