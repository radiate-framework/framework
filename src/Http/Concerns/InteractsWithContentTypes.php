<?php

namespace Radiate\Http\Concerns;

trait InteractsWithContentTypes
{
    /**
     * Get the content type
     *
     * @return string|null
     */
    public function getContentType(): ?string
    {
        if ($contentType = $this->get_content_type()) {
            return $contentType['value'];
        }
    }

    /**
     * Determine if the request is sending JSON.
     *
     * @return bool
     */
    public function isJson(): bool
    {
        return $this->is_json_content_type();
    }

    /**
     * Determine if the current request probably expects a JSON response.
     *
     * @return bool
     */
    public function expectsJson(): bool
    {
        return $this->ajax() || $this->wantsJson();
    }

    /**
     * Determine if the request can accept a JSON response
     *
     * @return bool
     */
    public function wantsJson(): bool
    {
        return $this->header('accept', '*/*') === 'application/json';
    }

    /**
     * Determines whether the current requests accepts a given content type.
     *
     * @param  string|array  $contentTypes
     * @return bool
     */
    public function accepts($contentTypes): bool
    {
        $type = $this->header('accept', '*/*');

        if (in_array($type, ['*/*', '*'])) {
            return true;
        }

        foreach ((array) $contentTypes as $contentType) {
            if ($contentType === $type) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the current request accepts any content type.
     *
     * @return bool
     */
    public function acceptsAnyContentType(): bool
    {
        return $this->accepts(['*/*', '*']);
    }

    /**
     * Determines whether a request accepts JSON.
     *
     * @return bool
     */
    public function acceptsJson(): bool
    {
        return $this->accepts('application/json');
    }

    /**
     * Determines whether a request accepts HTML.
     *
     * @return bool
     */
    public function acceptsHtml(): bool
    {
        return $this->accepts('text/html');
    }
}
