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

    public function changeStatus()
    {
        $user = Auth::user(); // Retrieve the authenticated user
        $agent = $user->agent;
        if($agent->status == 'offline')
            $agent->status = 'online';
        else
            $agent->status = 'offline';

        $agent->save();

        return response()->json([
            'agent' => $agent,
        ]);
    }

    public function updateLink()
    {
        $user = Auth::user(); // Retrieve the authenticated user
        $agent = $user->agent;
        $data = $request->validate([
            'agent_link' => 'required',
        ]);
        $agent->link = $data['agent_link'];
        $agent->save();

        return response()->json([
            'agent' => $agent,
        ]);
    }

    public function getAllOnlineAgents()
    {
        $agents = Agent::where('status', 'online')->with('user')->get();

        return response()->json([
            'agents' => $agents,
        ]);
    }

    public function getAgentInfo(Request $request)
    {
        $user = Auth::user(); // Retrieve the authenticated user
        $agent = $user->agent;

        if ($agent) {
            return $agent;
        }
        else {
            $agent = new Agent();
            $agent->user_id = $user->id;
            $agent->status = 'offline';
            $agent->link = '';
            $agent->save();
            return $agent;
        }
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
