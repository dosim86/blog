<?php

namespace App\Command\Worker;

interface WorkerInterface
{
    public function getRegisterWorkers(): array;
}