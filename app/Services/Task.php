<?php

namespace App\Services;

use App\Repositories\TaskInterface;

class Task 
{
    private $TaskInterface;

    public function __construct(TaskInterface $TaskInterface)
    {
        $this->TaskInterface = $TaskInterface;
    }

    public function index()
    {
        return $this->TaskInterface->index();
    }

    public function store($request)
    {
        return $this->TaskInterface->store($request);
    }

    public function show($task)
    {
        return $this->TaskInterface->show($task);
    }
}