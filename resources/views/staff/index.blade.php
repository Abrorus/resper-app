@extends('layouts.app')

@section('title', 'Staff Management')

@section('content')

<div class="max-w-6xl mx-auto p-8">

    <!-- PAGE TITLE -->
    <h2 class="text-2xl font-bold mb-2">
        Data Staff Restaurant
    </h2>

    <p class="text-gray-500 mb-6">
        Manage restaurant staff information.
    </p>

    <!-- STAFF NAVIGATION -->
    <div class="flex gap-3 mb-6">

        <a href="{{ route('staff.index') }}"
           class="px-5 py-3 bg-[#27AE60] text-white rounded-lg font-semibold">
            Staff Data
        </a>

        <a href="{{ route('staff.schedule') }}"
           class="px-5 py-3 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-100">
            Staff Schedule
        </a>

    </div>

    <!-- SUCCESS MESSAGE -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-300 text-green-800 p-4 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- TOTAL STAFF -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">

        <p class="text-gray-500">
            Total Staff
        </p>

        <h3 class="text-3xl font-bold text-[#27AE60]">
            {{ count($staff) }}
        </h3>

    </div>

    <!-- ADD STAFF BUTTON -->
    <div class="flex justify-end mb-5">

        <button
            type="button"
            onclick="document.getElementById('addStaffModal').showModal()"
            class="bg-[#27AE60] hover:bg-[#229954] text-white px-6 py-3 rounded-full font-semibold cursor-pointer"
        >
            + Add Staff
        </button>

    </div>

    <!-- STAFF TABLE -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full text-left">

                <thead class="bg-[#27AE60] text-white">
                    <tr>
                        <th class="p-4">Staff ID</th>
                        <th class="p-4">Name</th>
                        <th class="p-4">Position</th>
                        <th class="p-4">Contact</th>
                        <th class="p-4">Start Date</th>
                        <th class="p-4">Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($staff as $person)

                        <tr class="border-b border-gray-200">

                            <td class="p-4">
                                {{ $person['id'] }}
                            </td>

                            <td class="p-4">
                                {{ $person['name'] }}
                            </td>

                            <td class="p-4">
                                {{ $person['position'] }}
                            </td>

                            <td class="p-4">
                                {{ $person['contact'] }}
                            </td>

                            <td class="p-4">
                                {{ $person['start_date'] }}
                            </td>

                            <!-- ACTION BUTTONS -->
                            <td class="p-4">

                                <div class="flex items-center gap-2">

                                    <!-- EDIT BUTTON -->
                                    <button
                                        type="button"
                                        onclick="document.getElementById('editStaffModal{{ $person['db_id'] }}').showModal()"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium cursor-pointer"
                                    >
                                        Edit
                                    </button>

                                    <!-- DELETE FORM -->
                                    <form
                                        action="{{ route('staff.destroy', $person['db_id']) }}"
                                        method="POST"
                                        onsubmit="return confirm('Apakah kamu yakin ingin menghapus data staff ini?')"
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
                            <td
                                colspan="6"
                                class="p-8 text-center text-gray-500"
                            >
                                No staff data available.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


<!-- ADD STAFF MODAL -->
<dialog
    id="addStaffModal"
    class="m-auto w-full max-w-lg rounded-2xl p-0 shadow-xl backdrop:bg-black/50"
>

    <form
        action="{{ route('staff.store') }}"
        method="POST"
        class="p-6"
    >

        @csrf

        <!-- MODAL HEADER -->
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

        <!-- ADD VALIDATION ERRORS -->
        @if($errors->any() && !old('_editing_staff'))

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

        <!-- ADD FORM FIELDS -->
        <div class="space-y-4">

            <!-- NAME -->
            <div>
                <label class="block text-sm font-medium mb-2">
                    Name Staff
                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('_editing_staff') ? '' : old('name') }}"
                    placeholder="Enter staff name"
                    maxlength="100"
                    required
                    class="w-full border border-gray-300 rounded-lg p-3"
                >
            </div>

            <!-- STAFF ID -->
            <div>
                <label class="block text-sm font-medium mb-2">
                    Staff ID
                </label>

                <input
                    type="text"
                    name="staff_id"
                    value="{{ old('_editing_staff') ? '' : old('staff_id') }}"
                    placeholder="Enter staff ID"
                    maxlength="20"
                    required
                    class="w-full border border-gray-300 rounded-lg p-3"
                >
            </div>

            <!-- CONTACT -->
            <div>
                <label class="block text-sm font-medium mb-2">
                    Contact
                </label>

                <input
                    type="tel"
                    name="contact"
                    value="{{ old('_editing_staff') ? '' : old('contact') }}"
                    placeholder="Enter phone number"
                    maxlength="30"
                    required
                    class="w-full border border-gray-300 rounded-lg p-3"
                >
            </div>

            <!-- START DATE -->
            <div>
                <label class="block text-sm font-medium mb-2">
                    Start Date
                </label>

                <input
                    type="date"
                    name="start_date"
                    value="{{ old('_editing_staff') ? '' : old('start_date') }}"
                    required
                    class="w-full border border-gray-300 rounded-lg p-3"
                >
            </div>

            <!-- POSITION -->
            <div>
                <label class="block text-sm font-medium mb-2">
                    Position
                </label>

                <input
                    type="text"
                    name="position"
                    value="{{ old('_editing_staff') ? '' : old('position') }}"
                    placeholder="Enter position"
                    maxlength="100"
                    required
                    class="w-full border border-gray-300 rounded-lg p-3"
                >
            </div>

        </div>

        <!-- ADD MODAL BUTTONS -->
        <div class="flex justify-end gap-3 mt-8">

            <button
                type="button"
                onclick="document.getElementById('addStaffModal').close()"
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


