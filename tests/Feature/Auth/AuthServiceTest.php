<?php

namespace Tests\Feature\Auth;

use App\Interfaces\AuthServiceInterface;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AuthServiceInterface $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(AuthServiceInterface::class);
    }

    /** @test */
    public function it_authenticates_web_users_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $result = $this->service->attemptWeb([
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertTrue($result);
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function it_fails_web_authentication_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
        ]);

        $result = $this->service->attemptWeb([
            'email' => 'user@example.com',
            'password' => 'wrong-password',
        ]);

        $this->assertFalse($result);
        $this->assertGuest();
    }

    /** @test */
    public function it_authenticates_api_user_and_returns_token(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $result = $this->service->attemptApi([
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertInstanceOf(User::class, $result);
        $this->assertNotEmpty($result->token);
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function it_throws_exception_for_invalid_api_credentials(): void
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->service->attemptApi([
            'email' => 'user@example.com',
            'password' => 'wrong-password',
        ]);
    }

    /** @test */
    public function it_logs_out_user_and_invalidates_session(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        $this->be($user); // authenticate user

        $request = \Illuminate\Http\Request::create('/', 'POST');
        $request->setLaravelSession(session());

        session()->put('foo', 'bar'); // before logout
        $this->assertEquals('bar', session()->get('foo'));

        $this->service->logout($request);

        $this->assertGuest();
        $this->assertFalse(session()->has('foo')); // session invalidated
    }
}
