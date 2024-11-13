<?php

namespace Hemend\Api\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

class AsGeoString implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if($value instanceof Expression) {
            $value = $value->getValue(DB::getQueryGrammar());
            $value = str_replace(["(GeomFromText('POINT(", ")'))"], '', $value);
            $value = str_replace(' ', ',', trim($value));
        }

        $geo = array_filter(explode(',', $value));
        return count($geo) == 2 ? ['lng' => $geo[0], 'lat' => $geo[1]] : null;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return DB::raw("(GeomFromText('POINT(" . $value['lng'] . ' ' . $value['lat'] . ")'))");
    }
}
