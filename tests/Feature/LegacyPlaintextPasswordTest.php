<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LegacyPlaintextPasswordTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'session.driver' => 'array',
        ]);
        DB::purge('sqlite');

        Schema::connection('sqlite')->create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('customer');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('profile_image')->nullable();
            $table->timestamps();
        });
    }

    public function test_legacy_plaintext_admin_password_is_hashed_after_successful_login(): void
    {
        $user = User::create([
            'name' => 'Legacy Admin',
            'email' => 'legacy-admin@example.com',
            'password' => 'legacy-password-123',
            'role' => 'admin',
        ]);

        $this->post('/login', [
            'email' => 'legacy-admin@example.com',
            'password' => 'legacy-password-123',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
        $user->refresh();
        $this->assertNotSame('legacy-password-123', $user->password);
        $this->assertTrue(Hash::check('legacy-password-123', $user->password));
    }

    public function test_hashed_password_login_uses_the_normal_authentication_path(): void
    {
        $user = User::create([
            'name' => 'Hashed Admin',
            'email' => 'hashed-admin@example.com',
            'password' => Hash::make('hashed-password-123'),
            'role' => 'admin',
        ]);

        $this->post('/login', [
            'email' => 'hashed-admin@example.com',
            'password' => 'hashed-password-123',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
        $user->refresh();
        $this->assertTrue(Hash::check('hashed-password-123', $user->password));
    }
}
