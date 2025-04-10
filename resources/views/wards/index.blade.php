@section('scripts')
<script>
    $(document).ready(function() {
        // Handle add bed form submission
        $('.add-bed-form').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const roomId = form.data('room-id');
            const bedNumber = form.find('input[name="bed_number"]').val();
            const bedType = form.find('select[name="bed_type"]').val();

            $.ajax({
                url: '{{ route("add-bed") }}',
                method: 'POST',
                data: {
                    room_id: roomId,
                    bed_number: bedNumber,
                    bed_type: bedType,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        // Add new bed card to the room
                        const bedCard = `
                            <div class="col-md-3 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">${response.bed.bed_number}</h5>
                                        <p class="card-text">
                                            <span class="badge bg-success">Available</span>
                                            <br>
                                            Type: ${response.bed.bed_type}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        `;
                        $(`#room-${roomId} .beds-grid`).append(bedCard);
                        
                        // Reset form
                        form.find('input[name="bed_number"]').val('');
                        form.find('select[name="bed_type"]').val('Regular');
                        
                        // Show success message
                        alert('Bed added successfully');
                    }
                },
                error: function(xhr) {
                    alert('Failed to add bed: ' + xhr.responseJSON.message);
                }
            });
        });
    });
</script>
@endsection 