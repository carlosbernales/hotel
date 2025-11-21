function advanceCheckIn(roomId) {
    // Show confirmation dialog
    Swal.fire({
        title: 'Advance Check-in',
        text: 'Do you want to proceed with advance check-in for this room?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, proceed',
        cancelButtonText: 'No, cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirect to advance check-in form
            window.location.href = 'index.php?advance_checkin&room_id=' + roomId;
        }
    });
} 