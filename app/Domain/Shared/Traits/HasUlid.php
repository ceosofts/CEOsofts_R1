<?php

namespace App\Domain\Shared\Traits;

use Illuminate\Support\Str;

trait HasUlid
{
    /**
     * Boot the HasUlid trait for the model.
     */
    protected static function bootHasUlid(): void
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::ulid();
            }
        });
    }

    /**
     * Determine if the model uses auto-incrementing IDs.
     */
    public function getIncrementing(): bool
    {
        return false;
    }

    /**
     * Get the data type of the primary key.
     */
    public function getKeyType(): string
    {
        return 'string';
    }
}
