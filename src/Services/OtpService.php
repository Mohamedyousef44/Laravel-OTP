<?php

namespace Yossivic\Otp\Services;

use Yossivic\Otp\Contracts\OtpRepositoryInterface;
use Carbon\Carbon;

class OtpService
{
    public function __construct(private array $config) {}

    public function generate(string $key): string
    {
        $otp = $this->generateOtp();
        $hash = hash_hmac('sha256', $otp, $this->config['secret_key']);

        app(OtpRepositoryInterface::class)->store($key, $hash, $this->config['expires_in']);

        return $otp;
    }

    public function validate(string $key, string $input): array
    {
        $repo = app(OtpRepositoryInterface::class);

        // Get OTP record
        $record = $repo->getRecord($key);
        if (!$record) {
            return [
                'valid' => false,
                'message' => $this->config["messages"]['not_found'],
            ];
        }

        // Check expiry
        if (isset($record['expires_at']) && Carbon::now()->greaterThan($record['expires_at'])) {
            $repo->delete($key);
            return [
                'valid' => false,
                'message' => $this->config["messages"]['expired'],
            ];
        }

        // Check attempts
        if ($record['attempts'] > $this->config['max_attempts']) {
            return [
                'valid' => false,
                'message' => $this->config["messages"]['max_attempts'],
            ];
        }

        // Hash input and compare
        $hash = hash_hmac('sha256', $input, $this->config['secret_key']);
        if (hash_equals($record['otp_hash'], $hash)) {
            $repo->delete($key);
            return [
                'valid' => true,
                'message' => $this->config["messages"]['valid'],
            ];
        }

        // Increment attempts on failure
        $repo->incrementAttempts($key);

        return [
            'valid' => false,
            'message' => $this->config["messages"]['invalid'],
        ];
    }

    private function generateOtp(): string
    {
        $length = $this->config['length'];
        $type   = $this->config['type'];

        return match ($type) {
            'numeric' => str_pad((string) random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT),
            'alphanumeric' => $this->generateAlphaNumeric($length),
            default => throw new \InvalidArgumentException("Invalid OTP type: $type"),
        };
    }

    private function generateAlphaNumeric(int $length): string
    {
        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $otp = '';
        for ($i = 0; $i < $length; $i++) {
            $otp .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $otp;
    }
}
