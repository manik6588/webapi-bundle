<?php

namespace WebAPIBundle\Attribute;

#[\Attribute]
final class Key
{
    public function __construct(public readonly string|null $name = null)
    {

    }
}