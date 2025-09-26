<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class EncryptionService
{
    /**
     * Encrypt sensitive data
     */
    public function encrypt(string $data): string
    {
        return Crypt::encryptString($data);
    }

    /**
     * Decrypt sensitive data
     */
    public function decrypt(string $encryptedData): string
    {
        return Crypt::decryptString($encryptedData);
    }

    /**
     * Hash password
     */
    public function hashPassword(string $password): string
    {
        return Hash::make($password);
    }

    /**
     * Verify password
     */
    public function verifyPassword(string $password, string $hashedPassword): bool
    {
        return Hash::check($password, $hashedPassword);
    }

    /**
     * Generate secure random token
     */
    public function generateSecureToken(int $length = 64): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Generate API key
     */
    public function generateApiKey(): string
    {
        return 'api_' . $this->generateSecureToken(32);
    }

    /**
     * Encrypt financial data
     */
    public function encryptFinancialData(array $data): string
    {
        $jsonData = json_encode($data);
        return $this->encrypt($jsonData);
    }

    /**
     * Decrypt financial data
     */
    public function decryptFinancialData(string $encryptedData): array
    {
        $jsonData = $this->decrypt($encryptedData);
        return json_decode($jsonData, true);
    }

    /**
     * Encrypt personal information
     */
    public function encryptPersonalInfo(array $data): string
    {
        $jsonData = json_encode($data);
        return $this->encrypt($jsonData);
    }

    /**
     * Decrypt personal information
     */
    public function decryptPersonalInfo(string $encryptedData): array
    {
        $jsonData = $this->decrypt($encryptedData);
        return json_decode($jsonData, true);
    }

    /**
     * Encrypt token
     */
    public function encryptToken(string $token): string
    {
        return $this->encrypt($token);
    }

    /**
     * Decrypt token
     */
    public function decryptToken(string $encryptedToken): string
    {
        return $this->decrypt($encryptedToken);
    }

    /**
     * Generate secure salt
     */
    public function generateSalt(): string
    {
        return $this->generateSecureToken(32);
    }

    /**
     * Hash with salt
     */
    public function hashWithSalt(string $data, string $salt): string
    {
        return hash('sha256', $data . $salt);
    }

    /**
     * Generate secure UUID
     */
    public function generateSecureUuid(): string
    {
        return \Illuminate\Support\Str::uuid()->toString();
    }

    /**
     * Encrypt file content
     */
    public function encryptFile(string $filePath): string
    {
        $content = file_get_contents($filePath);
        return $this->encrypt($content);
    }

    /**
     * Decrypt file content
     */
    public function decryptFile(string $encryptedContent, string $outputPath): bool
    {
        $content = $this->decrypt($encryptedContent);
        return file_put_contents($outputPath, $content) !== false;
    }

    /**
     * Generate secure backup key
     */
    public function generateBackupKey(): string
    {
        return 'backup_' . $this->generateSecureToken(48);
    }

    /**
     * Encrypt backup data
     */
    public function encryptBackupData(array $data): string
    {
        $jsonData = json_encode($data);
        return $this->encrypt($jsonData);
    }

    /**
     * Decrypt backup data
     */
    public function decryptBackupData(string $encryptedData): array
    {
        $jsonData = $this->decrypt($encryptedData);
        return json_decode($jsonData, true);
    }
}
