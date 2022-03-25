<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Presence
 *
 * @property int $id
 * @property int $event_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Presence newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Presence newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Presence query()
 * @method static \Illuminate\Database\Eloquent\Builder|Presence whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Presence whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Presence whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Presence whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Presence whereUserId($value)
 * @mixin \Eloquent
 */
class Presence extends Model
{
    use HasFactory;
}
