<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\Models\Task;

class TasksTest extends TestCase
{
    use DatabaseMigrations;

    public function testGetTasks()
    {
        //Given we have task in the database
        $task = factory('App\Models\Task')->create();

        //When user visit the tasks page
        $response = $this->get('/tasks');
        
        //He should be able to read the task
        $response->assertSee($task->title);
    }

    public function testGetTask()
    {
        //Given we have task in the database
        $task = factory('App\Models\Task')->create();

        //When user visit the task's URI
        $response = $this->get('/tasks/'.$task->id);

        //He can see the task details
        $response->assertSee($task->title)->assertSee($task->description);
    }

    public function testAddTaskNoAuth()
    {
        //Given we have a task object
        $task = factory('App\Models\Task')->make();

        //When unauthenticated user submits post request to create task endpoint
        // He should be redirected to login page
        $this->post('/tasks',$task->toArray())->assertRedirect('/login');
    }

    public function testAddTaskWithAuth()
    {
        //Given we have an authenticated user
        $this->actingAs(factory('App\Models\User')->create());

        //And a task object
        $task = factory('App\Models\Task')->make();

        //When user submits post request to create task endpoint
        $this->post('/tasks',$task->toArray());

        //It gets stored in the database
        $this->assertEquals(1,Task::all()->count());
    }

    public function testUpdateTaskNoAuth(){
        //Given we have a signed in user
        $this->actingAs(factory('App\Models\User')->create());
        
        //And a task which is not created by the user
        $task = factory('App\Models\Task')->create();
        $task->title = "Updated Title";

        //When the user hit's the endpoint to update the task
        $response = $this->patch('/tasks/'.$task->id, $task->toArray());

        //We should expect a 403 error
        $response->assertStatus(403);
    }

    public function testUpdateTaskWithAuth(){
        //Given we have a signed in user
        $this->actingAs(factory('App\Models\User')->create());

        //And a task which is created by the user
        $task = factory('App\Models\Task')->create(['user_id' => auth()->user()->id]);
        $task->title = "Updated Title";

        //When the user hit's the endpoint to update the task
        $this->patch('/tasks/'.$task->id, $task->toArray());

        //The task should be updated in the database.
        $this->assertDatabaseHas('tasks',['id'=> $task->id , 'title' => 'Updated Title']);
    }

    public function testDeleteTaskNoAuth(){
        //Given we have a signed in user
        $this->actingAs(factory('App\Models\User')->create());

        //And a task which is not created by the user
        $task = factory('App\Models\Task')->create();

        //When the user hit's the endpoint to delete the task
        $response = $this->delete('/tasks/'.$task->id);

        //We should expect a 403 error
        $response->assertStatus(403);
    }
    
    public function testDeleteTaskWithAuth(){
        //Given we have a signed in user
        $this->actingAs(factory('App\Models\User')->create());

        //And a task which is created by the user
        $task = factory('App\Models\Task')->create(['user_id' => auth()->user()->id]);

        //When the user hit's the endpoint to delete the task
        $this->delete('/tasks/'.$task->id);

        //The task should be deleted from the database.
        $this->assertDatabaseMissing('tasks',['id'=> $task->id]);

    }

    public function testAddTaskRequireTitle(){
        $this->actingAs(factory('App\Models\User')->create());
        $task = factory('App\Models\Task')->make(['title' => null]);
        $this->post('/tasks',$task->toArray())->assertSessionHasErrors('title');
    }

    public function testAddTaskRequireDescription(){
        $this->actingAs(factory('App\Models\User')->create());
        $task = factory('App\Models\Task')->make(['description' => null]);
        $this->post('/tasks',$task->toArray())->assertSessionHasErrors('description');
    }
}
