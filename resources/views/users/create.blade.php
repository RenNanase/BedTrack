@extends('layouts.app')

@section('content')
<div class="py-6 bg-secondary">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Create New User</h1>
                <p class="text-sm text-gray-500">Add a new user to the system</p>
            </div>
            <a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors">
                Back to Users
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-md p-8">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label for="name" class="block text-base font-medium text-gray-700 mb-2">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary py-3 px-4 text-base">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-base font-medium text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary py-3 px-4 text-base">
                            <button type="button" class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-600" onclick="togglePasswordVisibility('password')">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 eye-open" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 eye-closed hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-base font-medium text-gray-700 mb-2">Confirm Password</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary py-3 px-4 text-base">
                            <button type="button" class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-600" onclick="togglePasswordVisibility('password_confirmation')">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 eye-open" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 eye-closed hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="role" class="block text-base font-medium text-gray-700 mb-2">Role</label>
                        <select name="role" id="role" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary py-3 px-4 text-base">
                            <option value="">Select a role</option>
                            <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="emergency" {{ old('role') == 'emergency' ? 'selected' : '' }}>Emergency Department</option>
                            <option value="nurse-manager" {{ old('role') == 'nurse-manager' ? 'selected' : '' }}>Nurse Manager</option>
                            <option value="superadmin" {{ old('role') == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                        </select>
                        @error('role')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-base font-medium text-gray-700 mb-2">Assigned Wards</label>
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
                                    <label for="ward_{{ $ward->id }}" class="ward-card border border-gray-200 rounded-md p-2 cursor-pointer hover:bg-gray-50 transition-colors flex items-start">
                                        <input type="checkbox" name="ward_ids[]" id="ward_{{ $ward->id }}" value="{{ $ward->id }}" 
                                            {{ in_array($ward->id, old('ward_ids', [])) ? 'checked' : '' }}
                                            class="h-5 w-5 text-primary border-gray-300 rounded mt-0.5 mr-2 focus:ring-primary">
                                        <div>
                                            <div class="font-medium text-gray-700 text-sm">{{ $ward->ward_name }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        @error('ward_ids')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8">
                    <button type="submit" class="px-6 py-3 bg-primary text-white rounded-md hover:bg-primary/90 transition-colors text-base font-medium">
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Toggle Eye Icon for Password Visibility -->
<script>
    function togglePasswordVisibility(inputId) {
        const passwordInput = document.getElementById(inputId);
        const eyeOpen = passwordInput.parentElement.querySelector('.eye-open');
        const eyeClosed = passwordInput.parentElement.querySelector('.eye-closed');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeOpen.classList.add('hidden');
            eyeClosed.classList.remove('hidden');
        } else {
            passwordInput.type = 'password';
            eyeOpen.classList.remove('hidden');
            eyeClosed.classList.add('hidden');
        }
    }

    // Ward selection functionality
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllBtn = document.getElementById('select-all-wards');
        const wardCheckboxes = document.querySelectorAll('input[name="ward_ids[]"]');
        
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
    });
</script>
@endsection
