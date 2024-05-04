<?php

namespace WebAPIBundle\Interfaces;

use Symfony\Component\HttpFoundation\JsonResponse;

interface WebAPIInterface
{
    /**
     * Set the status code of the API response
     *
     * @param int $code
     * @return WebAPIInterface
     */
    public function setStatusCode(int $code): self;

    /**
     * Build API response with structure class. It's improve security of API.
     * @return JsonResponse
     */
    public function publish(): JsonResponse;
}