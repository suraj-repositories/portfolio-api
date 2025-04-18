<?php

namespace App\Http\Controllers\Github;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\GithubServiceInterface;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    protected $githubService;

    public function __construct(GithubServiceInterface $githubService)
    {
        $this->githubService = $githubService;
    }
    //

    /**
     * Fetch Projects
     */

    public function fetchProjects(Request $request)
    {
        try {
            return response()->json(
                Project::orderBy('id', 'desc')
                    ->select(
                        'id',
                        'name',
                        'url',
                        'description',
                        'language',
                        'stars',
                        'image',
                        'created_at',
                        'updated_at'
                    )
                    ->get()
            );
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch projects',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function fetchPopularProjects(Request $request)
    {
        try {
            return response()->json(
                Project::orderBy('stars', 'desc')
                    ->select(
                        'id',
                        'name',
                        'url',
                        'description',
                        'language',
                        'stars',
                        'image',
                        'created_at',
                        'updated_at'
                    )
                    ->take($request->size ?? 5)
                    ->get()
            );
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch popular projects',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function fetchProjectById(Project $project)
    {
        try {
            $data = $project->only([
                'id',
                'name',
                'url',
                'description',
                'language',
                'stars',
                'image',
                'created_at',
                'updated_at'
            ]);

            return response()->json($data);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Failed to fetch project',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function fetchProjectLanguages(Project $project)
    {
        try {
            return response()->json(
                $project->languages->mapWithKeys(fn($lang) => [
                    $lang->language->name => $lang->percentage
                ])
            );

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch project languages',
                'message' => $e->getMessage()
            ], 500);
        }
    }
public function fetchReadmeContent(Project $project)
{
    try {
        $content = $project->readme?->content;

        if (is_null($content)) {
            return response()->json([
                'error' => 'Readme not found',
                'message' => 'This project does not have a readme file.'
            ], 404);
        }

        return response()->json($content);
    } catch (\Throwable $e) {
        return response()->json([
            'error' => 'Failed to fetch readme content',
            'message' => $e->getMessage()
        ], 500);
    }
}

    public function fetchProfileReadmeContent()
    {
        try {
            return response()->json($this->githubService->getProfileReadmeContent());
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch readme content',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
