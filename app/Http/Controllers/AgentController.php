<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    /**
     * Store a new agent record.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
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
