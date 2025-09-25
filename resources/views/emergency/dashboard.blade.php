@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="py-6 bg-secondary">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Flash Message -->
        @if (session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Emergency Department Dashboard</h1>
                <p class="text-sm text-gray-500">Last updated: {{ $currentDateTime }}</p>
            </div>
            <div>
                <button id="refreshBtn" class="px-4 py-2 bg-[#92ADA4] text-white rounded-md hover:bg-[#DAA38F] transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Refresh
                </button>
            </div>
        </div>

        <!-- Ward Status Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
            @foreach($wards as $ward)
                <div class="bg-white rounded-lg shadow-md overflow-hidden ward-card">
                    <div class="p-4 border-b border-gray-200 bg-primary/5 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-900">{{ $ward->ward_name }}</h2>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $wardStats[$ward->id]['availableBeds'] > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $wardStats[$ward->id]['availableBeds'] > 0 ? 'Has Available Beds' : 'No Available Beds' }}
                        </span>
                    </div>
                    <div class="p-4">
                        <div class="flex justify-between mb-3">
                            <div class="text-sm font-medium text-gray-500">Total Beds</div>
                            <div class="text-sm font-bold text-gray-900">{{ $wardStats[$ward->id]['totalBeds'] }}</div>
                        </div>
                        <div class="flex justify-between mb-3">
                            <div class="text-sm font-medium text-gray-500">Available</div>
                            <div class="text-sm font-bold text-green-600">{{ $wardStats[$ward->id]['availableBeds'] }}</div>
                        </div>
                        <div class="flex justify-between mb-3">
                            <div class="text-sm font-medium text-gray-500">Occupied</div>
                            <div class="text-sm font-bold text-red-600">{{ $wardStats[$ward->id]['occupiedBeds'] }}</div>
                        </div>
                        <div class="flex justify-between mb-3">
                            <div class="text-sm font-medium text-gray-500">Booked</div>
                            <div class="text-sm font-bold text-yellow-600">{{ $wardStats[$ward->id]['bookedBeds'] }}</div>
                        </div>

                        <!-- Progress bar for bed occupancy -->
                        <div class="mt-4">
                            <div class="flex justify-between mb-1">
                                <span class="text-xs font-medium text-gray-500">Occupancy Rate</span>
                                <span class="text-xs font-medium text-gray-500">{{ $wardStats[$ward->id]['occupiedPercentage'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-red-500 h-2 rounded-full" style="width: {{ $wardStats[$ward->id]['occupiedPercentage'] }}%"></div>
                            </div>
                        </div>

                        <!-- View Details button -->
                        <button class="ward-details-btn mt-4 w-full py-2 bg-primary/10 text-primary rounded-md hover:bg-primary/20 transition-colors" data-ward-id="{{ $ward->id }}">
                            View Details
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Modern Ward Detail Modal -->
        <div id="wardDetailModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center hidden z-50 transition-opacity duration-300">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-7xl max-h-[90vh] overflow-hidden flex flex-col">
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-gradient-to-r from-[#92ADA4] to-[#DAA38F] text-white flex justify-between items-center sticky top-0 z-10">
                    <div class="flex items-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <h2 class="text-xl font-bold" id="modalWardName"></h2>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="px-3 py-1 bg-white bg-opacity-20 rounded-full text-sm" id="bedCountBadge">
                            <span id="totalBedsCount">0</span> beds
                        </div>
                        <button id="closeModal" class="text-white hover:text-gray-200 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Search and Filter Bar -->
                <div class="px-6 py-3 bg-gray-50 border-b border-gray-200 flex flex-wrap gap-3 items-center">
                    <div class="relative flex-grow max-w-md">
                        <input type="text" id="wardSearchInput" placeholder="Search beds..." class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#92ADA4]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-2.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    
                    <div class="flex space-x-2">
                        <button class="status-filter px-3 py-1.5 rounded-lg bg-white border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#92ADA4] active" data-status="all">All</button>
                        <button class="status-filter px-3 py-1.5 rounded-lg bg-white border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#92ADA4]" data-status="available">Available</button>
                        <button class="status-filter px-3 py-1.5 rounded-lg bg-white border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#92ADA4]" data-status="occupied">Occupied</button>
                        <button class="status-filter px-3 py-1.5 rounded-lg bg-white border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#92ADA4]" data-status="other">Other</button>
                    </div>
                </div>
                
                <!-- Modal Content -->
                <div class="overflow-auto flex-grow p-6" id="modalContentWrapper">
                    <div id="modalContent" class="space-y-8"></div>
                </div>
                
                <!-- Status legend -->
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-200 grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full bg-green-500"></div>
                        <span class="text-xs text-gray-600">Available</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <span class="text-xs text-gray-600">Occupied</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                        <span class="text-xs text-gray-600">Booked</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                        <span class="text-xs text-gray-600">Maintenance</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize
        initializeDashboard();
        
        // Refresh button
        document.getElementById('refreshBtn').addEventListener('click', function() {
            window.location.reload();
        });
        
        // Ward details button click event
        document.querySelectorAll('.ward-details-btn').forEach(button => {
            button.addEventListener('click', function() {
                const wardId = this.getAttribute('data-ward-id');
                const wardName = this.closest('.ward-card').querySelector('h2').textContent;
                showWardDetails(wardId, wardName);
            });
        });
        
        // Close ward detail modal
        const closeWardDetailModal = document.getElementById('closeModal');
        if (closeWardDetailModal) {
            closeWardDetailModal.addEventListener('click', function() {
                const wardDetailModal = document.getElementById('wardDetailModal');
                if (wardDetailModal) {
                    wardDetailModal.classList.add('hidden');
                }
            });
        }
        
        // Filter form
        const filterForm = document.getElementById('filterForm');
        if (filterForm) {
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                applyFilters();
            });
        }
        
        // Initialize dashboard
        function initializeDashboard() {
            console.log('Emergency dashboard initialized');
        }
        
        // Show ward details in modal
        function showWardDetails(wardId, wardName) {
            const modalWardName = document.getElementById('modalWardName');
            const modalContent = document.getElementById('modalContent');
            const wardDetailModal = document.getElementById('wardDetailModal');
            const totalBedsCount = document.getElementById('totalBedsCount');
            
            if (!modalWardName || !modalContent || !wardDetailModal || !totalBedsCount) {
                console.error('Ward detail modal elements not found');
                return;
            }
            
            modalWardName.textContent = wardName;
            modalContent.innerHTML = '<div class="flex justify-center items-center p-10"><div class="animate-spin rounded-full h-10 w-10 border-b-2 border-gray-900"></div></div>';
            wardDetailModal.classList.remove('hidden');
            
            // Fetch the ward details
            fetch(`/BedTrack/public/emergency/ward-details/${wardId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    modalContent.innerHTML = '';
                    if (data.rooms.length === 0) {
                        modalContent.innerHTML = '<div class="bg-white rounded-lg shadow-md p-8 text-center"><p class="text-gray-500">No rooms available in this ward.</p></div>';
                        totalBedsCount.textContent = "0";
                        return;
                    }
                    
                    // Count total beds
                    let bedCount = 0;
                    data.rooms.forEach(room => {
                        bedCount += room.beds.length;
                    });
                    totalBedsCount.textContent = bedCount.toString();
                    
                    // Create content for each room
                    data.rooms.forEach(room => {
                        const roomDiv = document.createElement('div');
                        roomDiv.className = 'bg-white rounded-lg shadow-md overflow-hidden mb-8';
                        
                        // Room header
                        const roomHeader = document.createElement('div');
                        roomHeader.className = 'bg-gray-50 border-b border-gray-200 px-4 py-3 flex justify-between items-center';
                        roomHeader.innerHTML = `
                            <h3 class="text-lg font-medium text-gray-800">${room.room_name}</h3>
                            <span class="bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded-full">
                                ${room.beds.length} beds
                            </span>
                        `;
                        roomDiv.appendChild(roomHeader);
                        
                        // Beds container
                        const bedsContainer = document.createElement('div');
                        bedsContainer.className = 'grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 p-4';
                        
                        // Add beds to the container
                        room.beds.forEach(bed => {
                            const bedCard = document.createElement('div');
                            bedCard.dataset.status = bed.status.toLowerCase();
                            bedCard.dataset.bedName = bed.bed_name;
                            bedCard.dataset.searchText = bed.patient ? `${bed.bed_name} ${bed.patient.patient_name} ${bed.patient.mrn}`.toLowerCase() : bed.bed_name.toLowerCase();
                            
                            // Determine status and styling
                            let statusColor, bgColor, borderColor, statusLabel;
                            
                            if (bed.status.toLowerCase() === 'available') {
                                statusColor = 'text-green-800';
                                bgColor = 'bg-green-50';
                                borderColor = 'border-green-200';
                                statusLabel = 'Available';
                            } else if (bed.status.toLowerCase() === 'occupied') {
                                statusColor = 'text-red-800';
                                bgColor = 'bg-red-50';
                                borderColor = 'border-red-200';
                                statusLabel = 'Occupied';
                            } else if (bed.status.toLowerCase() === 'booked') {
                                statusColor = 'text-blue-800';
                                bgColor = 'bg-blue-50';
                                borderColor = 'border-blue-200';
                                statusLabel = 'Booked';
                            } else if (bed.status.toLowerCase() === 'maintenance' || bed.status.toLowerCase() === 'housekeeping') {
                                statusColor = 'text-yellow-800';
                                bgColor = 'bg-yellow-50';
                                borderColor = 'border-yellow-200';
                                statusLabel = bed.status;
                            } else {
                                statusColor = 'text-purple-800';
                                bgColor = 'bg-purple-50';
                                borderColor = 'border-purple-200';
                                statusLabel = bed.status;
                            }
                            
                            bedCard.className = `border rounded-lg overflow-hidden shadow-sm transition-all hover:shadow-md ${borderColor} ${bgColor}`;
                            
                            // Create bed card content
                            bedCard.innerHTML = `
                                <div class="px-3 py-2 border-b ${borderColor} flex justify-between items-center">
                                    <div class="font-medium">Bed ${bed.bed_name}</div>
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium ${statusColor} bg-white">
                                        ${statusLabel}
                                    </span>
                                </div>
                                <div class="p-3">
                                    ${bed.status.toLowerCase() === 'occupied' && bed.patient ? `
                                        <div class="space-y-2">
                                            <div class="flex items-center justify-between">
                                                <div class="font-medium text-sm">${bed.patient.patient_name}</div>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                MRN: ${bed.patient.mrn}
                                            </div>
                                        </div>
                                    ` : `
                                        <p class="text-sm text-gray-500 italic">No patient</p>
                                    `}
                                </div>
                            `;
                            
                            bedsContainer.appendChild(bedCard);
                        });
                        
                        roomDiv.appendChild(bedsContainer);
                        modalContent.appendChild(roomDiv);
                    });
                    
                    // Initialize search and filter functionality
                    initializeSearchAndFilter();
                })
                .catch(error => {
                    console.error('Error fetching ward details:', error);
                    modalContent.innerHTML = '<div class="bg-white rounded-lg shadow-md p-8 text-center"><p class="text-red-500">Error loading ward details. Please try again.</p></div>';
                });
        }
        
        // Search and filter functionality
        function initializeSearchAndFilter() {
            const searchInput = document.getElementById('wardSearchInput');
            const statusFilters = document.querySelectorAll('.status-filter');
            const bedCards = document.querySelectorAll('#modalContent [data-status]');
            
            // Search functionality
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                filterBeds(searchTerm, getActiveFilter());
            });
            
            // Status filter functionality
            statusFilters.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons
                    statusFilters.forEach(btn => btn.classList.remove('active', 'bg-gray-100'));
                    
                    // Add active class to clicked button
                    this.classList.add('active', 'bg-gray-100');
                    
                    // Apply filtering
                    const searchTerm = searchInput.value.toLowerCase().trim();
                    filterBeds(searchTerm, this.getAttribute('data-status'));
                });
            });
            
            // Initial filter (show all)
            filterBeds('', 'all');
        }
        
        // Get the active filter
        function getActiveFilter() {
            const activeFilter = document.querySelector('.status-filter.active');
            return activeFilter ? activeFilter.getAttribute('data-status') : 'all';
        }
        
        // Filter beds based on search term and status
        function filterBeds(searchTerm, statusFilter) {
            const bedCards = document.querySelectorAll('#modalContent [data-status]');
            
            bedCards.forEach(card => {
                // Check if card matches search term
                const matchesSearch = searchTerm === '' || card.dataset.searchText.includes(searchTerm);
                
                // Check if card matches status filter
                let matchesFilter = false;
                if (statusFilter === 'all') {
                    matchesFilter = true;
                } else if (statusFilter === 'available') {
                    matchesFilter = card.dataset.status === 'available';
                } else if (statusFilter === 'occupied') {
                    matchesFilter = card.dataset.status === 'occupied';
                } else if (statusFilter === 'other') {
                    matchesFilter = card.dataset.status !== 'available' && card.dataset.status !== 'occupied';
                }
                
                // Show/hide card based on filters
                if (matchesSearch && matchesFilter) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
            
            // Show/hide room sections if they have no visible beds
            document.querySelectorAll('#modalContent .bg-white.rounded-lg').forEach(roomSection => {
                const visibleBeds = roomSection.querySelectorAll('[data-status]:not(.hidden)').length;
                if (visibleBeds === 0) {
                    roomSection.classList.add('hidden');
                } else {
                    roomSection.classList.remove('hidden');
                }
            });
            
            // Show message if no beds match filters
            const visibleBeds = document.querySelectorAll('#modalContent [data-status]:not(.hidden)').length;
            const noResultsMessage = document.getElementById('noResultsMessage');
            
            if (visibleBeds === 0) {
                if (!noResultsMessage) {
                    const message = document.createElement('div');
                    message.id = 'noResultsMessage';
                    message.className = 'bg-white rounded-lg shadow-md p-8 text-center';
                    message.innerHTML = '<p class="text-gray-500">No beds match your filters.</p>';
                    modalContent.appendChild(message);
                }
            } else if (noResultsMessage) {
                noResultsMessage.remove();
            }
        }
    });
</script>
@endpush
@endsection