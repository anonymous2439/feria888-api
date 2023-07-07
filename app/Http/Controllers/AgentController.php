<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentController extends Controller
{
    /**
     * Store a new agent record.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function getAllOnlineAgents()
    {
        $agents = Agent::whereHas('user', function ($query) {
            $query->where('userTypes.name', 'agent');
        })->where('status', 'online')->get();

        return response()->json([
            'agents' => $agents,
        ]);
    }

     public function getAgentInfo(Request $request)
     {
        $user = Auth::user(); // Retrieve the authenticated user
 
        if ($user->userType && $user->userType->name === 'agent') {
            $agent = Agent::where('user_id', $user->id)->first();

            if ($agent) {
                return response()->json([
                    'agent' => $agent,
                ]);
            }
        }

        return response()->json([
            'message' => 'Agent not found',
        ], 404);
     }
     
    public function storeOrUpdateAgent()
    {
        $data = $request->validate([
            'user_id' => 'required',
        ]);
        $agent = Agent::updateOrCreate(
            ['user_id' => $data['user_id']],
            ['status' => 'offline', 'link' => '']
        );
        return response()->json($agent, 201);
    }
     
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required',
            'link' => 'required',
        ]);

        $agent = Agent::create($validatedData);

        return response()->json($agent, 201);
    }

    /**
     * Update the specified agent record.
     *
     * @param  Request  $request
     * @param  Agent  $agent
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Agent $agent)
    {
        $validatedData = $request->validate([
            'user_id' => 'exists:users,id',
            'status' => 'required',
            'link' => 'required',
        ]);

        $agent->update($validatedData);

        return response()->json($agent, 200);
    }

    /**
     * Delete the specified agent record.
     *
     * @param  Agent  $agent
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Agent $agent)
    {
        $agent->delete();

        return response()->json(null, 204);
    }
}
