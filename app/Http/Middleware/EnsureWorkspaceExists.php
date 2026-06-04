<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureWorkspaceExists
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->workspaces()->count() === 0) {
            $request->user()->workspaces()->create([
                'name' => $request->user()->company_name ?? $request->user()->name,
            ]);
        }

        return $next($request);
    }
}
