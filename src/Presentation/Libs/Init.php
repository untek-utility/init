<?php

namespace Untek\Utility\Init\Presentation\Libs;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Untek\Core\Container\Helpers\ContainerHelper;
use Untek\Core\Instance\Helpers\InstanceHelper;
use Untek\Core\Instance\Libs\InstanceProvider;
use Untek\Core\Instance\Libs\Resolvers\InstanceResolver;
use Untek\Framework\Console\Symfony4\Helpers\InputHelper;
use Untek\Utility\Init\Presentation\Cli\Tasks\BaseTask;

class Init
{

    private ?string $profile;
    private $input;
    private $output;
    private $config = [];
    private $params = [];
    private $taskList = [];
    private $container;

    public function __construct(ContainerInterface $container, InputInterface $input, OutputInterface $output, array $config, array $taskList)
    {
        $this->config = $config;
        $this->input = $input;
        $this->output = $output;
        $this->taskList = $taskList;
        $this->container = $container;
//        $containerConfigurator = ContainerHelper::getContainerConfiguratorByContainer($this->container);
        /*$containerConfigurator->singleton(ContainerInterface::class, function () {
            return $this->container;
        });*/
        /*$this->container->singleton(ContainerInterface::class, function () {
            return $this->container;
        });*/
    }

    /**
     * @return string
     */
    public function getProfile(): ?string
    {
        return $this->profile;
    }

    /**
     * @param string $profile
     */
    public function setProfile(?string $profile): void
    {
        $this->profile = $profile;
    }

    private function initParams()
    {
        $this->params['env'] = $this->profile;
        $this->params['overwrite'] = $this->input->getParameterOption('--overwrite');
    }

    public function run()
    {
        if (!extension_loaded('openssl')) {
            die('The OpenSSL PHP extension is required.');
        }

        $this->initParams();
        $this->output->write("Application Initialization Tool\n\n");
        $envName = $this->userInput();
        $this->output->write("\n  Start initialization ...\n\n");

        $root = realpath(__DIR__ . '/../../../../../..');
        $this->runTaskList($root, $envName);

        $this->output->write("\n  ... initialization completed.\n\n");
    }

    private function runTaskList(string $root, string $envName)
    {
        $env = $this->config[$envName];
        foreach ($this->taskList as $callback => $class) {
            /** @var InstanceProvider $instanceProvider */
            $instanceProvider = new InstanceProvider($this->container, new InstanceResolver($this->container));
//            $instanceProvider = $this->container->get(InstanceProvider::class);

            /** @var BaseTask $taskInstance */
            $taskInstance = InstanceHelper::create($class, [
                'input' => $this->input,
                'output' => $this->output,
                'root' => $root,
                'env' => $env,
                'params' => $this->params
            ]);
//            if(is_array($class['class'])) {
//
//            }
//            dump($class['class']);
//            $taskInstance = new $class($this->input,$this->output,$root,$env,$this->params);

//            $taskInstance = $instanceProvider->createInstance($class, [
//                'input' => $this->input,
//                'output' => $this->output,
//                'root' => $root,
//                'env' => $env,
//                'params' => $this->params]);
//            $taskInstance = ClassHelper::createInstance($class, [$this->input, $this->output, $root, $env, $this->params]);
            //$taskInstance = new $class($this->input, $this->output, $root, $env, $this->params);
            $taskInstance->run($env[$callback] ?? []);
        }
    }

    private function userInput()
    {
        if (empty($this->params['env']) || $this->params['env'] === true) {
            $answer = $this->selectEnv();
            $envNames = array_keys($this->config);
            $envName = $envNames[$answer];
        } else {
            $envName = $this->params['env'];
//            dd($envName);
        }
        $this->validateEnvName($envName);
        $this->userConfirm($envName);
        return $envName;
    }

    private function selectEnv(): string
    {
        $envName = null;
        $envNames = array_keys($this->config);
        $this->output->write("Which environment do you want the application to be initialized in?\n\n");
        foreach ($envNames as $i => $name) {
            $this->output->write("  [$i] $name\n");
        }
        $questionText = "\n  Your choice [0-" . (count($this->config) - 1) . ', or "q" to quit] ';
        $answer = InputHelper::question($this->input, $this->output, $questionText);

        if (!ctype_digit($answer) || !in_array($answer, range(0, count($this->config) - 1))) {
            $this->output->write("\n  Quit initialization.\n");
            exit(0);
        }

        return $answer;
    }

    private function validateEnvName(string $envName)
    {
        $envNames = array_keys($this->config);
        if (!in_array($envName, $envNames, true)) {
            $envList = implode(', ', $envNames);
            $this->output->write("\n  $envName is not a valid environment. Try one of the following: $envList. \n");
            exit(2);
        }
    }

    private function userConfirm(string $envName)
    {
        if (empty($this->params['env'])) {
            $questionText = "\n  Initialize the application under '{$envName}' environment? [yes|no] ";
            $answer = InputHelper::question($this->input, $this->output, $questionText);
            if (strncasecmp($answer, 'y', 1)) {
                $this->output->write("\n  Quit initialization.\n");
                exit(0);
            }
        }
    }

}
