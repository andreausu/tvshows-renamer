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
                'filePath',
                InputArgument::OPTIONAL,
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
        $path = $input->getArgument('filePath');

        if ($input->getOption('dry-run')) {
            // dry-run
        }

        $fs = new \Symfony\Component\Filesystem\Filesystem();

        if (!$fs->isAbsolutePath($path)) {
            $path = getcwd() . '/' . $path;
        }

        $newFileName = \Usu\TvShowsRenamer\Renamer::rename($path);

        $output->writeln($newFileName);
    }
}