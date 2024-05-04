<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use WebAPIBundle\Command\StructureCommand;

final class StructureCommandTest extends TestCase
{
    public function testExecute()
    {

        $application = new Application();

        $application->add(new StructureCommand());

        $command = $application->find('webapi:structure:create');

        $commandTester = new CommandTester($command);

        $inputs = [
            'Id',
            'integer',
            'false',
            'Messages',
            'string',
            '',
            'Error',
            'string',
            '',
            ''
        ];

        $commandTester->setInputs($inputs);

        $commandTester->execute([
            'class' => 'TestStructure'
        ]);

        $this->assertStringContainsString('[OK] Success.', $commandTester->getDisplay());
    }
}
