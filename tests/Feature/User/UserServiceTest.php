<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    protected UserService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UserService();
    }

    public function test_it_creates_user_with_hashed_password()
    {
        $data = [
            'name' => 'Test',
            'email' => 'test@example.com',
            'username' => 'tester',
            'role' => 'dashboard',
            'password' => 'secret123',
        ];

        $user = $this->service->create($data);

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
        $this->assertTrue(Hash::check('secret123', $user->password));
        $this->assertEquals('dashboard', $user->role);
    }

    public function test_it_updates_user_and_hashes_password_if_present()
    {
        $user = User::factory()->create();

        $updated = $this->service->update($user->id, ['name' => 'Updated', 'password' => 'newpass123']);

        $this->assertEquals('Updated', $updated->name);
        $this->assertTrue(Hash::check('newpass123', $updated->password));
    }

    public function test_it_updates_user_without_changing_password()
    {
        $user = User::factory()->create(['password' => Hash::make('original')]);

        $updated = $this->service->update($user->id, ['name' => 'NoPassChange']);

        $this->assertEquals('NoPassChange', $updated->name);
        $this->assertTrue(Hash::check('original', $updated->password));
    }

    public function test_it_finds_user_by_id()
    {
        $user = User::factory()->create();
        $found = $this->service->findById($user->id);

        $this->assertNotNull($found);
        $this->assertEquals($user->id, $found->id);
    }

    public function test_it_deletes_user()
    {
        $user = User::factory()->create();
        $this->service->delete($user->id);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_it_filters_users()
    {
        User::factory()->create(['name' => 'Ali', 'email' => 'ali@site.com', 'username' => 'aliuser', 'role' => 'dashboard']);
        User::factory()->create(['name' => 'Zara', 'email' => 'zara@site.com', 'username' => 'zarauser', 'role' => 'api']);

        $result = $this->service->getAll(['name' => 'Ali']);
        $this->assertCount(1, $result);
        $this->assertEquals('Ali', $result->first()->name);
    }
}
