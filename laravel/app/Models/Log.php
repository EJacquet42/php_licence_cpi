<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** @property int|null $user_id */
#[Fillable(['user_id', 'type', 'facility', 'priority', 'message', 'questions_data', 'score', 'total'])]
class Log extends Model
{
    protected function casts(): array
    {
        return [
            'questions_data' => 'array',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