<!-- EDIT STAFF MODALS -->
@foreach($staff as $person)

    <dialog
        id="editStaffModal{{ $person['db_id'] }}"
        class="m-auto w-full max-w-lg rounded-2xl p-0 shadow-xl backdrop:bg-black/50"
    >

        <form
            action="{{ route('staff.update', $person['db_id']) }}"
            method="POST"
            class="p-6"
        >

            @csrf
            @method('PUT')

            <input
                type="hidden"
                name="_editing_staff"
                value="{{ $person['db_id'] }}"
            >

            <!-- EDIT MODAL HEADER -->
            <div class="flex justify-between items-center mb-6">

                <h2 class="text-xl font-bold">
                    Edit Data Staff
                </h2>

                <button
                    type="button"
                    onclick="document.getElementById('editStaffModal{{ $person['db_id'] }}').close()"
                    class="text-gray-500 cursor-pointer"
                >
                    ✕
                </button>

            </div>

            <!-- EDIT VALIDATION ERRORS -->
            @if($errors->any() && (string) old('_editing_staff') === (string) $person['db_id'])

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

                <!-- NAME -->
                <div>
                    <label class="block text-sm font-medium mb-2">
                        Name Staff
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ (string) old('_editing_staff') === (string) $person['db_id'] ? old('name', $person['name']) : $person['name'] }}"
                        maxlength="100"
                        required
                        class="w-full border border-gray-300 rounded-lg p-3"
                    >
                </div>

                <!-- STAFF ID -->
                <div>
                    <label class="block text-sm font-medium mb-2">
                        Staff ID
                    </label>

                    <input
                        type="text"
                        name="staff_id"
                        value="{{ (string) old('_editing_staff') === (string) $person['db_id'] ? old('staff_id', $person['id']) : $person['id'] }}"
                        maxlength="20"
                        required
                        class="w-full border border-gray-300 rounded-lg p-3"
                    >
                </div>

                <!-- CONTACT -->
                <div>
                    <label class="block text-sm font-medium mb-2">
                        Contact
                    </label>

                    <input
                        type="tel"
                        name="contact"
                        value="{{ (string) old('_editing_staff') === (string) $person['db_id'] ? old('contact', $person['contact']) : $person['contact'] }}"
                        maxlength="30"
                        required
                        class="w-full border border-gray-300 rounded-lg p-3"
                    >
                </div>

                <!-- START DATE -->
                <div>
                    <label class="block text-sm font-medium mb-2">
                        Start Date
                    </label>

                    <input
                        type="date"
                        name="start_date"
                        value="{{ (string) old('_editing_staff') === (string) $person['db_id'] ? old('start_date', $person['start_date']) : $person['start_date'] }}"
                        required
                        class="w-full border border-gray-300 rounded-lg p-3"
                    >
                </div>

                <!-- POSITION -->
                <div>
                    <label class="block text-sm font-medium mb-2">
                        Position
                    </label>

                    <input
                        type="text"
                        name="position"
                        value="{{ (string) old('_editing_staff') === (string) $person['db_id'] ? old('position', $person['position']) : $person['position'] }}"
                        maxlength="100"
                        required
                        class="w-full border border-gray-300 rounded-lg p-3"
                    >
                </div>

            </div>

            <!-- EDIT MODAL BUTTONS -->
            <div class="flex justify-end gap-3 mt-8">

                <button
                    type="button"
                    onclick="document.getElementById('editStaffModal{{ $person['db_id'] }}').close()"
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

            const editingStaffId = @json(old('_editing_staff'));

            const modal = editingStaffId
                ? document.getElementById('editStaffModal' + editingStaffId)
                : document.getElementById('addStaffModal');

            if (modal) {
                modal.showModal();
            }

        });
    </script>

@endif

@endsection
