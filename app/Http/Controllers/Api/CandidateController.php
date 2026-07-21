<?php

namespace App\Http\Controllers\Api;

use App\Exports\CandidatesExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCandidateRequest;
use App\Http\Requests\UpdateCandidateRequest;
use App\Http\Resources\CandidateResource;
use App\Imports\CandidatesImport;
use App\Models\Candidate;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CandidateController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Candidate::query();

        if ($user->hasRole(Role::CANDIDATE)) {
            $query->whereKey($user->candidate_id);
        } elseif (! $user->hasRole(Role::HR_ADMIN, Role::HR_STAFF, Role::INTERVIEWER)) {
            abort(403);
        }

        $query
            ->when($request->query('search'), function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('full_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($request->query('status'), fn ($query, string $status) => $query->where('status', $status));

        $sort = in_array($request->query('sort'), ['full_name', 'email', 'status', 'created_at'], true)
            ? $request->query('sort')
            : 'created_at';
        $direction = $request->query('direction') === 'asc' ? 'asc' : 'desc';

        return CandidateResource::collection(
            $query->orderBy($sort, $direction)->paginate((int) $request->query('per_page', 15))
        );
    }

    public function store(StoreCandidateRequest $request): JsonResponse
    {
        $this->ensureHrCanWrite($request);

        $data = $request->validated();
        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('candidates', 'public');
        }
        unset($data['photo']);

        $candidate = Candidate::query()->create($data);

        return (new CandidateResource($candidate))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, Candidate $candidate): CandidateResource
    {
        $user = $request->user();
        if ($user->hasRole(Role::CANDIDATE) && $user->candidate_id !== $candidate->id) {
            abort(403);
        }
        if (! $user->hasRole(Role::HR_ADMIN, Role::HR_STAFF, Role::INTERVIEWER, Role::CANDIDATE)) {
            abort(403);
        }

        return new CandidateResource($candidate->load('applications.job'));
    }

    public function update(UpdateCandidateRequest $request, Candidate $candidate): CandidateResource
    {
        $this->ensureHrCanWrite($request);

        $data = $request->validated();
        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('candidates', 'public');
        }
        unset($data['photo']);

        $candidate->update($data);

        return new CandidateResource($candidate->refresh());
    }

    public function destroy(Request $request, Candidate $candidate): JsonResponse
    {
        $this->ensureHrCanWrite($request);
        $candidate->delete();

        return response()->json(['message' => 'Candidate deleted.']);
    }

    public function export(Request $request): BinaryFileResponse
    {
        if (! $request->user()->hasRole(Role::HR_ADMIN, Role::HR_STAFF)) {
            abort(403);
        }

        return Excel::download(new CandidatesExport, 'candidates.xlsx');
    }

    public function import(Request $request): JsonResponse
    {
        if (! $request->user()->hasRole(Role::HR_ADMIN, Role::HR_STAFF)) {
            abort(403);
        }

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        Excel::import(new CandidatesImport, $request->file('file'));

        return response()->json(['message' => 'Candidates imported.'], 201);
    }

    private function ensureHrCanWrite(Request $request): void
    {
        if (! $request->user()->hasRole(Role::HR_ADMIN, Role::HR_STAFF)) {
            abort(403);
        }
    }
}
