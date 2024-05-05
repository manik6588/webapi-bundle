<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use WebAPIBundle\Command\StructureCommand;

return function (ContainerConfigurator $configurator)
{
    $services = $configurator->services();

    $services->set(StructureCommand::class)
            ->tag('console.command');
};