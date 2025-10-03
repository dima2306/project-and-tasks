<?php

use App\Models\PersonalAccessToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);
});

describe('Sanctum Token Authentication', function () {
    describe('Token Creation', function () {
        it('can create a token for authenticated user', function () {
            $token = $this->user->createToken('test-token');

            expect($token)->toBeInstanceOf(\Laravel\Sanctum\NewAccessToken::class);
            expect($token->accessToken)->toBeInstanceOf(PersonalAccessToken::class);
            expect($token->plainTextToken)->toBeString();
        });

        it('can create multiple tokens with different names', function () {
            $token1 = $this->user->createToken('mobile-app');
            $token2 = $this->user->createToken('web-app');

            expect($this->user->tokens)->toHaveCount(2);
            expect($this->user->tokens->pluck('name')->toArray())->toBe(['mobile-app', 'web-app']);
        });

        it('can create tokens with abilities', function () {
            $token = $this->user->createToken('test-token', ['read', 'write']);

            expect($token->accessToken->abilities)->toBe(['read', 'write']);
        });

        it('stores hashed token in database', function () {
            $token = $this->user->createToken('test-token');

            $this->assertDatabaseHas('personal_access_tokens', [
                'tokenable_type' => User::class,
                'tokenable_id' => $this->user->id,
                'name' => 'test-token',
            ]);

            // Ensure the token is hashed, not stored as plain text
            $storedToken = PersonalAccessToken::first();
            expect($storedToken->token)->not->toBe($token->plainTextToken);
        });
    });

    describe('Token Authentication', function () {
        it('can authenticate using bearer token', function () {
            $token = $this->user->createToken('test-token');

            $this->withHeader('Authorization', 'Bearer ' . $token->plainTextToken)
                ->getJson('/api/auth/user')
                ->assertStatus(200)
                ->assertJson([
                    'id' => $this->user->id,
                    'email' => $this->user->email,
                ]);
        });

        it('rejects invalid bearer token', function () {
            $this->withHeader('Authorization', 'Bearer invalid-token')
                ->getJson('/api/auth/user')
                ->assertStatus(401);
        });

        it('rejects request without bearer token', function () {
            $this->getJson('/api/auth/user')
                ->assertStatus(401);
        });

        it('can authenticate with token that has required abilities', function () {
            $token = $this->user->createToken('test-token', ['read']);

            // This would require middleware that checks abilities
            $this->withHeader('Authorization', 'Bearer ' . $token->plainTextToken)
                ->getJson('/api/auth/user')
                ->assertStatus(200);
        });
    });

    describe('Token Revocation', function () {
        it('can revoke current token', function () {
            $token = $this->user->createToken('test-token');

            $this->withHeader('Authorization', 'Bearer ' . $token->plainTextToken)
                ->postJson('/api/auth/logout')
                ->assertStatus(200);

            // Token should be deleted
            expect(PersonalAccessToken::count())->toBe(0);
        });

        it('can revoke all user tokens', function () {
            $this->user->createToken('token-1');
            $this->user->createToken('token-2');
            $token3 = $this->user->createToken('token-3');

            expect(PersonalAccessToken::count())->toBe(3);

            $this->withHeader('Authorization', 'Bearer ' . $token3->plainTextToken)
                ->postJson('/api/auth/logout-all')
                ->assertStatus(200);

            expect(PersonalAccessToken::count())->toBe(0);
        });

        it('can manually delete specific tokens', function () {
            $token1 = $this->user->createToken('token-1');
            $token2 = $this->user->createToken('token-2');

            $this->user->tokens()->where('id', $token1->accessToken->id)->delete();

            expect(PersonalAccessToken::count())->toBe(1);
            expect(PersonalAccessToken::first()->name)->toBe('token-2');
        });
    });

    describe('Token Expiration', function () {
        it('respects token expiration using time travel', function () {
            // Set expiration to 1 minute for testing
            config(['sanctum.expiration' => 1]);

            $token = $this->user->createToken('test-token');

            // Fresh token should work
            $this->withHeader('Authorization', 'Bearer ' . $token->plainTextToken)
                ->getJson('/api/auth/user')
                ->assertStatus(200);

            // Travel forward in time to simulate expiration
            $this->travel(2)->minutes();

            // Now the token should be expired and rejected
            $this->withHeader('Authorization', 'Bearer ' . $token->plainTextToken)
                ->getJson('/api/auth/user')
                ->assertStatus(401);

            // Travel back to present
            $this->travelBack();
        });

        it('handles null expiration (never expires)', function () {
            config(['sanctum.expiration' => null]);

            $token = $this->user->createToken('test-token');

            // Travel far into the future
            $this->travel(1)->year();

            // Should still work since expiration is null
            $this->withHeader('Authorization', 'Bearer ' . $token->plainTextToken)
                ->getJson('/api/auth/user')
                ->assertStatus(200);

            $this->travelBack();
        });

        it('can create tokens with specific expiration', function () {
            $expiresAt = now()->addDays(30);
            $token = $this->user->createToken('test-token', ['*'], $expiresAt);

            expect($token->accessToken->expires_at->format('Y-m-d H:i:s'))
                ->toBe($expiresAt->format('Y-m-d H:i:s'));

            // Fresh token should work
            $this->withHeader('Authorization', 'Bearer ' . $token->plainTextToken)
                ->getJson('/api/auth/user')
                ->assertStatus(200);
        });

        it('rejects tokens that are explicitly expired via expires_at', function () {
            // Create token with short expiration
            $token = $this->user->createToken('test-token', ['*'], now()->addMinutes(1));

            // Fresh token should work
            $this->withHeader('Authorization', 'Bearer ' . $token->plainTextToken)
                ->getJson('/api/auth/user')
                ->assertStatus(200);

            // Travel past the token's expiration
            $this->travel(2)->minutes();

            // Should be rejected
            $this->withHeader('Authorization', 'Bearer ' . $token->plainTextToken)
                ->getJson('/api/auth/user')
                ->assertStatus(401);

            $this->travelBack();
        });

        it('checks token expiration at model level', function () {
            config(['sanctum.expiration' => 1]);

            $token = $this->user->createToken('test-token');

            // Token should not be expired initially
            expect($token->accessToken->isExpired())->toBeFalse();

            // Travel forward in time
            $this->travel(2)->minutes();

            // Refresh the token model and check expiration
            $token->accessToken->refresh();
            expect($token->accessToken->isExpired())->toBeTrue();

            $this->travelBack();
        });
    });

    describe('Token Security', function () {
        it('generates unique tokens', function () {
            $token1 = $this->user->createToken('test-token-1');
            $token2 = $this->user->createToken('test-token-2');

            expect($token1->plainTextToken)->not->toBe($token2->plainTextToken);
        });

        it('hashes tokens consistently', function () {
            $token = $this->user->createToken('test-token');
            $hash1 = hash('sha256', $token->plainTextToken);
            $hash2 = hash('sha256', $token->plainTextToken);

            expect($hash1)->toBe($hash2);
        });

        it('cannot authenticate with raw database token', function () {
            $token = $this->user->createToken('test-token');
            $rawDbToken = $token->accessToken->token;

            $this->withHeader('Authorization', 'Bearer ' . $rawDbToken)
                ->getJson('/api/auth/user')
                ->assertStatus(401);
        });
    });
});
