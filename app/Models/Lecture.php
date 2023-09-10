<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\Lecture
 *
 * @property int         $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Lecture newModelQuery()
 * @method static Builder|Lecture newQuery()
 * @method static Builder|Lecture query()
 * @method static Builder|Lecture whereCreatedAt($value)
 * @method static Builder|Lecture whereId($value)
 * @method static Builder|Lecture whereUpdatedAt($value)
 * @mixin Eloquent
 * @property string      $name
 * @property string      $status
 * @property int         $teacher_id
 * @method static Builder|Lecture whereName($value)
 * @method static Builder|Lecture whereTeacherId($value)
 * @method static Builder|Lecture whereStatus($value)
 * @property-read \App\Models\User|null $teacher
 * @method static \Database\Factories\LectureFactory factory(...$parameters)
 */
class Lecture extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'teacher_id',
        'status',
    ];

    /**
     * Get the teacher of the lecture
     *
     * @return HasOne
     */
    public function teacher(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
