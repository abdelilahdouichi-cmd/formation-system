<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class LogAdminActions
{
    protected array $logMethods = ['POST', 'PUT', 'PATCH', 'DELETE'];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->shouldLog($request)) {
            return $response;
        }

        $user = $request->user();
        if (! $user) {
            return $response;
        }

        DB::table('audit_logs')->insert([
            'user_id' => $user->id,
            'method' => $request->method(),
            'path' => $request->path(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'action' => $this->determineAction($request),
            'model_type' => $this->getModelType($request),
            'model_id' => $this->getModelId($request),
            'response_code' => $response->getStatusCode(),
            'created_at' => now(),
        ]);

        return $response;
    }

    private function shouldLog(Request $request): bool
    {
        return in_array($request->method(), $this->logMethods, true)
            && str_starts_with($request->path(), 'admin');
    }

    private function determineAction(Request $request): string
    {
        return match ($request->method()) {
            'POST' => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default => 'unknown',
        };
    }

    private function getModelType(Request $request): ?string
    {
        if (str_contains($request->path(), 'admin/users')) {
            return 'User';
        }

        return null;
    }

    private function getModelId(Request $request): ?int
    {
        if (str_contains($request->path(), 'admin/users')) {
            $id = $request->route('user')?->id;
            if ($id) {
                return $id;
            }

            return null;
        }

        return null;
    }
}
