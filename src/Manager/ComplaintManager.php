<?php

namespace App\Manager;


use App\Entity\Complaint;

class ComplaintManager
{
    public function __construct(
    )
    {
    }

    public function create(): Complaint
    {
        $complaint = new Complaint();

        return $complaint;
    }
}
