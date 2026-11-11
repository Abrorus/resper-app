<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'total', 'date', 'manager_name', 'position'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'total' => 'integer',
        ];
    }
}

