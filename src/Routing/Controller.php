<?php

namespace Radiate\Routing;

use Radiate\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    use AuthorizesRequests;
}
