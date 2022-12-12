<?php

declare(strict_types=1);

namespace Log;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Kernel {
    public function prepareContainerBuilder(): ContainerBuilder
    {
        $containerBuilder = new ContainerBuilder();

        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../config'));
        $loader->load('services.yaml');

        $containerBuilder->compile();
        return $containerBuilder;
    }
}