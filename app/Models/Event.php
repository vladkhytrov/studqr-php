<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\Event
 *
 * @property int         $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Event newModelQuery()
 * @method static Builder|Event newQuery()
 * @method static Builder|Event query()
 * @method static Builder|Event whereCreatedAt($value)
 * @method static Builder|Event whereId($value)
 * @method static Builder|Event whereUpdatedAt($value)
 * @mixin Eloquent
 * @property string      $name
 * @property string      $status
 * @property int         $owner_id
 * @method static Builder|Event whereName($value)
 * @method static Builder|Event whereOwnerId($value)
 * @method static Builder|Event whereStatus($value)
 */
class Event extends Model
{
    use HasFactory;

    /**
     * Get the owner of the event
     *
     * @return HasOne
     */
    public function owner(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
