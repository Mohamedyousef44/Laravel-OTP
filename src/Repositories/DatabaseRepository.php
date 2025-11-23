<?php

namespace Gangon\Otp\Repositories;

use Gangon\Otp\Contracts\OtpRepositoryInterface;
use Gangon\Otp\Models\Otp as Model;
use Carbon\Carbon;

class DatabaseRepository implements OtpRepositoryInterface
{
    /**
     * Store OTP record in cache
     */
    public function store(string $key, string $hash, int $expiry): void
    {
        Model::updateOrCreate(
            ['identifier' => $key],
            [
                'otp_hash' => $hash,
                'attempts' => 1,
                'expires_at' => Carbon::now()->addMinutes($expiry),
            ]
        );
    }

    /**
     * Get OTP hash only (optional, for old validate)
     */
    public function get(string $key): ?string
    {
        $otp = Model::where('identifier', $key)->first();
        return $otp ? $otp->toArray() : null;
    }

    /**
     * Get the full OTP record (hash, attempts, expiry)
     */
    public function getRecord(string $key): ?array
    {
        return Model::where('identifier', $key)->first();
    }

    /**
     * Delete OTP record
     */
    public function delete(string $key): void
    {
        Model::where('identifier', $key)->delete();
    }

    /**
     * Increment validation attempts
     */
    public function incrementAttempts(string $key): void
    {
        Model::where('identifier', $key)->increment('attempts');
    }

    /**
     * Get current number of attempts
     */
    public function getAttempts(string $key): int
    {
        return Model::where('identifier', $key)->value('attempts') ?? 1;
    }
}
