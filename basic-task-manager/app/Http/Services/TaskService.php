<?php

namespace App\Http\Services;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function index()
    {
        return auth()->user()->tasks;
    }

    public function create(Request $request)
    {
        $task = Task::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'due_date' => $request->input('due_date'),
            'status' => $request->input('status'),
        ]);

        $task->users()->attach(Auth::id());
    }

    public function edit($id)
    {
        $task = Task::findOrFail($id);
        return $task;
    }

    public function update(Request $request, $id)
    {
        try {
            $task = Task::findOrFail($id);
            $task->update($request->all());
            return $task;
        } catch (ModelNotFoundException $e) {
            throw new Exception("Task not found", 404);
        } catch (Exception $e) {
            throw new Exception("An error occurred while updating the task", 500);
        }
    }

    public function destroy($id)
    {
        try {
            $task = Task::findOrFail($id);
            $task->delete();
        } catch (ModelNotFoundException $e) {
            throw new Exception("Task not found", 404);
        } catch (Exception $e) {
            throw new Exception("An error occurred while deleting the task", 500);
        }
    }

    public function list($sortColumn, $sortDirection)
    {        
        return auth()->user()->tasks()->orderBy($sortColumn, $sortDirection)->get();
    }
}
