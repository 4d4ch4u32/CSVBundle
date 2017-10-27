<?php

namespace webworks\CSVBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class webworksCSVExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $csvConfigDir = $container->getParameter('kernel.root_dir') . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'csv';
        $locator = new FileLocator($csvConfigDir);
        $yamlMappingFile = $locator->locate('mapping.yml', null, false);

        if (sizeof($yamlMappingFile) == 0) {
            throw new FileNotFoundException('Cannot find required file "mapping.yml" in ' . $csvConfigDir . '.');
        }

        $fileToUse = $yamlMappingFile[0];

        try {
            $yamlContent = Yaml::parse(file_get_contents($fileToUse));
        } catch (ParseException $ex) {
            throw new ParseException('Cannot parse csv mapping file "' . $csvConfigDir . DIRECTORY_SEPARATOR . 'mapping.yml.twig"');
        }

        $container->setParameter('webworks_csv_mapping', $yamlContent);
    }
}
