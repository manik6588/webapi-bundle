<?php

use PHPUnit\Framework\TestCase;
use WebAPIBundle\Structure\TestWebAPIStructure;
use WebAPIBundle\WebAPI;

final class WebAPITest extends TestCase
{
    public function testWebApi()
    {
        $structure = new TestWebAPIStructure();
        $structure->setId(1);
        $structure->setMessages('Testing message');

        $webapi = new WebAPI($structure);
        $webapi->setStatusCode(200);

        $this->assertIsObject($webapi->publish());
    }
}