<?php

namespace App\Service;

use App\Lib\Helper;
use GearmanClient;
use GearmanWorker;
use App\Lib\Traits\ManagerTrait;
use Psr\Container\ContainerInterface;

class QueueService
{
    use ManagerTrait;

    const LOW_PRIORITY = -1;
    const NORMAL_PRIORITY = 0;
    const HIGH_PRIORITY = 1;

    private const CONFIG_IP = 'ip';
    private const CONFIG_PORT = 'port';

    private $config = [];

    private $client;

    private $worker;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getClient(): GearmanClient
    {
        if (empty($this->client)) {
            $this->client = new GearmanClient();
            $this->client->addServer(
                $this->getConfig(self::CONFIG_IP),
                $this->getConfig(self::CONFIG_PORT)
            );
        }

        return $this->client;
    }

    public function getWorker(): GearmanWorker
    {
        if (empty($this->worker)) {
            $this->worker = new GearmanWorker();
            $this->worker->addServer();
        }

        return $this->worker;
    }

    /**
     * @throws \Exception
     */
    public function addTask($id, array $data, $priority = self::NORMAL_PRIORITY)
    {
        $data['_uniq_id'] = Helper::randomHash32();
        $data = serialize($data);

        switch ($priority) {
            case self::LOW_PRIORITY:
                $this->getClient()->doLowBackground($id, $data);
                break;
            case self::HIGH_PRIORITY:
                $this->getClient()->doHighBackground($id, $data);
                break;
            default:
                $this->getClient()->doBackground($id, $data);
        }
    }

    private function getConfig($name)
    {
        if (empty($this->config)) {
            $config = $this->getParameter('app.gearman.config');
            [$ip, $port] = explode(':', $config);

            $this->config[self::CONFIG_IP] = $ip;
            $this->config[self::CONFIG_PORT] = $port;
        }

        return $this->config[$name] ?? null;
    }
}