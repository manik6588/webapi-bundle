<?php

namespace WebAPI\Bundle\Interfaces;

use Symfony\Component\HttpFoundation\JsonResponse;

interface WebAPIInterface
{
    /**
     * To set the status code of the API response.
     *
     * @param int $code
     * @return WebAPIInterface
     */
    public function setStatusCode(int $code): self;

    /**
     * To get input data as raw JSON array without response headers.
     */
    public function getRawJson(): array;

    /**
     * Builds an API response with a structure class and improves security.
     * @return JsonResponse
     */
    public function publish(): JsonResponse;
}