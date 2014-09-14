<?php

namespace Usu\TvShowsRenamer\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RenamerCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('rename')
            ->setDescription('Renames a file')
            ->addArgument(
                'filePaths',
                InputArgument::IS_ARRAY,
                'Who do you want to greet?'
            )
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'If set it will only print the new filename'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $paths = $input->getArgument('filePaths');

        foreach ($paths as $path) {

            $output->writeln('<info>Started renaming ' . $path . '</info>');

            if ($input->getOption('dry-run')) {
                // dry-run
            }

            $fs = new \Symfony\Component\Filesystem\Filesystem();

            if (!$fs->isAbsolutePath($path)) {
                $path = getcwd() . '/' . $path;
            }

            $newFileName = \Usu\TvShowsRenamer\Renamer::rename($path);

            $output->writeln('<info>' . $newFileName . '<info>');

        }
    }
}