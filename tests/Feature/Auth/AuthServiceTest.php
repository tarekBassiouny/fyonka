<?php

namespace Tests\Feature\Auth;

use App\Interfaces\AuthServiceInterface;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AuthServiceInterface $service;
    protected User $webUser;
    protected User $apiUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(AuthServiceInterface::class);
        $this->webUser = User::factory()->create([
            'username' => 'admin',
            'password' => bcrypt('password'),
            'role' => 'dashboard'
        ]);
        $this->apiUser = User::factory()->create([
            'username' => 'api',
            'password' => bcrypt('password'),
            'role' => 'api'
        ]);
    }

    /** @test */
    public function it_authenticates_web_users_with_valid_credentials(): void
    {
        $result = $this->service->attemptWeb([
            'username' => $this->webUser->username,
            'password' => 'password',
        ]);

        $this->assertTrue($result);
        $this->assertAuthenticatedAs($this->webUser);
    }

    /** @test */
    public function it_fails_web_authentication_with_invalid_credentials(): void
    {
        $result = $this->service->attemptWeb([
            'username' => $this->webUser->username,
            'password' => 'wrong-password',
        ]);

        $this->assertFalse($result);
        $this->assertGuest();
    }

    /** @test */
    public function it_authenticates_api_user_and_returns_token(): void
    {
        $result = $this->service->attemptApi([
            'username' => $this->apiUser->username,
            'password' => 'password',
        ]);

        $this->assertInstanceOf(User::class, $result);
        $this->assertNotEmpty($result->token);
        $this->assertAuthenticatedAs($this->apiUser);
    }

    /** @test */
    public function it_throws_exception_for_invalid_api_credentials(): void
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $this->service->attemptApi([
            'username' => $this->apiUser->username,
            'password' => 'wrong-password',
        ]);
    }

    /** @test */
    public function it_logs_out_user_and_invalidates_session(): void
    {
        $this->be($this->webUser); // authenticate user

        $request = Request::create('/', 'POST');
        $request->setLaravelSession(session());

        session()->put('foo', 'bar'); // before logout
        $this->assertEquals('bar', session()->get('foo'));

        $this->service->logout($request);

        $this->assertGuest();
        $this->assertFalse(session()->has('foo')); // session invalidated
    }

    /** @test */
    public function it_denies_dashboard_login_for_api_users(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage(__('auth.dashboard_login_not_allowed'));

        $this->service->attemptWeb([
            'username' => $this->apiUser->username,
            'password' => 'password',
        ]);
    }

    /** @test */
    public function it_denies_api_login_for_dashboard_users(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage(__('auth.api_login_not_allowed'));

        $this->service->attemptApi([
            'username' => $this->webUser->username,
            'password' => 'password',
        ]);
    }
}
