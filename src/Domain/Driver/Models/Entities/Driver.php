<?php

declare(strict_types=1);

namespace Src\Domain\Driver\Models\Entities;

use Database\Factories\DriverFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Src\Domain\Driver\Enums\DriverStatus;

/**
 * @property int $id
 * @property string $name
 * @property DriverStatus $status
 * @property string|null $location
 */
class Driver extends Model
{
    /** @use HasFactory<DriverFactory> */
    use HasFactory;

    protected $table = 'drivers';

    protected $fillable = [
        'name',
        'status',
        'location',
    ];

    protected $casts = [
        'status' => DriverStatus::class,
    ];

    protected static function newFactory(): DriverFactory
    {
        return DriverFactory::new();
    }
}
