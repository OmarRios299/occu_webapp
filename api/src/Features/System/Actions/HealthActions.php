<?php

declare(strict_types=1);

namespace Occu\Api\Features\System\Actions;

use Occu\Api\Http\JsonResponder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class HealthActions
{
    public function __construct(private readonly JsonResponder $json)
    {
    }

    public function health(Request $request, Response $response, array $args = []): Response
    {
        return $this->json->ok($response, ['status' => 'ok']);
    }
}

