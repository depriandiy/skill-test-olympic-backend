<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'candidate_id' => $this->candidate_id,
            'job_id' => $this->job_id,
            'apply_date' => $this->apply_date?->toDateString(),
            'status' => $this->status,
            'candidate' => new CandidateResource($this->whenLoaded('candidate')),
            'job' => new JobResource($this->whenLoaded('job')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
