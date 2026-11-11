
@extends('layouts.app')

@section('title', 'Staff Management')

@section('content')

<main class="max-w-6xl mx-auto p-8">


        <h2 class="text-2xl font-bold mb-2">
            Data Staff Restaurant
        </h2>

        <p class="text-gray-500 mb-6">
            Manage restaurant staff information.
        </p>

<!-- Staff Navigation -->
<div class="flex gap-3 mb-6">

    <!-- Staff Data -->
    <a href="{{ route('staff.index') }}"
       class="px-5 py-3 bg-[#27AE60] text-white rounded-lg font-semibold">
        Staff Data
    </a>

    <!-- Staff Schedule -->
    <a href="{{ route('staff.schedule') }}"
       class="px-5 py-3 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-100">
        Staff Schedule
    </a>

</div>

        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <p class="text-gray-500">Total Staff</p>
            <h3 class="text-3xl font-bold text-[#27AE60]">
                {{ count($staff) }}
            </h3>
        </div>

<div class="flex justify-end mb-5">
    <button
        type="button"
        onclick="document.getElementById('addStaffModal').showModal()"
        class="bg-[#27AE60] hover:bg-[#229954] text-white px-6 py-3 rounded-full font-semibold cursor-pointer"
    >
        + Add Staff
    </button>
</div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-[#27AE60] text-white">
                    <tr>
                        <th class="p-4">Staff ID</th>
                        <th class="p-4">Name</th>
                        <th class="p-4">Position</th>
                        <th class="p-4">Contact</th>
                        <th class="p-4">Start Date</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($staff as $person)
                        <tr class="border-b border-gray-200">
                            <td class="p-4">{{ $person['id'] }}</td>
                            <td class="p-4">{{ $person['name'] }}</td>
                            <td class="p-4">{{ $person['position'] }}</td>
                            <td class="p-4">{{ $person['contact'] }}</td>
                            <td class="p-4">{{ $person['start_date'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </main>

<!-- Add Staff Modal -->
<dialog
    id="addStaffModal"
    class="w-full max-w-lg rounded-2xl p-0 shadow-xl backdrop:bg-black/50"
    style="font-family: 'Poppins', sans-serif;"
>
    <div class="p-6">

        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold">
                Add Data Staff
            </h2>

            <button
                type="button"
                onclick="document.getElementById('addStaffModal').close()"
                class="text-gray-500 cursor-pointer"
            >
                ✕
            </button>
        </div>

        <div class="space-y-4">

            <div>
                <label class="block text-sm font-medium mb-2">
                    Name Staff
                </label>
                <input type="text"
                    placeholder="Enter staff name"
                    class="w-full border border-gray-300 rounded-lg p-3">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">
                    Staff ID
                </label>
                <input type="text"
                    placeholder="Enter staff ID"
                    class="w-full border border-gray-300 rounded-lg p-3">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">
                    Contact
                </label>
                <input type="tel"
                    placeholder="Enter phone number"
                    class="w-full border border-gray-300 rounded-lg p-3">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">
                    Start Date
                </label>
                <input type="date"
                    class="w-full border border-gray-300 rounded-lg p-3">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">
                    Position
                </label>
                <input type="text"
                    placeholder="Enter position"
                    class="w-full border border-gray-300 rounded-lg p-3">
            </div>

        </div>

        <div class="flex justify-end gap-3 mt-8">
            <button
                type="button"
                onclick="document.getElementById('addStaffModal').close()"
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
            Preview UI: penyimpanan data belum diaktifkan.
        </p>

    </div>
</dialog>

@endsection
