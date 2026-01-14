<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
    
        $projects = Project::with('tasks')->latest()->get();

        return Inertia::render('Dashboard', [
            'projects' => $projects
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Project::create($validated);

        return redirect()->route('dashboard');
    }

    public function show(Project $project)
    {
        $project->load('tasks');

        return Inertia::render('Project/Show', [
            'project' => $project
        ]);
    }
}