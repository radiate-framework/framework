<?php

namespace Radiate\Http;

class RedirectResponse extends Response
{
    /**
     * Create the redirect response
     *
     * @param string $location
     * @param integer $status
     */
    public function __construct(string $location, int $status = 302)
    {
        parent::__construct('', $status, ['location' => $location]);
    }
}
