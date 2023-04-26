<?php

namespace App;

use App\Command\ImportDataCommand;
use App\Service\DatabaseDataSaver;
use App\Service\DataParserChain;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\Dotenv\Dotenv;

/**
 * Main console application class, that reads configs, setups all services and prepares the command.
 */
class Application extends ConsoleApplication
{
    public function __construct(private string $env = '')
    {
        parent::__construct();
    }

    private $container;

    public function init()
    {
        $dotenv = new Dotenv();
        $dotenv->load(dirname(__DIR__).'/.env');
        if ($this->env && is_file(dirname(__DIR__).'/.env.'.$this->env)) {
            $dotenv->load(dirname(__DIR__).'/.env.'.$this->env);
        }

        $this->container = new ContainerBuilder();
        $loader = new PhpFileLoader($this->container, new FileLocator(dirname(__DIR__).'/config'));
        $loader->load('services.php');
        $this->container->compile();

        $logger = $this->container->get('logger');
        $parser = $this->container->get(DataParserChain::class);
        $saver = $this->container->get(DatabaseDataSaver::class);

        $this->add(new ImportDataCommand($parser, $saver, $logger));
    }

    public function createDatabase(): void
    {
        $schemaTool = $this->container->get(SchemaTool::class);
        $em = $this->container->get('entity_manager');
        $classes = [
            $em->getClassMetadata('App\Entity\Coffee'),
        ];

        try {
            $schemaTool->dropSchema($classes);
        } catch (\Exception $e) {
            //    it's ok
        }
        $schemaTool->createSchema($classes);
    }
}
