<?php

namespace App\Model;

interface TaskRunnerInterface
{
    public function support(string $type): bool;
    
    public function run(TaskInterface $task): void;
}