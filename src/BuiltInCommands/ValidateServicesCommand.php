<?php
/**
 * Created by PhpStorm.
 * User: amopi
 * Date: 2016-03-28
 * Time: 21:03
 */

namespace Amopi\Mopi\BuiltInCommands;

use Amopi\Mopi\ConsoleApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ValidateServicesCommand extends Command
{
    protected function configure()
    {
        parent::configure();

        $this->setName('mopi:services:validate');
        $this->setDescription("Validate all services configured for mopi.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ConsoleApplication $console */
        $console = $this->getApplication();
        $mopi = $console->getMopi();

        $ids = $mopi->getServiceIds();
        foreach ($ids as $id) {
            try {
                $output->writeln("Validating <comment>$id</comment> ...");
                $mopi->getService($id);
                $output->writeln("<info>Done.</info>");
            } catch (\Exception $e) {
                $output->writeln(
                    "<error>Service $id is misconfigured, execption = \n" . $e->getTraceAsString() . "</error>"
                );
            }
        }

    }
}
