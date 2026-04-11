<?php

declare(strict_types=1);

namespace Occu\Api\Http;

use Psr\Http\Message\ResponseInterface as Response;

final class JsonResponder
{
    /** @param array|object|null $data */
    public function ok(Response $response, array|object|null $data = [], int $status = 200): Response
    {
        return $this->json($response, $status, [
            'success' => true,
            'data' => $data,
        ]);
    }

    public function created(Response $response, array $data = []): Response
    {
        return $this->ok($response, $data, 201);
    }

    public function error(Response $response, int $status, string $errorCode, string $message, array $details = []): Response
    {
        return $this->json($response, $status, [
            'success' => false,
            'errorCode' => $errorCode,
            'message' => $message,
            'details' => (object)$details,
        ]);
    }

    private function json(Response $response, int $status, array $payload): Response
    {
        $response->getBody()->write((string)json_encode($payload, JSON_UNESCAPED_UNICODE));
        return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus($status);
    }
}

