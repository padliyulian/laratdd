<?php

namespace App\Repositories;

use App\Repositories\TaskInterface;
use App\Models\Task;

class TaskRepository implements TaskInterface
{
    private $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function index()
    {
        return $this->task->latest()->get();
    }

    public function store($request)
    {
        return $this->task->create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => auth()->user()->id
        ]);
    }

    public function show($task)
    {
        return $this->task->findOrFail($task);
    }
}