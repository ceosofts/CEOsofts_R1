<?php

namespace App\Domain\Shared\Traits;

use Illuminate\Support\Str;

trait HasUuid
{
    /**
     * Boot the trait.
     *
     * @return void
     */
    protected static function bootHasUuid()
    {
        static::creating(function ($model) {
            if (!$model->uuid) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get a model instance by UUID.
     *
     * @param  string  $uuid
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public static function findByUuid($uuid)
    {
        return static::where('uuid', $uuid)->first();
    }

    /**
     * Get a model instance by UUID or fail.
     *
     * @param  string  $uuid
     * @return \Illuminate\Database\Eloquent\Model
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public static function findByUuidOrFail($uuid)
    {
        return static::where('uuid', $uuid)->firstOrFail();
    }
}
