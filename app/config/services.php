<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Serializer\XMLArrayEncoder;
use App\Service\DatabaseDataSaver;
use App\Service\DataParserChain;
use App\Service\FileParser;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\SchemaTool;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerBuilder;

return static function (ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder) {
    $services = $containerConfigurator->services();

    // logger
    $services->set('logger.stream_handler', StreamHandler::class)
        ->args([dirname(__DIR__).'/'.$_ENV['LOGS'], Level::Info]);
    $services->set('logger', Logger::class)
        ->args(['app', [service('logger.stream_handler')]])
        ->public();

    // db setup
    $services->set('orm_configuration', Configuration::class)
        ->factory([ORMSetup::class, 'createAttributeMetadataConfiguration'])
        ->args([[dirname(__DIR__).'/src/Entity'], true]);
    $services->set('database_connection', Connection::class)
        ->factory([DriverManager::class, 'getConnection'])
        ->args([[
            'driver' => $_ENV['DB_DRIVER'],
            'path' => dirname(__DIR__).'/'.$_ENV['DB_PATH'],
        ]]);
    $services->set('entity_manager', EntityManager::class)
        ->args([service('database_connection'), service('orm_configuration')])
        ->public();

    // db tool
    $services->set(SchemaTool::class)
        ->arg('$em', service('entity_manager'))
        ->public();

    // data parsers
    $services->set(XMLArrayEncoder::class);
    $services->set(FileParser::class)
        ->arg('$logger', service('logger'))
        ->arg('$encoders', [service(XMLArrayEncoder::class)])
        ->arg('$fileName', dirname(__DIR__).'/'.$_ENV['XML_SOURCE'])
    ;

    //    data parser chain
    $services->set(DataParserChain::class)
        ->call('addParser', [service(FileParser::class)])
        ->public();

    //    data saver
    $services->set(DatabaseDataSaver::class)
        ->arg('$entityManager', service('entity_manager'))
        ->public();
};
