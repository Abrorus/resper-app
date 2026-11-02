<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    
    public function index()
    {
        $staff = [
            [
                'id' => 'STF001',
                'name' => 'Bimo',
                'position' => 'Chef',
                'contact' => '081234567890',
                'start_date' => '2026-10-01',
            ],
            [
                'id' => 'STF002',
                'name' => 'Andi',
                'position' => 'Cashier',
                'contact' => '081234567891',
                'start_date' => '2026-10-15',
            ],
        ];

        return view('staff.index', compact('staff'));
    }
    public function schedule()
    {
        // Data contoh sementara
        $staffSchedules = [
            [
                'id' => 'STF001',
                'name' => 'Bimo',
                'position' => 'Chef',
                'day' => 'Monday',
                'time' => '08:00 - 16:00',
            ],
            [
                'id' => 'STF002',
                'name' => 'Andi',
                'position' => 'Cashier',
                'day' => 'Tuesday',
                'time' => '09:00 - 17:00',
            ],
        ];

        return view('staff.schedule', compact('staffSchedules'));
    }


}