<?php

declare(strict_types=1);

namespace Occu\Api\Features\System;

use Occu\Api\Features\System\Actions\HealthActions;
use Occu\Api\Http\JsonResponder;
use Slim\App;

final class Routes
{
    /**
     * @param array{json: JsonResponder} $deps
     */
    public static function register(App $app, array $deps): void
    {
        $health = new HealthActions($deps['json']);

        $app->get('/health', [$health, 'health']);
    }
}

