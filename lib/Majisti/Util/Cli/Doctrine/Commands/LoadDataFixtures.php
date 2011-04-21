<?php

namespace Majisti\Util\Cli\Doctrine\Commands;

use Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console,
    Doctrine\Common\DataFixtures as DataFixtures;

class LoadDataFixtures extends Console\Command\Command
{
    protected $_application;

    public function __construct(\Zend_Application $app, $name = null)
    {
        $this->_application = $app;
        parent::__construct($name);
    }

    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
            ->setName('orm:schema-tool:load-fixtures')
            ->setDescription('Load data fixtures on EntityManager Storage ' .
                'Connection. (Will purge all data before populating)')
            ->setHelp(<<<EOT
Load data fixtures into the database.
EOT
        );
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(Console\Input\InputInterface $input,
        Console\Output\OutputInterface $output)
    {
        $app    = $this->_application;
        $em     = $app->getBootstrap()->getResource('Doctrine');
        $loader = new DataFixtures\Loader();
        $purger = new DataFixtures\Purger\ORMPurger($em);

        $maj = $app->getOption('majisti');
        $env = $maj['app']['env'];
        $dataDir = 'development' === $env || 'integration' === $env
            ? '/testing'
            : '/initial';

        $path = $maj['app']['path'] .  '/lib/models/doctrine/fixtures' . $dataDir;

        if( file_exists($path) ) {
            $loader->loadFromDirectory($path);
        }

        $cont = $app->getBootstrap()->getResource('frontController');
        /* load modules fixtures */
        $modules = $cont->getControllerDirectory();
        foreach( $modules as $module ) {
            if( $path = realpath($module . '/../models/doctrine/fixtures' . $dataDir) ) {
                $loader->loadFromDirectory($path);
            }
        }
        $executor = new DataFixtures\Executor\ORMExecutor($em, $purger);

        $fixtures = $loader->getFixtures();
        $executor->execute($fixtures);

        $output->write(sprintf('%d fixtures loaded!',
            count($fixtures)) . PHP_EOL);
    }
}
