<?php

namespace Majisti\Util\Cli\Doctrine;

use \Doctrine\DBAL\Migrations\Tools\Console\Command as MigrationCommand,
    \Doctrine\ORM\Tools\Console\ConsoleRunner,
    \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper,
    \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper,
    \Symfony\Component\Console\Helper as SymfonyHelper;

class CliLoader
{
    protected $_application;

    protected $_cli;

    public function __construct(\Zend_Application $application)
    {
        $this->_application = $application;
    }

    public function getCli()
    {
        if( null === $this->_cli ) {
            $this->loadCli();
        }

        return $this->_cli;
    }

    public function runCli()
    {
        $this->getCli()->run();
    }

    protected function loadCli()
    {
        /* create the cli */
        $cli = new \Symfony\Component\Console\Application(
            'Doctrine Command Line Interface',
            \Doctrine\ORM\Version::VERSION
        );

        $cli->setCatchExceptions(true);

        $app = $this->_application;
        $em = $app->getBootstrap()->getResource('Doctrine');

        $helperSet = new SymfonyHelper\HelperSet(array(
            'db' => new ConnectionHelper($em->getConnection()),
            'em' => new EntityManagerHelper($em),
            'dialog' => new SymfonyHelper\DialogHelper(),
        ));

        $cli->setHelperSet($helperSet);

        /* add default commands */
        ConsoleRunner::addCommands($cli);

        /* add migrations commands */
        $cli->addCommands(array(
            new MigrationCommand\DiffCommand(),
            new MigrationCommand\ExecuteCommand(),
            new MigrationCommand\GenerateCommand(),
            new MigrationCommand\MigrateCommand(),
            new MigrationCommand\StatusCommand(),
            new MigrationCommand\VersionCommand()
        ));

        /* data fixtures loading */
        $cli->addCommand(new Commands\LoadDataFixtures($app));

        $this->_cli = $cli;
    }
}
