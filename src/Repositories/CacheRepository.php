<?php

namespace Yossivic\Otp\Repositories;

use Yossivic\Otp\Contracts\OtpRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class CacheRepository implements OtpRepositoryInterface
{
    /**
     * Store OTP record in cache
     */
    public function store(string $key, string $hash, int $expiry): void
    {
        $data = [
            'otp_hash' => $hash,
            'attempts' => 0,
            'expires_at' => Carbon::now()->addMinutes($expiry),
        ];

        Cache::put($key, $data, Carbon::now()->addMinutes($expiry));
    }

    /**
     * Get OTP hash only (optional, for old validate)
     */
    public function get(string $key): ?string
    {
        $record = Cache::get($key);
        return $record['otp_hash'] ?? null;
    }

    /**
     * Get the full OTP record (hash, attempts, expiry)
     */
    public function getRecord(string $key): ?array
    {
        return Cache::get($key);
    }

    /**
     * Delete OTP record
     */
    public function delete(string $key): void
    {
        Cache::forget($key);
    }

    /**
     * Increment validation attempts
     */
    public function incrementAttempts(string $key): void
    {
        $data = Cache::get($key);
        if ($data) {
            $data['attempts']++;
            // Update cache with same expiry
            $expiresAt = $data['expires_at'] ?? Carbon::now()->addMinutes(5);
            Cache::put($key, $data, Carbon::parse($expiresAt));
        }
    }

    /**
     * Get current number of attempts
     */
    public function getAttempts(string $key): int
    {
        $record = Cache::get($key);
        return $record['attempts'] ?? 0;
    }
}
