<?php
namespace App\Message\Command;

use App\Event\EventMessageInterface;



class PendingTaskCommand implements EventMessageInterface 
{
    public function __construct(
        public string $taskId
    )
    {} 
}