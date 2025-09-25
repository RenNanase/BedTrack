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
                            <option value="emergency" {{ old('role', $user->role) == 'emergency' ? 'selected' : '' }}>Emergency Department</option>
                            <option value="nurse-manager" {{ old('role', $user->role) == 'nurse-manager' ? 'selected' : '' }}>Nurse Manager</option>
                            <option value="superadmin" {{ old('role', $user->role) == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                        </select>
                        @error('role')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Assigned Wards</label>
                        <div class="ward-selection-container border border-gray-300 rounded-md p-2 bg-white">
                            <div class="flex items-center justify-between border-b border-gray-200 pb-2 mb-2">
                                <div class="text-sm text-gray-500 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Select all wards this user can access</span>
                                </div>
                                <button type="button" id="select-all-wards" class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 py-1 px-2 rounded transition-colors">
                                    Select All
                                </button>
                            </div>
                            
                            <div class="ward-cards grid grid-cols-2 sm:grid-cols-3 gap-2 max-h-[240px] overflow-y-auto p-1">
                                @foreach($wards as $ward)
                                    @php
                                        $selected = in_array($ward->id, old('ward_ids', $user->wards->pluck('id')->toArray()));
                                    @endphp
                                    <label for="ward_{{ $ward->id }}" class="ward-card border border-gray-200 rounded-md p-2 cursor-pointer hover:bg-gray-50 transition-colors flex items-start">
                                        <input type="checkbox" name="ward_ids[]" id="ward_{{ $ward->id }}" value="{{ $ward->id }}" 
                                            {{ $selected ? 'checked' : '' }}
                                            class="h-5 w-5 text-primary border-gray-300 rounded mt-0.5 mr-2 focus:ring-primary">
                                        <div>
                                            <div class="font-medium text-gray-700 text-sm">{{ $ward->ward_name }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        @error('ward_ids')
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
        
        // Ward selection functionality
        const selectAllBtn = document.getElementById('select-all-wards');
        const wardCheckboxes = document.querySelectorAll('input[name="ward_ids[]"]');
        
        if (selectAllBtn && wardCheckboxes.length > 0) {
            selectAllBtn.addEventListener('click', function() {
                const anyUnchecked = Array.from(wardCheckboxes).some(checkbox => !checkbox.checked);
                
                wardCheckboxes.forEach(checkbox => {
                    checkbox.checked = anyUnchecked;
                });
                
                // Update button text
                selectAllBtn.textContent = anyUnchecked ? 'Deselect All' : 'Select All';
            });
            
            // Initialize button text
            const allChecked = Array.from(wardCheckboxes).every(checkbox => checkbox.checked);
            selectAllBtn.textContent = allChecked ? 'Deselect All' : 'Select All';
        }
    });
</script>
@endpush
@endsection
