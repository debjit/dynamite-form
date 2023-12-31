<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    public function getRouteKeyName() {
        return 'url';
      }
    protected $casts = [
        'content' => 'array',
        'boolean'=>'is_public',
        'boolean'=>'is_member_only',
    ];

    protected $guarded = [];
}
