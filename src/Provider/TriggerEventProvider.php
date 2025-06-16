<?php

namespace App\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\TriggerEventResource;
use App\Manager\TriggerEventManager;
use App\Model\Permission;

class TriggerEventProvider implements ProviderInterface
{

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $list = [];
        /** @var Permission $p */
        foreach (TriggerEventManager::getInstance()->getTriggerEvents() as $p) {
            $list[] = TriggerEventResource::fromModel($p);
        }

        return $list;
    }
}
