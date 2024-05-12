<?php

use App\Structure\TestStructure;
use PHPUnit\Framework\TestCase;
use WebAPIBundle\WebAPI;

final class WebAPITest extends TestCase
{
    public function testWebApi()
    {
        if (require_once('src/Structure/TestStructure.php')) {
            $structure = new TestStructure();
            $structure->setId(1);
            $structure->setMessages('Testing message');
            $structure->setError("Testing error....!");

            $webapi = new WebAPI($structure);
            $webapi->setStatusCode(200);

            $this->assertIsObject($webapi->publish());
        }
    }
}
