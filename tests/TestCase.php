<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Authenticate as the given user and return JWT token.
     */
    protected function authenticateAs(User $user): string
    {
        $token = JWTAuth::fromUser($user);

        return "Bearer {$token}";
    }

    /**
     * Make request with JWT authentication header.
     */
    protected function withJwtAuth(User $user)
    {
        return $this->withHeader('Authorization', $this->authenticateAs($user));
    }
}
