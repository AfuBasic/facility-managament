<?php

use App\Models\SocialAccount;
use App\Models\User;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;

beforeEach(function () {
    $this->mockSocialiteUser = Mockery::mock(SocialiteUser::class);
    $this->mockSocialiteUser->token = 'test-token';
    $this->mockSocialiteUser->refreshToken = 'test-refresh-token';
    $this->mockSocialiteUser->shouldReceive('getId')->andReturn('google-123');
    $this->mockSocialiteUser->shouldReceive('getEmail')->andReturn('john@example.com');
    $this->mockSocialiteUser->shouldReceive('getName')->andReturn('John Doe');
    $this->mockSocialiteUser->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');
});

it('redirects to google oauth', function () {
    $response = $this->get(route('social.redirect', 'google'));

    $response->assertRedirect();
    expect($response->headers->get('Location'))->toContain('accounts.google.com');
});

it('returns 404 for unsupported provider', function () {
    $response = $this->get(route('social.redirect', 'unsupported'));

    $response->assertNotFound();
});

it('creates new user on first social login', function () {
    Socialite::shouldReceive('driver')
        ->with('google')
        ->andReturn(Mockery::mock()->shouldReceive('user')->andReturn($this->mockSocialiteUser)->getMock());

    $response = $this->get(route('social.callback', 'google'));

    $response->assertRedirect(route('user.home'));

    $this->assertDatabaseHas('users', [
        'email' => 'john@example.com',
        'name' => 'John Doe',
    ]);

    $this->assertDatabaseHas('social_accounts', [
        'provider' => 'google',
        'provider_id' => 'google-123',
    ]);

    $user = User::where('email', 'john@example.com')->first();
    expect($user->email_verified_at)->not->toBeNull();
    $this->assertAuthenticatedAs($user);
});

it('links google to existing user with same email', function () {
    $existingUser = User::factory()->create([
        'email' => 'john@example.com',
    ]);

    Socialite::shouldReceive('driver')
        ->with('google')
        ->andReturn(Mockery::mock()->shouldReceive('user')->andReturn($this->mockSocialiteUser)->getMock());

    $response = $this->get(route('social.callback', 'google'));

    $response->assertRedirect(route('user.home'));

    expect(User::count())->toBe(1);
    expect($existingUser->socialAccounts()->count())->toBe(1);
    expect($existingUser->socialAccounts()->first()->provider)->toBe('google');
    $this->assertAuthenticatedAs($existingUser);
});

it('logs in existing user with linked social account', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
    ]);

    SocialAccount::create([
        'user_id' => $user->id,
        'provider' => 'google',
        'provider_id' => 'google-123',
        'provider_token' => 'old-token',
    ]);

    Socialite::shouldReceive('driver')
        ->with('google')
        ->andReturn(Mockery::mock()->shouldReceive('user')->andReturn($this->mockSocialiteUser)->getMock());

    $response = $this->get(route('social.callback', 'google'));

    $response->assertRedirect(route('user.home'));

    expect(User::count())->toBe(1);
    expect(SocialAccount::count())->toBe(1);

    $socialAccount = SocialAccount::first();
    expect($socialAccount->provider_token)->toBe('test-token');
    $this->assertAuthenticatedAs($user);
});

it('returns 404 for unsupported provider on callback', function () {
    $response = $this->get(route('social.callback', 'unsupported'));

    $response->assertNotFound();
});
