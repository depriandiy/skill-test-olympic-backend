<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\UpdateApplicationRequest;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Application::query()->with('candidate', 'job');

        if ($user->hasRole(Role::CANDIDATE)) {
            $query->where('candidate_id', $user->candidate_id);
        } elseif (! $user->hasRole(Role::HR_ADMIN)) {
            abort(403);
        }

        return ApplicationResource::collection(
            $query->latest()->paginate((int) $request->query('per_page', 15))
        );
    }

    public function store(StoreApplicationRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        if ($user->hasRole(Role::CANDIDATE)) {
            if ($user->candidate_id !== (int) $data['candidate_id']) {
                abort(403);
            }
            $data['status'] = 'submitted';
        } elseif (! $user->hasRole(Role::HR_ADMIN)) {
            abort(403);
        }

        $data['apply_date'] ??= now()->toDateString();
        $data['status'] ??= 'submitted';

        $application = Application::query()->create($data)->load('candidate', 'job');
        $this->syncCandidateStatus($application);

        return (new ApplicationResource($application))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, Application $application): ApplicationResource
    {
        $user = $request->user();
        if ($user->hasRole(Role::CANDIDATE) && $user->candidate_id !== $application->candidate_id) {
            abort(403);
        }
        if (! $user->hasRole(Role::HR_ADMIN, Role::CANDIDATE)) {
            abort(403);
        }

        return new ApplicationResource($application->load('candidate', 'job'));
    }

    public function update(UpdateApplicationRequest $request, Application $application): ApplicationResource
    {
        if (! $request->user()->hasRole(Role::HR_ADMIN)) {
            abort(403);
        }

        $application->update($request->validated());
        $this->syncCandidateStatus($application->refresh());

        return new ApplicationResource($application->load('candidate', 'job'));
    }

    private function syncCandidateStatus(Application $application): void
    {
        $candidateStatus = match ($application->status) {
            'submitted', 'reviewed' => 'screening',
            'interview' => 'interview',
            'accepted' => 'accepted',
            'rejected' => 'rejected',
            default => 'new',
        };

        $application->candidate()->update(['status' => $candidateStatus]);
    }
}
