@extends('layouts.app')

@section('title', 'Staff Schedule')

@section('content')

<main class="max-w-6xl mx-auto p-8">

        <div class="mb-6">
            <h2 class="text-2xl font-bold mb-2">
                Staff Schedule
            </h2>

            <p class="text-gray-500">
                Manage restaurant staff working schedules.
            </p>
        </div>

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
                class="bg-[#27AE60] hover:bg-[#229954] text-white px-6 py-3 rounded-full font-semibold cursor-pointer"
            >
                + Add Schedule
            </button>

        </div>

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

                            </tr>
                        @empty
                            <tr>
                                <td colspan="5"
                                    class="text-center p-8 text-gray-500">
                                    No schedules available.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>

    </main>

    <!-- ADD SCHEDULE MODAL -->
    <dialog
        id="scheduleModal"
        class="m-auto w-full max-w-lg rounded-2xl p-0 shadow-xl backdrop:bg-black/50"
        style="font-family: 'Poppins', sans-serif;"
    >

        <div class="p-6">

            <div class="flex justify-between items-center mb-6">

                <h2 class="text-xl font-bold">
                    Add Jadwal Staff
                </h2>

                <button
                    type="button"
                    onclick="document.getElementById('scheduleModal').close()"
                    class="text-gray-500 cursor-pointer">
                    ✕
                </button>

            </div>

            <div class="space-y-4">

                <!-- STAFF -->
                <div>
                    <label class="block text-sm font-medium mb-2">
                        Staff Name
                    </label>

                    <select
                        class="w-full border border-gray-300 rounded-lg p-3"
                    >
                        <option value="">Select Staff</option>

                        @foreach($staffSchedules as $schedule)
                            <option value="{{ $schedule['id'] }}">
                                {{ $schedule['name'] }}
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
                        class="w-full border border-gray-300 rounded-lg p-3"
                    >
                        <option value="">Select Day</option>
                        <option>Monday</option>
                        <option>Tuesday</option>
                        <option>Wednesday</option>
                        <option>Thursday</option>
                        <option>Friday</option>
                        <option>Saturday</option>
                        <option>Sunday</option>
                    </select>
                </div>

                <!-- START TIME -->
                <div>
                    <label class="block text-sm font-medium mb-2">
                        Start Time
                    </label>

                    <input
                        type="time"
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
                        class="w-full border border-gray-300 rounded-lg p-3"
                    >
                </div>

                <!-- SHIFT -->
                <div>
                    <label class="block text-sm font-medium mb-2">
                        Shift
                    </label>

                    <select
                        class="w-full border border-gray-300 rounded-lg p-3"
                    >
                        <option value="">Select Shift</option>
                        <option>Morning</option>
                        <option>Afternoon</option>
                        <option>Night</option>
                    </select>
                </div>

            </div>

            <!-- BUTTONS -->
            <div class="flex justify-end gap-3 mt-8">

                <button
                    type="button"
                    onclick="document.getElementById('scheduleModal').close()"
                    class="px-5 py-2 border border-gray-300 rounded-full cursor-pointer"
                >
                    Cancel
                </button>

                <button
                    type="button"
                    disabled
                    class="px-6 py-2 bg-[#27AE60] text-white rounded-full opacity-50 cursor-not-allowed"
                >
                    Save
                </button>

            </div>

            <p class="text-xs text-gray-500 mt-4">
                Preview UI: schedule saving is not yet enabled.
            </p>

        </div>

        </dialog>

@endsection
