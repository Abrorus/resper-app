<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Staff extends Model
{
    protected $table = 'staff';

    protected $fillable = [
        'staff_id',
        'name',
        'position',
        'contact',
        'start_date',
    ];

    protected $casts = [
        'start_date' => 'date',
    ];

    // Relasi satu Staff memiliki banyak jadwal kerja
    public function schedules(): HasMany
    {
        return $this->hasMany(StaffSchedule::class, 'staff_id');
    }
}
