<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AgentApiKey
{
    public function handle(Request $request, Closure $next)
    {
        $key = $request->bearerToken();

        if (!$key || $key !== config('app.agent_api_key')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
