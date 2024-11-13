<?php

namespace Hemend\Api\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * usage: AsArrayString::class | AsArrayString::class.':true'
 */
class AsArrayString implements CastsAttributes
{
    protected bool $is_numeric = false;

    public function __construct(bool $is_numeric = false)
    {
        $this->is_numeric = $is_numeric;
    }

    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return array
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $data = array_filter(explode(',', $value));
        return $this->is_numeric ? array_map('intval', $data) : $data;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  array  $value
     * @param  array  $attributes
     * @return string
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return !empty($value) && is_array($value) ? trim(implode(',', $value)) : $value;
    }
}
