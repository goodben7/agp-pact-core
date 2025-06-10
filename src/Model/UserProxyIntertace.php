<?php
namespace App\Model;

interface UserProxyIntertace {
    public const PERSON_ADMIN = 'ADM';
    public const PERSON_COMMITTEE = 'COM';
    public const PERSON_NGO = 'NGO';
    public const PERSON_COMPANY = 'ENT';
    public const PERSON_CONTROL_MISSION = 'CTL';
    public const PERSON_INFRASTRUCTURE_CELL = 'INF';
    public const PERSON_WORLD_BANK = 'WBK';
}