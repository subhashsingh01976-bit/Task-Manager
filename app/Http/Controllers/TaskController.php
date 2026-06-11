<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    // Get all tasks
    public function index()
    {
        return Task::all();
    }

    // Create new task
    public function store(Request $req)
    {
        $task = Task::create([
            'title' => $req->title,
            'description' => $req->description,
            'is_completed' => false
        ]);

        return response()->json($task);
    }

    // ✅ FIXED Update task (IMPORTANT)
    public function update(Request $req, $id)
    {
        $task = Task::findOrFail($id);

        // Only update fields that are sent
        if ($req->has('title')) {
            $task->title = $req->title;
        }

        if ($req->has('description')) {
            $task->description = $req->description;
        }

        if ($req->has('is_completed')) {
            $task->is_completed = $req->is_completed;
        }

        $task->save();

        return response()->json($task);
    }

    // Delete task
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json([
            "message" => "Task Deleted Successfully"
        ]);
    }
}