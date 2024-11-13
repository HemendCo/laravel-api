<?php

namespace Hemend\Api\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class AsDecimal implements CastsAttributes
{
    protected int $decimals;

    public function __construct(int $decimals = 2)
    {
        $this->decimals = $decimals;
    }

    /**
     * Cast the attribute to a decimal with formatted output.
     *
     * @param  Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null) {
            return null;
        }

        // فرمت کردن عدد
        $formattedPrice = number_format((float) $value, $this->decimals, '.', '');

        // حذف صفرهای اضافی
        if (strpos($formattedPrice, '.') !== false) {
            $formattedPrice = rtrim($formattedPrice, '0'); // حذف صفرهای اضافی
            $formattedPrice = rtrim($formattedPrice, '.'); // حذف نقطه در انتها در صورت نیاز
        }

        return (float) $formattedPrice;
    }

    /**
     * Prepare the value for storage.
     *
     * @param  Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return (float) $value;
    }
}
