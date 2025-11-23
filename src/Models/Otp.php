<?php

namespace Gangon\Otp\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Otp extends Model
{
    protected $table = 'otps';

    protected $fillable = [
        'identifier',
        'otp_hash',
        'attempts',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Check if OTP is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at instanceof Carbon &&
            now()->greaterThan($this->expires_at);
    }
}
