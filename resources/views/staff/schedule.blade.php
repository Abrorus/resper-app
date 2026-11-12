@extends('layouts.app')

@section('title', 'Staff Schedule')

@section('content')

<div class="max-w-6xl mx-auto p-8">

    <!-- PAGE TITLE -->
    <h2 class="text-2xl font-bold mb-2">
        Staff Schedule
    </h2>

    <p class="text-gray-500 mb-6">
        Manage restaurant staff working schedules.
    </p>

    <!-- NAVIGATION -->
    <div class="flex gap-3 mb-6">

        <a href="{{ route('staff.index') }}"
           class="px-5 py-3 bg-white border border-gray-200 rounded-lg text-gray-600">
            Staff Data
        </a>

        <a href="{{ route('staff.schedule') }}"
           class="px-5 py-3 bg-[#27AE60] text-white rounded-lg font-semibold">
            Staff Schedule
        </a>

    </div>

    <!-- SUCCESS MESSAGE -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-300 text-green-800 p-4 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- TOTAL SCHEDULE -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">

        <p class="text-gray-500">
            Total Staff Schedules
        </p>

        <h3 class="text-3xl font-bold text-[#27AE60]">
            {{ count($staffSchedules) }}
        </h3>

    </div>

    <!-- ADD SCHEDULE BUTTON -->
    <div class="flex justify-end mb-5">

        <button
            type="button"
            onclick="document.getElementById('scheduleModal').showModal()"
            class="bg-[#27AE60] hover:bg-[#229954] text-white px-6 py-3 rounded-full font-semibold cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
            @disabled($staffMembers->isEmpty())
        >
            + Add Schedule
        </button>

    </div>

    @if($staffMembers->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded-lg mb-5">
            Belum ada data karyawan. Tambahkan Staff terlebih dahulu melalui halaman Staff Data.
        </div>
    @endif

    <!-- SCHEDULE TABLE -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full text-left">

                <thead class="bg-[#27AE60] text-white">
                    <tr>
                        <th class="p-4">Staff ID</th>
                        <th class="p-4">Name</th>
                        <th class="p-4">Position</th>
                        <th class="p-4">Day</th>
                        <th class="p-4">Working Time</th>
                        <th class="p-4">Shift</th>
                        <th class="p-4">Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($staffSchedules as $schedule)

                        <tr class="border-b border-gray-200">

                            <td class="p-4">
                                {{ $schedule['id'] }}
                            </td>

                            <td class="p-4">
                                {{ $schedule['name'] }}
                            </td>

                            <td class="p-4">
                                {{ $schedule['position'] }}
                            </td>

                            <td class="p-4">
                                {{ $schedule['day'] }}
                            </td>

                            <td class="p-4">
                                {{ $schedule['time'] }}
                            </td>

                            <td class="p-4">
                                {{ $schedule['shift'] }}
                            </td>

                            <!-- ACTION BUTTONS -->
                            <td class="p-4">

                                <div class="flex items-center gap-2">

                                    <!-- EDIT BUTTON -->
                                    <button
                                        type="button"
                                        onclick="document.getElementById('editScheduleModal{{ $schedule['db_id'] }}').showModal()"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium cursor-pointer"
                                    >
                                        Edit
                                    </button>

                                    <!-- DELETE FORM -->
                                    <form
                                        action="{{ route('staff.schedule.destroy', $schedule['db_id']) }}"
                                        method="POST"
                                        onsubmit="return confirm('Apakah kamu yakin ingin menghapus jadwal staff ini?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium cursor-pointer"
                                        >
                                            Delete
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7"
                                class="text-center p-8 text-gray-500">
                                No schedules available.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


<!-- ADD SCHEDULE MODAL -->
<dialog
    id="scheduleModal"
    class="m-auto w-full max-w-lg rounded-2xl p-0 shadow-xl backdrop:bg-black/50 max-h-[90vh] overflow-y-auto"
>

    <form
        action="{{ route('staff.schedule.store') }}"
        method="POST"
        class="p-6"
    >

        @csrf

        <!-- MODAL HEADER -->
        <div class="flex justify-between items-center mb-6">

            <h2 class="text-xl font-bold">
                Add Jadwal Staff
            </h2>

            <button
                type="button"
                onclick="document.getElementById('scheduleModal').close()"
                class="text-gray-500 cursor-pointer"
            >
                ✕
            </button>

        </div>

        <!-- CREATE VALIDATION ERRORS -->
        @if($errors->any() && !old('_editing_schedule'))

            <div class="bg-red-100 border border-red-300 text-red-700 p-4 rounded-lg mb-5">

                <p class="font-semibold mb-2">
                    Please check your input:
                </p>

                <ul class="list-disc pl-5 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>

        @endif

        <!-- CREATE FORM FIELDS -->
        <div class="space-y-4">

            <!-- STAFF NAME -->
            <div>

                <label class="block text-sm font-medium mb-2">
                    Staff Name
                </label>

                <select
                    name="staff_id"
                    required
                    class="w-full border border-gray-300 rounded-lg p-3"
                >
                    <option value="">Select Staff</option>

                    @foreach($staffMembers as $member)

                        <option
                            value="{{ $member->id }}"
                            @selected(!old('_editing_schedule') && old('staff_id') == $member->id)
                        >
                            {{ $member->name }} ({{ $member->staff_id }})
                        </option>

                    @endforeach

                </select>

            </div>

            <!-- DAY -->
            <div>

                <label class="block text-sm font-medium mb-2">
                    Day
                </label>

                <select
                    name="day"
                    required
                    class="w-full border border-gray-300 rounded-lg p-3"
                >
                    <option value="">Select Day</option>

                    @foreach([
                        'Monday',
                        'Tuesday',
                        'Wednesday',
                        'Thursday',
                        'Friday',
                        'Saturday',
                        'Sunday'
                    ] as $day)

                        <option
                            value="{{ $day }}"
                            @selected(!old('_editing_schedule') && old('day') == $day)
                        >
                            {{ $day }}
                        </option>

                    @endforeach

                </select>

            </div>

            <!-- START TIME -->
            <div>

                <label class="block text-sm font-medium mb-2">
                    Start Time
                </label>

                <input
                    type="time"
                    name="start_time"
                    value="{{ old('_editing_schedule') ? '' : old('start_time') }}"
                    required
                    class="w-full border border-gray-300 rounded-lg p-3"
                >

            </div>

            <!-- END TIME -->
            <div>

                <label class="block text-sm font-medium mb-2">
                    End Time
                </label>

                <input
                    type="time"
                    name="end_time"
                    value="{{ old('_editing_schedule') ? '' : old('end_time') }}"
                    required
                    class="w-full border border-gray-300 rounded-lg p-3"
                >

            </div>

            <!-- SHIFT -->
            <div>

                <label class="block text-sm font-medium mb-2">
                    Shift
                </label>

                <select
                    name="shift"
                    required
                    class="w-full border border-gray-300 rounded-lg p-3"
                >
                    <option value="">Select Shift</option>

                    @foreach([
                        'Morning',
                        'Afternoon',
                        'Night'
                    ] as $shift)

                        <option
                            value="{{ $shift }}"
                            @selected(!old('_editing_schedule') && old('shift') == $shift)
                        >
                            {{ $shift }}
                        </option>

                    @endforeach

                </select>

            </div>

        </div>

        <!-- ADD MODAL BUTTONS -->
        <div class="flex justify-end gap-3 mt-8">

            <button
                type="button"
                onclick="document.getElementById('scheduleModal').close()"
                class="px-5 py-2 border border-gray-300 rounded-full cursor-pointer"
            >
                Cancel
            </button>

            <button
                type="submit"
                class="px-6 py-2 bg-[#27AE60] hover:bg-[#229954] text-white rounded-full font-semibold cursor-pointer"
            >
                Save
            </button>

        </div>

    </form>

</dialog>


<!-- EDIT SCHEDULE MODALS -->
@foreach($staffSchedules as $schedule)

    @php
        $isEditing = (string) old('_editing_schedule') === (string) $schedule['db_id'];

        $selectedStaff = $isEditing
            ? old('staff_id', $schedule['staff_db_id'])
            : $schedule['staff_db_id'];

        $selectedDay = $isEditing
            ? old('day', $schedule['day'])
            : $schedule['day'];

        $selectedStart = $isEditing
            ? old('start_time', $schedule['start_time'])
            : $schedule['start_time'];

        $selectedEnd = $isEditing
            ? old('end_time', $schedule['end_time'])
            : $schedule['end_time'];

        $selectedShift = $isEditing
            ? old('shift', $schedule['shift'])
            : $schedule['shift'];
    @endphp

    <dialog
        id="editScheduleModal{{ $schedule['db_id'] }}"
        class="m-auto w-full max-w-lg rounded-2xl p-0 shadow-xl backdrop:bg-black/50 max-h-[90vh] overflow-y-auto"
    >

        <form
            action="{{ route('staff.schedule.update', $schedule['db_id']) }}"
            method="POST"
            class="p-6"
        >

            @csrf
            @method('PUT')

            <input
                type="hidden"
                name="_editing_schedule"
                value="{{ $schedule['db_id'] }}"
            >

            <!-- EDIT MODAL HEADER -->
            <div class="flex justify-between items-center mb-6">

                <h2 class="text-xl font-bold">
                    Edit Jadwal Staff
                </h2>

                <button
                    type="button"
                    onclick="document.getElementById('editScheduleModal{{ $schedule['db_id'] }}').close()"
                    class="text-gray-500 cursor-pointer"
                >
                    ✕
                </button>

            </div>

            <!-- EDIT VALIDATION ERRORS -->
            @if($errors->any() && $isEditing)

                <div class="bg-red-100 border border-red-300 text-red-700 p-4 rounded-lg mb-5">

                    <p class="font-semibold mb-2">
                        Please check your input:
                    </p>

                    <ul class="list-disc pl-5 text-sm">

                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach

                    </ul>

                </div>

            @endif

            <!-- EDIT FORM FIELDS -->
            <div class="space-y-4">

                <!-- STAFF NAME -->
                <div>

                    <label class="block text-sm font-medium mb-2">
                        Staff Name
                    </label>

                    <select
                        name="staff_id"
                        required
                        class="w-full border border-gray-300 rounded-lg p-3"
                    >
                        <option value="">Select Staff</option>

                        @foreach($staffMembers as $member)

                            <option
                                value="{{ $member->id }}"
                                @selected((string) $selectedStaff === (string) $member->id)
                            >
                                {{ $member->name }} ({{ $member->staff_id }})
                            </option>

                        @endforeach

                    </select>

                </div>

                <!-- DAY -->
                <div>

                    <label class="block text-sm font-medium mb-2">
                        Day
                    </label>

                    <select
                        name="day"
                        required
                        class="w-full border border-gray-300 rounded-lg p-3"
                    >
                        <option value="">Select Day</option>

                        @foreach([
                            'Monday',
                            'Tuesday',
                            'Wednesday',
                            'Thursday',
                            'Friday',
                            'Saturday',
                            'Sunday'
                        ] as $day)

                            <option
                                value="{{ $day }}"
                                @selected($selectedDay === $day)
                            >
                                {{ $day }}
                            </option>

                        @endforeach

                    </select>

                </div>

                <!-- START TIME -->
                <div>

                    <label class="block text-sm font-medium mb-2">
                        Start Time
                    </label>

                    <input
                        type="time"
                        name="start_time"
                        value="{{ $selectedStart }}"
                        required
                        class="w-full border border-gray-300 rounded-lg p-3"
                    >

                </div>

                <!-- END TIME -->
                <div>

                    <label class="block text-sm font-medium mb-2">
                        End Time
                    </label>

                    <input
                        type="time"
                        name="end_time"
                        value="{{ $selectedEnd }}"
                        required
                        class="w-full border border-gray-300 rounded-lg p-3"
                    >

                </div>

                <!-- SHIFT -->
                <div>

                    <label class="block text-sm font-medium mb-2">
                        Shift
                    </label>

                    <select
                        name="shift"
                        required
                        class="w-full border border-gray-300 rounded-lg p-3"
                    >
                        <option value="">Select Shift</option>

                        @foreach(['Morning', 'Afternoon', 'Night'] as $shift)

                            <option
                                value="{{ $shift }}"
                                @selected($selectedShift === $shift)
                            >
                                {{ $shift }}
                            </option>

                        @endforeach

                    </select>

                </div>

            </div>

            <!-- EDIT MODAL BUTTONS -->
            <div class="flex justify-end gap-3 mt-8">

                <button
                    type="button"
                    onclick="document.getElementById('editScheduleModal{{ $schedule['db_id'] }}').close()"
                    class="px-5 py-2 border border-gray-300 rounded-full cursor-pointer"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="px-6 py-2 bg-[#27AE60] hover:bg-[#229954] text-white rounded-full font-semibold cursor-pointer"
                >
                    Save Changes
                </button>

            </div>

        </form>

    </dialog>

@endforeach


<!-- REOPEN CORRECT MODAL IF VALIDATION FAILS -->
@if($errors->any())

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const editingScheduleId = @json(old('_editing_schedule'));

            const modal = editingScheduleId
                ? document.getElementById('editScheduleModal' + editingScheduleId)
                : document.getElementById('scheduleModal');

            if (modal) {
                modal.showModal();
            }

        });
    </script>

@endif

@endsection
