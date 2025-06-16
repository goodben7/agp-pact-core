<?php

namespace App\Manager;

use App\Model\TriggerEvent;

class TriggerEventManager
{
    private static ?TriggerEventManager $instance = null;

    private ?string $projectDir = null;

    public function __construct()
    {
        if (null === $this->projectDir) {
            $r = new \ReflectionObject($this);

            if (!is_file($dir = $r->getFileName())) {
                throw new \LogicException(sprintf('Cannot auto-detect project dir for kernel of class "%s".', $r->name));
            }

            $dir = $rootDir = \dirname($dir);
            while (!is_file($dir . '/composer.json')) {
                if ($dir === \dirname($dir)) {
                    return $this->projectDir = $rootDir;
                }
                $dir = \dirname($dir);
            }
            $this->projectDir = $dir;
        }
    }

    public static function getInstance(): TriggerEventManager
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return array<TriggerEvent>
     */
    public function getTriggerEvents(): iterable
    {
        $list = require sprintf('%s/config/trigger_event.php', $this->projectDir);

        return $list();
    }

    public function getTriggerEventsAsListChoices(): iterable
    {
        $choices = [];
        /** @var TriggerEvent $p */
        foreach ($this->getTriggerEvents() as $p) {
            $choices[$p->getLabel()] = $p->getTriggerEventId();
        }

        return $choices;
    }
}
