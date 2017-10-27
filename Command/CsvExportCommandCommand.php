<?php

namespace webworks\CSVBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CsvExportCommandCommand extends ContainerAwareCommand
{

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('webworks:csv:export')
            ->setDescription('Creates a CSV file using the specified mapping information')
            ->addArgument('mapping_name', InputArgument::REQUIRED);
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mappingName = $input->getArgument('mapping_name');

        $csvPath = $this->getContainer()->get('webworks.csv.mapping')
            ->setMappingName($mappingName)
            ->setMode('export')
            ->process()
            ->getPath();

        $output->writeln('CSV file saved: '. $csvPath);
    }

}
