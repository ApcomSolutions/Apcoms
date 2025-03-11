<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TeamService;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    protected $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    public function index()
    {
        $teams = $this->teamService->getAllTeams();
        return response()->json($teams, 200);
    }

    public function active()
    {
        $teams = $this->teamService->getActiveTeams();
        return response()->json($teams, 200);
    }

    public function show($id)
    {
        $team = $this->teamService->getTeamById($id);
        return response()->json($team, 200);
    }

    public function store(Request $request)
    {
        try {
            $team = $this->teamService->createTeam($request);

            return response()->json([
                'message' => 'Team member successfully created',
                'data' => $team
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating team member: ' . $e->getMessage()
            ], 422);
        }
    }

    public function update(Request $request, $id)
    {
        $team = $this->teamService->updateTeam($request, $id);

        return response()->json([
            'message' => 'Team member successfully updated',
            'data' => $team
        ], 200);
    }

    public function destroy($id)
    {
        $result = $this->teamService->deleteTeam($id);

        return response()->json([
            'message' => $result['message']
        ], 200);
    }

    public function updateOrder(Request $request)
    {
        $result = $this->teamService->updateOrder($request);

        return response()->json([
            'message' => $result['message']
        ], 200);
    }
}
