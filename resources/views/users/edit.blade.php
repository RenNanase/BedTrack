@extends('layouts.app')

@section('content')
<div class="py-6 bg-secondary">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Edit User</h1>
                <p class="text-sm text-gray-500">Update user information and ward assignment</p>
            </div>
            <a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors">
                Back to Users
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary py-3 px-4 text-base">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password (leave blank to keep current)</label>
                        <div class="relative">
                            <input type="password" name="password" id="password"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary py-3 px-4 text-base">
                            <button type="button" class="absolute inset-y-0 right-0 flex items-center mr-3 mt-1 text-gray-600 cursor-pointer" id="toggle-password">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" id="eye-open" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" id="eye-closed" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" />
                                    <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="password_confirmation_div" style="display: none;">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary py-3 px-4 text-base">
                            <button type="button" class="absolute inset-y-0 right-0 flex items-center mr-3 mt-1 text-gray-600 cursor-pointer" id="toggle-confirmation">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" id="eye-open-confirmation" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" id="eye-closed-confirmation" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" />
                                    <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                        <select name="role" id="role" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary py-3 px-4 text-base">
                            <option value="">Select a role</option>
                            <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="superadmin" {{ old('role', $user->role) == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                        </select>
                        @error('role')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="ward_id" class="block text-sm font-medium text-gray-700">Ward</label>
                        <select name="ward_id" id="ward_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary py-3 px-4 text-base">
                            <option value="">Select a ward</option>
                            @foreach($wards as $ward)
                                <option value="{{ $ward->id }}" {{ old('ward_id', $user->wards->contains($ward->id) ? $ward->id : '') == $ward->id ? 'selected' : '' }}>
                                    {{ $ward->ward_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('ward_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="w-full md:w-auto px-4 py-2 bg-primary text-white rounded-md hover:bg-primary/90 transition-colors">
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Show/hide password confirmation field based on password input
    document.addEventListener('DOMContentLoaded', function() {
        const passwordField = document.getElementById('password');
        const confirmationDiv = document.getElementById('password_confirmation_div');
        
        if (passwordField && confirmationDiv) {
            passwordField.addEventListener('input', function() {
                if (this.value.length > 0) {
                    confirmationDiv.style.display = 'block';
                } else {
                    confirmationDiv.style.display = 'none';
                }
            });
        }

        // Toggle password visibility
        const togglePassword = document.getElementById('toggle-password');
        const toggleConfirmation = document.getElementById('toggle-confirmation');
        
        if (togglePassword) {
            togglePassword.addEventListener('click', function() {
                const passwordInput = document.getElementById('password');
                const eyeOpen = document.getElementById('eye-open');
                const eyeClosed = document.getElementById('eye-closed');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    eyeOpen.classList.add('hidden');
                    eyeClosed.classList.remove('hidden');
                } else {
                    passwordInput.type = 'password';
                    eyeOpen.classList.remove('hidden');
                    eyeClosed.classList.add('hidden');
                }
            });
        }
        
        if (toggleConfirmation) {
            toggleConfirmation.addEventListener('click', function() {
                const confirmInput = document.getElementById('password_confirmation');
                const eyeOpenConfirmation = document.getElementById('eye-open-confirmation');
                const eyeClosedConfirmation = document.getElementById('eye-closed-confirmation');
                
                if (confirmInput.type === 'password') {
                    confirmInput.type = 'text';
                    eyeOpenConfirmation.classList.add('hidden');
                    eyeClosedConfirmation.classList.remove('hidden');
                } else {
                    confirmInput.type = 'password';
                    eyeOpenConfirmation.classList.remove('hidden');
                    eyeClosedConfirmation.classList.add('hidden');
                }
            });
        }
    });
</script>
@endpush
@endsection
