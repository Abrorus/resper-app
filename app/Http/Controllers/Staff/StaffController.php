<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\StaffSchedule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    // READ - Menampilkan data Staff dari MySQL
    public function index()
    {
        $staff = Staff::orderBy('id')
            ->get()
            ->map(function ($person) {
                return [
                    'db_id' => $person->id,
                    'id' => $person->staff_id,
                    'name' => $person->name,
                    'position' => $person->position,
                    'contact' => $person->contact,
                    'start_date' => $person->start_date->format('Y-m-d'),
                ];
            })
            ->toArray();

        return view('staff.index', compact('staff'));
    }

    // CREATE - Menambahkan data Staff ke MySQL
    public function store(Request $request)
    {
        $validated = $request->validate([
            'staff_id' => 'required|string|max:20|unique:staff,staff_id',
            'name' => 'required|string|max:100',
            'position' => 'required|string|max:100',
            'contact' => 'required|string|max:30',
            'start_date' => 'required|date',
        ]);

        Staff::create($validated);

        return redirect()
            ->route('staff.index')
            ->with('success', 'Data staff berhasil ditambahkan.');
    }

    // UPDATE - Memperbarui data Staff di MySQL
    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'staff_id' => [
                'required',
                'string',
                'max:20',
                Rule::unique('staff', 'staff_id')->ignore($staff->id),
            ],
            'name' => 'required|string|max:100',
            'position' => 'required|string|max:100',
            'contact' => 'required|string|max:30',
            'start_date' => 'required|date',
        ]);

        $staff->update($validated);

        return redirect()
            ->route('staff.index')
            ->with('success', 'Data staff berhasil diperbarui.');
    }

    // DELETE - Menghapus data Staff dari MySQL
    public function destroy(Staff $staff)
    {
        $staff->delete();

        return redirect()
            ->route('staff.index')
            ->with('success', 'Data staff berhasil dihapus.');
    }

    // READ - Menampilkan jadwal Staff dari MySQL
    public function schedule()
    {
        // Mengambil daftar karyawan untuk formulir jadwal
        $staffMembers = Staff::orderBy('name')->get();

        // Mengambil jadwal beserta data karyawannya
        $staffSchedules = StaffSchedule::with('staff')
            ->orderBy('id')
            ->get()
            ->map(function ($schedule) {
                return [
                    'db_id' => $schedule->id,
                    'staff_db_id' => $schedule->staff_id,
                    'id' => $schedule->staff->staff_id,
                    'name' => $schedule->staff->name,
                    'position' => $schedule->staff->position,
                    'day' => $schedule->day,
                    'start_time' => substr($schedule->start_time, 0, 5),
                    'end_time' => substr($schedule->end_time, 0, 5),
                    'time' => substr($schedule->start_time, 0, 5)
                        . ' - ' .
                        substr($schedule->end_time, 0, 5),
                    'shift' => $schedule->shift,
                ];
            })
            ->toArray();

        return view('staff.schedule', compact(
            'staffSchedules',
            'staffMembers'
        ));
    }

    // CREATE - Menambahkan jadwal Staff ke MySQL
    public function storeSchedule(Request $request)
    {
        $validated = $request->validate([
            'staff_id' => 'required|integer|exists:staff,id',
            'day' => [
                'required',
                Rule::in([
                    'Monday', 'Tuesday', 'Wednesday',
                    'Thursday', 'Friday', 'Saturday', 'Sunday',
                ]),
            ],
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|different:start_time',
            'shift' => [
                'required',
                Rule::in(['Morning', 'Afternoon', 'Night']),
            ],
        ]);

        StaffSchedule::create($validated);

        return redirect()
            ->route('staff.schedule')
            ->with('success', 'Jadwal staff berhasil ditambahkan.');
    }
    
    // UPDATE - Memperbarui jadwal Staff di MySQL
    public function updateSchedule(Request $request, StaffSchedule $staffSchedule)
    {
        $validated = $request->validate([
            'staff_id' => 'required|integer|exists:staff,id',

            'day' => [
                'required',
                Rule::in([
                    'Monday',
                    'Tuesday',
                    'Wednesday',
                    'Thursday',
                    'Friday',
                    'Saturday',
                    'Sunday',
                ]),
            ],

            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|different:start_time',

            'shift' => [
                'required',
                Rule::in([
                    'Morning',
                    'Afternoon',
                    'Night',
                ]),
            ],
        ]);

        $staffSchedule->update($validated);

        return redirect()
            ->route('staff.schedule')
            ->with('success', 'Jadwal staff berhasil diperbarui.');
    }

    // DELETE - Menghapus jadwal Staff dari MySQL
    public function destroySchedule(StaffSchedule $staffSchedule)
    {
        $staffSchedule->delete();

        return redirect()
            ->route('staff.schedule')
            ->with('success', 'Jadwal staff berhasil dihapus.');
    }

}
