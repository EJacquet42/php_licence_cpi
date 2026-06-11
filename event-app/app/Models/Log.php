<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'type', 'facility', 'priority', 'message', 'questions_data', 'score', 'total'])]
class Log extends Model
{
    use HasFactory;
    protected function casts(): array
    {
        return [
            'questions_data' => 'array',
        ];
    }
}
