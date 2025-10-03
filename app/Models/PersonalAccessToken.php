<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    /**
     * Determine if the token is expired.
     */
    public function isExpired(): bool
    {
        // Check if expires_at is set and is in the past
        if ($this->expires_at && $this->expires_at->isPast()) {
            return true;
        }

        // Check global expiration setting
        $expiration = config('sanctum.expiration');

        if ($expiration === null) {
            return false; // Never expires if expiration is null
        }

        // Check if token is older than the configured expiration time
        return $this->created_at->addMinutes($expiration)->isPast();
    }
}
