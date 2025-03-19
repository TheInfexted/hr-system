<?php
// Upcoming Events Widget
?>
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Upcoming Events</h5>
    </div>
    <div class="card-body" id="upcoming-events-container">
        <div class="text-center my-3">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fetch upcoming events
    fetch('<?= base_url('events/upcomingEvents') ?>')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('upcoming-events-container');
            
            // Clear loading spinner
            container.innerHTML = '';
            
            if (data.events && data.events.length > 0) {
                const eventsList = document.createElement('ul');
                eventsList.className = 'list-group list-group-flush';
                
                data.events.forEach(event => {
                    const eventItem = document.createElement('li');
                    eventItem.className = 'list-group-item px-0';
                    
                    const eventDate = new Date(event.start_date);
                    const formattedDate = eventDate.toLocaleDateString('en-US', { 
                        month: 'short', day: 'numeric', year: 'numeric' 
                    });
                    
                    eventItem.innerHTML = `
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">
                                    <a href="<?= base_url('events/view/') ?>${event.id}" class="text-decoration-none">
                                        ${event.title}
                                    </a>
                                </h6>
                                <div class="small text-muted">
                                    <i class="bi bi-calendar3 me-1"></i> ${formattedDate}
                                </div>
                                <div class="small text-muted">
                                    <i class="bi bi-geo-alt me-1"></i> ${event.location || 'No location specified'}
                                </div>
                            </div>
                            <span class="badge bg-primary rounded-pill">
                                ${getDaysUntil(event.start_date)}
                            </span>
                        </div>
                    `;
                    
                    eventsList.appendChild(eventItem);
                });
                
                container.appendChild(eventsList);
                
                // If there are more than the displayed events, add a "View All" link
                const viewAllLink = document.createElement('div');
                viewAllLink.className = 'text-center mt-3';
                viewAllLink.innerHTML = `
                    <a href="<?= base_url('events') ?>" class="btn btn-sm btn-outline-primary">
                        View All Events
                    </a>
                `;
                
                container.appendChild(viewAllLink);
            } else {
                // No events found
                container.innerHTML = `
                    <div class="text-center text-muted py-3">
                        <i class="bi bi-calendar-x fs-1 mb-3"></i>
                        <p>No upcoming events found.</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error fetching upcoming events:', error);
            
            const container = document.getElementById('upcoming-events-container');
            container.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Failed to load upcoming events. Please try again later.
                </div>
            `;
        });
        
    // Helper function to calculate days until an event
    function getDaysUntil(dateString) {
        const eventDate = new Date(dateString);
        const currentDate = new Date();
        
        // Reset time to compare dates only
        eventDate.setHours(0, 0, 0, 0);
        currentDate.setHours(0, 0, 0, 0);
        
        // Calculate difference in days
        const diffTime = eventDate - currentDate;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays === 0) {
            return 'Today';
        } else if (diffDays === 1) {
            return 'Tomorrow';
        } else {
            return `In ${diffDays} days`;
        }
    }
});
</script>