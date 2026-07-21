<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Job;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        if (! $request->user()->hasRole(Role::HR_ADMIN, Role::HR_STAFF)) {
            abort(403);
        }

        return response()->json([
            'data' => [
                'total_candidates' => Candidate::query()->count(),
                'total_jobs' => Job::query()->count(),
                'candidates_per_status' => Candidate::query()
                    ->selectRaw('status, count(*) as total')
                    ->groupBy('status')
                    ->pluck('total', 'status')
                    ->map(fn ($total) => (int) $total)
                    ->all(),
            ],
        ]);
    }
}
