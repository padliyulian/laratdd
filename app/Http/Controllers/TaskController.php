<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Services\Task as TaskService;
use App\Http\Requests\Task as TaskRequest;

class TaskController extends Controller
{
    private $TaskService;

    public function __construct(TaskService $TaskService)
    {
        $this->TaskService = $TaskService;
    }

    public function index()
    {
        $tasks = $this->TaskService->index();
        return view('tasks.index',compact('tasks'));
    }

    public function create()
    {
        return view('tasks.create');
    }

    public function store(TaskRequest $request)
    {
        $task = $this->TaskService->store($request);
        return redirect('/tasks/'.$task->id);
    }

    public function show($task)
    {
        $task = $this->TaskService->show($task);
        return view('tasks.show',compact('task'));
    }

    public function edit(Task $task)
    {
        return view('tasks.edit',compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);
        $task->update($request->all());
        return redirect('/tasks/'.$task->id);
    }

    public function destroy(Task $task)
    {
        $this->authorize('update', $task);
        if ($task->delete()) {
            return redirect('/tasks');
        }
    }
}
