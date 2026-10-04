<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SupabaseAuthTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->assertSame('sqlite', DB::connection()->getDriverName());
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));

        Schema::dropIfExists('users');
        Schema::create('users', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('full_name')->nullable();
            $table->string('email')->unique();
            $table->text('avatar_url')->nullable();
            $table->timestampTz('created_at')->useCurrent();
        });

        config([
            'services.supabase.url' => 'https://project.example.test',
            'services.supabase.anon_key' => 'public-test-key',
        ]);
    }

    public function test_sign_in_and_registration_pages_render_the_supabase_account_controls(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('data-supabase-form="login"', false)
            ->assertSee('data-supabase-google', false)
            ->assertSee('data-anon-key="public-test-key"', false);

        $this->get('/register')
            ->assertOk()
            ->assertSee('data-supabase-form="register"', false)
            ->assertSee('Use at least 12 characters.');
    }

    public function test_auth_callback_uses_https_behind_a_tls_terminating_proxy(): void
    {
        $this->withServerVariables([
            'HTTP_X_FORWARDED_FOR' => '203.0.113.10',
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'HTTP_X_FORWARDED_PORT' => '443',
        ])
            ->get('/login')
            ->assertOk()
            ->assertSee('data-callback-url="https://localhost/auth/callback"', false);
    }

    public function test_a_verified_supabase_user_is_synchronized_and_given_a_laravel_session(): void
    {
        $userId = 'a4d7c66f-f343-4c5b-9bb2-e95448d6c3bb';
        Http::fake([
            'https://project.example.test/auth/v1/user' => Http::response([
                'id' => $userId,
                'email' => '  DEVELOPER@example.com ',
                'user_metadata' => [
                    'full_name' => 'Test Developer',
                    'avatar_url' => 'https://images.example.test/avatar.png',
                ],
            ]),
        ]);

        $response = $this->postJson('/auth/supabase/session', [
            'access_token' => 'verified-access-token',
            'refresh_token' => 'verified-refresh-token',
            'expires_in' => 3600,
            'email' => 'attacker@example.test',
            'id' => '00000000-0000-0000-0000-000000000000',
        ]);

        $response->assertOk()->assertJsonPath('redirect', '/manage');
        $this->assertAuthenticatedAs(User::query()->findOrFail($userId));
        $this->assertDatabaseHas('users', [
            'id' => $userId,
            'full_name' => 'Test Developer',
            'email' => 'developer@example.com',
            'avatar_url' => 'https://images.example.test/avatar.png',
        ]);
        $storedTokens = session('supabase.tokens');
        $this->assertIsString($storedTokens);
        $this->assertStringNotContainsString('verified-refresh-token', $storedTokens);
        $this->assertNotSame('', Crypt::decryptString($storedTokens));

        Http::assertSent(fn ($request): bool => $request->url() === 'https://project.example.test/auth/v1/user'
            && $request->hasHeader('apikey', 'public-test-key')
            && $request->hasHeader('Authorization', 'Bearer verified-access-token'));
    }

    public function test_an_invalid_supabase_access_token_cannot_create_a_laravel_session(): void
    {
        Http::fake([
            'https://project.example.test/auth/v1/user' => Http::response(['message' => 'invalid token'], 401),
        ]);

        $this->postJson('/auth/supabase/session', [
            'access_token' => 'forged-token',
            'refresh_token' => 'forged-refresh-token',
        ])->assertUnauthorized();

        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_missing_supabase_configuration_fails_closed(): void
    {
        config([
            'services.supabase.url' => '',
            'services.supabase.anon_key' => '',
        ]);

        $this->postJson('/auth/supabase/session', [
            'access_token' => 'some-token',
            'refresh_token' => 'some-refresh-token',
        ])->assertServiceUnavailable();

        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_portfolio_management_stays_protected_until_the_session_is_established(): void
    {
        $this->get('/portfolio/create')->assertRedirect('/login');
        $this->get('/manage')->assertRedirect('/login');
    }

    public function test_sign_out_clears_the_laravel_session_and_revokes_the_supabase_session(): void
    {
        $user = User::factory()->create();
        Http::fake([
            'https://project.example.test/auth/v1/logout*' => Http::response([], 204),
        ]);
        $encryptedTokens = Crypt::encryptString(json_encode([
            'access_token' => 'session-access-token',
            'refresh_token' => 'session-refresh-token',
            'expires_at' => now()->addHour()->timestamp,
        ], JSON_THROW_ON_ERROR));

        $this->actingAs($user)
            ->withSession(['supabase.tokens' => $encryptedTokens])
            ->post('/logout')
            ->assertRedirect('/');

        $this->assertGuest();
        Http::assertSent(fn ($request): bool => str_contains($request->url(), '/auth/v1/logout?scope=local')
            && $request->hasHeader('Authorization', 'Bearer session-access-token'));
    }

    public function test_user_profile_migration_preserves_existing_supabase_profiles(): void
    {
        $userId = 'a4d7c66f-f343-4c5b-9bb2-e95448d6c3bb';
        DB::table('users')->insert([
            'id' => $userId,
            'full_name' => 'Existing profile',
            'email' => 'existing@example.test',
            'avatar_url' => null,
            'created_at' => now(),
        ]);

        $migration = require database_path('migrations/0001_01_01_000000_create_users_table.php');
        $migration->up();

        $this->assertTrue(Schema::hasColumn('users', 'full_name'));
        $this->assertFalse(Schema::hasColumn('users', 'password'));
        $this->assertDatabaseHas('users', [
            'id' => $userId,
            'full_name' => 'Existing profile',
        ]);

        $migration->down();
        $this->assertDatabaseHas('users', ['id' => $userId]);
    }
}
