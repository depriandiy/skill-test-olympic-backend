<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Skill Test Olympic Backend API',
    description: 'Laravel REST API for HR recruitment with Sanctum auth, RBAC, candidates, jobs, applications, dashboard, import, and export.'
)]
#[OA\Server(url: 'http://localhost:8000/api', description: 'Local API server')]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Sanctum'
)]
#[OA\Get(
    path: '/me',
    summary: 'Get authenticated user profile',
    security: [['bearerAuth' => []]],
    tags: ['Authentication'],
    responses: [
        new OA\Response(response: 200, description: 'Authenticated user profile'),
        new OA\Response(response: 401, description: 'Unauthenticated'),
    ]
)]
#[OA\Post(
    path: '/login',
    summary: 'Login and receive Sanctum bearer token',
    tags: ['Authentication'],
    responses: [
        new OA\Response(response: 200, description: 'Login successful'),
        new OA\Response(response: 401, description: 'Invalid credentials'),
    ]
)]
class ApiDocumentation
{
}
