<?php

declare(strict_types=1);

namespace WebAPIBundle;

use Symfony\Component\HttpFoundation\JsonResponse;
use WebAPIBundle\Attribute\Key;
use WebAPIBundle\Interfaces\WebAPIInterface;

final class WebAPI implements WebAPIInterface
{

    protected object $instance;
    protected JsonResponse $response;
    protected array $data;
    protected array $body;

    public function __construct(object $structureClass)
    {
        $this->instance = $structureClass;
        $this->response = new JsonResponse();
        $this->data = [];
    }

    private function retrieveAttributes()
    {
        $reflection = new \ReflectionClass($this->instance);
        foreach ($reflection->getMethods() as $method) {
            $attributes = $method->getAttributes(Key::class);
            foreach ($attributes as $attribute) {
                $this->invoke($this->instance, $attribute, $method);
            }
        }
    }

    private function invoke(object $instance, \ReflectionAttribute $attribute, \ReflectionMethod $method): void
    {
        $key = $attribute->newInstance();
        $this->data[$key->name] = $method->invoke($instance);
    }

    private function buildResponseBody(array $data): string
    {
        return json_encode($data);
    }

    private function enhanceSecurity(JsonResponse &$response): JsonResponse
    {
        $response->headers->set('Server', 'WebAPI');
        $response->headers->set('Content-Security-Policy', 'default-src "self"');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    private function buildJsonResponse(string $body): JsonResponse
    {
        $response = $this->response;
        $this->enhanceSecurity($response);
        $response->setContent($body);
        return $response;
    }

    public function setStatusCode(int $code): WebAPIInterface {
        $this->response->setStatusCode($code);

        return $this;
    }

    public function publish(): JsonResponse
    {
        $this->retrieveAttributes();
        return $this->buildJsonResponse($this->buildResponseBody($this->data));
    }

    public function getRawJson(): array
    {
        $this->retrieveAttributes();
        return $this->data;
    }
}