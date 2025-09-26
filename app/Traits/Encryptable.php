<?php

namespace App\Traits;

use App\Services\EncryptionService;
use Illuminate\Database\Eloquent\Model;

trait Encryptable
{
    /**
     * Get encrypted attributes
     */
    protected function getEncryptedAttributes(): array
    {
        return $this->encrypted ?? [];
    }

    /**
     * Encrypt attribute value
     */
    protected function encryptAttribute(string $value): string
    {
        return app(EncryptionService::class)->encrypt($value);
    }

    /**
     * Decrypt attribute value
     */
    protected function decryptAttribute(string $value): string
    {
        try {
            return app(EncryptionService::class)->decrypt($value);
        } catch (\Exception $e) {
            // If decryption fails, return the original value
            return $value;
        }
    }

    /**
     * Get attribute value
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if (in_array($key, $this->getEncryptedAttributes()) && is_string($value)) {
            return $this->decryptAttribute($value);
        }

        return $value;
    }

    /**
     * Set attribute value
     */
    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->getEncryptedAttributes()) && is_string($value)) {
            $value = $this->encryptAttribute($value);
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Get encrypted attributes for JSON serialization
     */
    protected function getArrayableAttributes()
    {
        $attributes = parent::getArrayableAttributes();

        // Decrypt encrypted attributes for JSON output
        foreach ($this->getEncryptedAttributes() as $key) {
            if (isset($attributes[$key]) && is_string($attributes[$key])) {
                $attributes[$key] = $this->decryptAttribute($attributes[$key]);
            }
        }

        return $attributes;
    }

    /**
     * Get model attributes for database storage
     */
    public function getAttributes()
    {
        $attributes = parent::getAttributes();

        // Encrypt attributes before saving to database
        foreach ($this->getEncryptedAttributes() as $key) {
            if (isset($attributes[$key]) && is_string($attributes[$key])) {
                $attributes[$key] = $this->encryptAttribute($attributes[$key]);
            }
        }

        return $attributes;
    }

    /**
     * Get original encrypted attributes
     */
    public function getOriginal($key = null, $default = null)
    {
        $original = parent::getOriginal($key, $default);

        if ($key === null) {
            // Decrypt all encrypted attributes in original array
            foreach ($this->getEncryptedAttributes() as $encryptedKey) {
                if (isset($original[$encryptedKey]) && is_string($original[$encryptedKey])) {
                    $original[$encryptedKey] = $this->decryptAttribute($original[$encryptedKey]);
                }
            }
        } elseif (in_array($key, $this->getEncryptedAttributes()) && is_string($original)) {
            $original = $this->decryptAttribute($original);
        }

        return $original;
    }

    /**
     * Get changes with decrypted values
     */
    public function getChanges()
    {
        $changes = parent::getChanges();

        // Decrypt encrypted attributes in changes
        foreach ($this->getEncryptedAttributes() as $key) {
            if (isset($changes[$key]) && is_string($changes[$key])) {
                $changes[$key] = $this->decryptAttribute($changes[$key]);
            }
        }

        return $changes;
    }

    /**
     * Get dirty attributes with decrypted values
     */
    public function getDirty()
    {
        $dirty = parent::getDirty();

        // Decrypt encrypted attributes in dirty array
        foreach ($this->getEncryptedAttributes() as $key) {
            if (isset($dirty[$key]) && is_string($dirty[$key])) {
                $dirty[$key] = $this->decryptAttribute($dirty[$key]);
            }
        }

        return $dirty;
    }
}
