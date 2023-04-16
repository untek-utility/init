<?php

namespace Untek\Utility\Init\Presentation\Cli\Commands;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Untek\Core\Container\Traits\ContainerAwareAttributeTrait;
use Untek\Utility\Init\Presentation\Libs\Init;

class InitCommand extends Command
{

    use ContainerAwareAttributeTrait;

    protected static $defaultName = 'init';

    public function __construct(ContainerInterface $container)
    {
        parent::__construct(self::$defaultName);
        $this->setContainer($container);
    }

    protected function configure()
    {
//        $this->addArgument('channel', InputArgument::OPTIONAL);
        $this->addOption(
            'overwrite',
            null,
            InputOption::VALUE_OPTIONAL,
            '',
            false
        );
        $this->addOption(
            'profile',
            null,
            InputOption::VALUE_OPTIONAL,
            '',
            null
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $isOverwrite = $input->getOption('overwrite');
        $profile = $input->getOption('profile');

//        \Untek\Core\DotEnv\Domain\Libs\DotEnv::init();
        $defaultDefinitions = [
            'copyFiles' => 'Untek\Utility\Init\Presentation\Cli\Tasks\CopyFilesTask',
            'random' => [
                'class' => 'Untek\Utility\Init\Presentation\Cli\Tasks\RandomTask',
                'length' => 64,
                'charSet' => 'num|lower|upper',
                'quote' => '"',
                'quoteChars' => [
                    '"',
                    '$',
                ],
                'placeHolders' => [
                    'CSRF_TOKEN_ID',
                ],
            ],
//    'setCookieValidationKey' => 'Untek\Utility\Init\Presentation\Cli\Tasks\GenerateCookieValidationKeyTask',
            'setWritable' => 'Untek\Utility\Init\Presentation\Cli\Tasks\SetWritableTask',
            'setExecutable' => 'Untek\Utility\Init\Presentation\Cli\Tasks\SetExecutableTask',
            'createSymlink' => 'Untek\Utility\Init\Presentation\Cli\Tasks\CreateSymlinkTask',
        ];

//$configFile = getenv('ENVIRONMENTS_CONFIG_FILE') ?: __DIR__ . '/../../../../environments/config.php';
        $configFile = getenv('ENVIRONMENTS_CONFIG_FILE');
        $config = require $configFile;

        if (empty($config['definitions'])) {
            $config['definitions'] = $defaultDefinitions;
        }

//        $input = new ArgvInput;
//        $output = new ConsoleOutput;
//        $container = new \Untek\Core\Container\Libs\Container();
        $initLib = new Init($this->getContainer(), $input, $output, $config['environments'], $config['definitions']);
        if($profile) {
            $initLib->setProfile($profile);
        }
        $initLib->run();

        return Command::SUCCESS;
    }
}
