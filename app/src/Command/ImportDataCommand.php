<?php

namespace App\Command;

use App\Exception\AppException;
use App\Exception\DataSavingException;
use App\Service\DataParserChain;
use App\Service\DataSaverInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'parse-data')]
class ImportDataCommand extends Command
{
    public function __construct(
        private readonly DataParserChain $parserChain,
        private readonly DataSaverInterface $dataSaver,
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $objects = $this->parserChain->parse();
        } catch (AppException $e) {
            $this->logger->critical($e->getMessage(), $e->getContext());
            $output->writeln($e->getMessage());

            return 1;
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            $output->writeln('Unknown parse error');

            return 1;
        }

        try {
            $importedCount = $this->dataSaver->save($objects);
        } catch (DataSavingException $exception) {
            $this->logger->critical($exception->getMessage());
            $output->writeln('Save data error');

            return 1;
        }

        $this->logger->info(sprintf('%s records imported ', $importedCount));
        $output->writeln(sprintf('%s records imported ', $importedCount));

        return 0;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('The command reads XML data from a file and saves it in the database');
    }
}
