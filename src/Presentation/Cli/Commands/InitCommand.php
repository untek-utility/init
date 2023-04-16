<?php

namespace Untek\Utility\Init\Presentation\Cli\Commands;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Untek\Core\Container\Traits\ContainerAwareAttributeTrait;
use Untek\Core\Text\Libs\TemplateRender;
use Untek\Framework\Console\Symfony4\Traits\IOTrait;
use Untek\Utility\Init\Presentation\Libs\Init;

class InitCommand extends Command
{
    use IOTrait;
    use ContainerAwareAttributeTrait;

    protected static $defaultName = 'init';

    public function __construct(ContainerInterface $container)
    {
        parent::__construct(self::$defaultName);
        $this->setContainer($container);
    }

    protected function configure()
    {
        $this->addOption(
            'config',
            null,
            InputOption::VALUE_OPTIONAL,
            '',
            false
        );
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
        $output->writeln("Application Initialization Tool\n");

        $this->setInputOutput($input, $output);

        $overwrite = $input->getOption('overwrite');
        $profile = $input->getOption('profile');
        $configFile = $input->getOption('config');

        $configFile = (new TemplateRender())
            ->addReplacement('ROOT_DIRECTORY', getenv('ROOT_DIRECTORY'))
            ->renderTemplate($configFile);

        $profiles = require $configFile;

        if (empty($profile)) {
            $profile = $this->userInput($profiles);
        }

        $profileConfig = $profiles[$profile];
        $profileConfig['overwrite'] = $overwrite;

        $initLib = new Init($input, $output, $profileConfig);

        $output->writeln("\n  Start initialization ...\n\n");
        $initLib->run();
        $output->writeln("\n  ... initialization completed.\n\n");

        return Command::SUCCESS;
    }

    private function userInput($profiles)
    {
        $keys = array_keys($profiles);
        $answer = $this->selectEnv($profiles);
        $envName = $keys[$answer] ?? null;
        if ($envName == null) {
            $this->output->write("\n  Quit initialization.\n");
            exit(Command::SUCCESS);
        }
        $this->validateEnvName($envName, $profiles);
        $this->userConfirm($envName);
        return $envName;
    }

    private function selectEnv($profiles): ?string
    {
        $envName = null;
        $envNames = array_keys($profiles);
        $this->output->write("Which environment do you want the application to be initialized in?\n\n");
        foreach ($envNames as $i => $name) {
            $this->output->write("  [$i] $name\n");
        }
        $questionText = "  Your choice [0-" . (count($profiles) - 1) . ', or "q" to quit] ';
        $answer = $this->getStyle()->askQuestion(new Question($questionText));
        return $answer;
    }

    private function userConfirm(string $envName)
    {
        $questionText = "  Initialize the application under '{$envName}' environment?";
        $answer = $this->getStyle()->confirm($questionText, false);
        if (!$answer) {
            $this->output->write("  Quit initialization.\n");
            exit(Command::SUCCESS);
        }
    }

    private function validateEnvName(string $envName, $profiles)
    {
        $envNames = array_keys($profiles);
        if (!in_array($envName, $envNames, true)) {
            $envList = implode(', ', $envNames);
            $this->output->write("\n  $envName is not a valid environment. Try one of the following: $envList. \n");
            exit(Command::INVALID);
        }
    }
}
