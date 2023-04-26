<?php

namespace Tests\Command;

use App\Command\ImportDataCommand;
use App\Entity\Coffee;
use App\Serializer\XMLArrayEncoder;
use App\Service\DatabaseDataSaver;
use App\Service\DataParserChain;
use App\Service\DataSaverInterface;
use App\Service\FileParser;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Tester\CommandTester;

final class CommandTest extends TestCase
{
    public static function tearDownAfterClass(): void
    {
        @\unlink(dirname(__DIR__, 2).'/'.$_ENV['DB_PATH']);
    }

    public function testUnsupportedFile(): void
    {
        $saver = $this->getMockBuilder(DataSaverInterface::class)->getMock();
        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();

        $parser = new DataParserChain();
        $parser->addParser(new FileParser($logger, [new XMLArrayEncoder()], __DIR__.'/resources/feed.json'));

        $commandTester = new CommandTester(new ImportDataCommand($parser, $saver, $logger));
        $this->assertSame(1, $commandTester->execute([]));
    }

    public function testSupportedSource(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $em = $this->getEntityManager();
        $saver = new DatabaseDataSaver($em);
        $parser = new DataParserChain();
        $parser->addParser(new FileParser($logger, [new XMLArrayEncoder()], __DIR__.'/resources/data.xml'));

        $command = new ImportDataCommand($parser, $saver, $logger);
        $commandTester = new CommandTester($command);

        $this->assertSame(0, $commandTester->execute([]));

        $imported = $em->getRepository(Coffee::class)->findAll();
        $this->assertCount(2, $imported, 'check if all 2 valid items were imported');
    }

    private function getEntityManager(): EntityManagerInterface
    {
        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: [dirname(__DIR__).'/src/Entity'],
            isDevMode: true,
        );
        $connection = DriverManager::getConnection([
            'driver' => $_ENV['DB_DRIVER'],
            'path' => dirname(__DIR__, 2).'/'.$_ENV['DB_PATH'],
        ]);

        return new EntityManager($connection, $config);
    }
}
