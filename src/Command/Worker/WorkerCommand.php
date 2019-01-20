<?php

namespace App\Command\Worker;

use App\Exception\Worker\WorkerNotFoundException;
use App\Exception\Worker\WorkersNotRegisteredException;
use GearmanJob;
use App\Service\QueueService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class WorkerCommand extends Command
{
    protected static $defaultName = 'app:worker:run';

    private $queueService;

    private $container;

    private $logger;

    private $workerObject;

    public function __construct(
        QueueService $queueService,
        ContainerInterface $container,
        LoggerInterface $appLogger
    ) {
        $this->queueService = $queueService;
        $this->container = $container;
        $this->logger = $appLogger;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'worker name')
            ->setDescription('Worker executor')
        ;
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $workerObject = $this->getWorkerObject($input);
        if (empty($workers = $workerObject->getRegisterWorkers())) {
            throw new WorkersNotRegisteredException();
        }

        $workerService = $this->queueService->getWorker();
        foreach ($workers as $id => $method) {
            if (!method_exists($workerObject, $method)) {
                continue;
            }

            $workerService->addFunction($id, function (GearmanJob $job) use ($id, $workerObject, $method) {
                try {
                    $data = unserialize($job->workload());
                    call_user_func_array([$workerObject, $method], [$data, $job]);
                } catch (\Exception $e) {
                    $this->logger->error("[worker:{$id}] " . $e->getMessage());
                }
            });
        }

        while ($workerService->work()) {
            if ($workerService->returnCode() != GEARMAN_SUCCESS) {
                break;
            }
        }
    }

    /**
     * @throws \Exception
     */
    private function getWorkerObject(InputInterface $input): WorkerInterface
    {
        $workerName = $input->getArgument('name');
        $workerClass = 'App\\Worker\\' . ucfirst($workerName) . 'Worker';

        if (!$this->container->has($workerClass)) {
            throw new WorkerNotFoundException();
        }

        $this->workerObject = $this->container->get($workerClass);
        if (!$this->workerObject instanceof WorkerInterface) {
            throw new WorkerNotFoundException();
        }

        return $this->workerObject;
    }
}
