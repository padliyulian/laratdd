<?php

namespace App\Repositories;


interface TaskInterface
{
    public function index();
    public function show($task);
    public function store($request);
    // public function update($request, $task);
}