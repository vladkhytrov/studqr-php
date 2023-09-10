<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\QrCode
 *
 * @property int $id
 * @property string $qr_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|QrCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QrCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QrCode query()
 * @method static \Illuminate\Database\Eloquent\Builder|QrCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QrCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QrCode whereQrId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QrCode whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class QrCode extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'qr_id',
        'lecture_id',
    ];

}
