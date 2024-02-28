<?php

namespace Untek\Utility\Init\Presentation\Cli\Commands;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Untek\Core\Container\Traits\ContainerAwareAttributeTrait;
use Untek\Core\Contract\Common\Exceptions\NotFoundException;
use Untek\Core\Text\Libs\TemplateRender;
use Untek\Framework\Console\Symfony4\Question\ChoiceQuestion;
use Untek\Framework\Console\Symfony4\Style\SymfonyStyle;
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
        $io = new SymfonyStyle($input, $output);
        $output->writeln("Application Initialization Tool\n");

        $this->setInputOutput($input, $output);

        $overwrite = $input->getOption('overwrite');
        $profile = $input->getOption('profile');
        $configFile = $input->getOption('config');

        $configFile = (new TemplateRender())
            ->addReplacement('ROOT_DIRECTORY', getenv('ROOT_DIRECTORY'))
            ->renderTemplate($configFile);

        $profiles = $this->loadProfiles($configFile);

        if (empty($profile)) {
            $profile = $this->userInput($profiles);
        }

        $profileConfig = $this->findProfile($profile, $profiles);
        $profileConfig['overwrite'] = $overwrite;

        $initLib = new Init($this->getStyle(), $profileConfig);

        $io->writeln("\n  Start initialization ...\n\n");
        $initLib->run();
        $io->success("Initialization completed.");

        return Command::SUCCESS;
    }

    protected function findProfile(string $name, array $profiles): array
    {
        foreach ($profiles as $profile) {
            if ($profile['name'] == $name || $profile['title'] == $name) {
                return $profile;
            }
        }
        throw new NotFoundException('Profile "' . $name . '" not found.');
    }

    protected function loadProfiles(string $configFile): array
    {
        $profiles = require $configFile;
        $newProfiles = [];
        $slugger = new AsciiSlugger();
        foreach ($profiles as $profile) {
            if (empty($profile['title'])) {
                throw new \Exception('Title of profile is empty.');
            }
            $profile['name'] = $slugger->slug($profile['title'])->lower()->toString();
            $newProfiles[$profile['title']] = $profile;
        }
        return $newProfiles;
    }

    private function userInput($profiles)
    {
        $envName = $this->selectEnv($profiles);
        if ($envName === null) {
            $this->output->write("\n  Quit initialization.\n");
            exit(Command::SUCCESS);
        }
        $this->validateEnvName($envName, $profiles);
        $this->userConfirm($envName);
        return $envName;
    }

    private function selectEnv($profiles): ?string
    {
        $envNames = array_keys($profiles);
        $question = new ChoiceQuestion(
            'Which environment do you want the application to be initialized in?',
            $envNames,
            0
        );
        return $this->getStyle()->askQuestion($question);
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
