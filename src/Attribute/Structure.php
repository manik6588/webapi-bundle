<?php

namespace WebAPI\Attribute;

#[\Attribute]
final class Structure
{
    public function __construct(public bool $enable=true)
    {
    }
}