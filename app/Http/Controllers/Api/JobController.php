<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;
use App\Http\Resources\JobResource;
use App\Models\Job;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        if (! $request->user()->hasRole(Role::HR_ADMIN, Role::HR_STAFF, Role::CANDIDATE)) {
            abort(403);
        }

        $query = Job::query()
            ->when($request->query('search'), function ($query, string $search): void {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%");
            })
            ->when($request->has('is_active'), fn ($query) => $query->where('is_active', $request->boolean('is_active')));

        $sort = in_array($request->query('sort'), ['title', 'department', 'created_at'], true)
            ? $request->query('sort')
            : 'created_at';
        $direction = $request->query('direction') === 'asc' ? 'asc' : 'desc';

        return JobResource::collection(
            $query->orderBy($sort, $direction)->paginate((int) $request->query('per_page', 15))
        );
    }

    public function store(StoreJobRequest $request): JsonResponse
    {
        $this->ensureHrCanWrite($request);
        $job = Job::query()->create($request->validated());

        return (new JobResource($job))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, Job $job): JobResource
    {
        if (! $request->user()->hasRole(Role::HR_ADMIN, Role::HR_STAFF, Role::CANDIDATE)) {
            abort(403);
        }

        return new JobResource($job);
    }

    public function update(UpdateJobRequest $request, Job $job): JobResource
    {
        $this->ensureHrCanWrite($request);
        $job->update($request->validated());

        return new JobResource($job->refresh());
    }

    public function destroy(Request $request, Job $job): JsonResponse
    {
        $this->ensureHrCanWrite($request);
        $job->delete();

        return response()->json(['message' => 'Job deleted.']);
    }

    private function ensureHrCanWrite(Request $request): void
    {
        if (! $request->user()->hasRole(Role::HR_ADMIN, Role::HR_STAFF)) {
            abort(403);
        }
    }
}
