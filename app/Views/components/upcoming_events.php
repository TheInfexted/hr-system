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
                
                // Sort events by proximity to current date (closest events first)
                const sortedEvents = [...data.events].sort((a, b) => {
                    const dateA = new Date(a.start_date);
                    const dateB = new Date(b.start_date);
                    return dateA - dateB;
                });
                
                sortedEvents.forEach(event => {
                    const eventItem = document.createElement('li');
                    eventItem.className = 'list-group-item px-0';
                    
                    const eventDate = new Date(event.start_date);
                    const formattedDate = eventDate.toLocaleDateString('en-US', { 
                        month: 'short', day: 'numeric', year: 'numeric' 
                    });
                    
                    // Display time if available
                    let timeInfo = '';
                    if (event.start_time) {
                        const startTime = new Date(`2000-01-01T${event.start_time}`).toLocaleTimeString([], {
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                        
                        if (event.end_time && event.start_date === event.end_date) {
                            const endTime = new Date(`2000-01-01T${event.end_time}`).toLocaleTimeString([], {
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                            timeInfo = `${startTime} - ${endTime}`;
                        } else {
                            timeInfo = `at ${startTime}`;
                        }
                    }
                    
                    // Get days until the event
                    const daysInfo = getDaysUntil(event.start_date);
                    
                    // Choose badge color based on how soon the event is
                    let badgeClass = 'bg-primary';
                    if (daysInfo === 'Today') {
                        badgeClass = 'bg-danger';
                    } else if (daysInfo === 'Tomorrow') {
                        badgeClass = 'bg-warning';
                    } else if (daysInfo.includes('In 2 days') || daysInfo.includes('In 3 days')) {
                        badgeClass = 'bg-info';
                    }
                    
                    // Create event item with more visual appeal
                    eventItem.innerHTML = `
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="me-3 text-center">
                                <div class="${badgeClass} text-white rounded px-2 py-1">
                                    <div class="small">${eventDate.toLocaleDateString('en-US', { month: 'short' })}</div>
                                    <div class="fw-bold">${eventDate.getDate()}</div>
                                </div>
                                <div class="small text-muted mt-1">${daysInfo}</div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    <a href="<?= base_url('events/view/') ?>${event.id}" class="text-decoration-none">
                                        ${event.title}
                                    </a>
                                </h6>
                                <div class="small text-muted mb-1">
                                    <i class="bi bi-clock me-1"></i> ${timeInfo || 'All day'}
                                </div>
                                <div class="small text-muted">
                                    <i class="bi bi-geo-alt me-1"></i> ${event.location || 'No location specified'}
                                </div>
                            </div>
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
        
        // Compare year, month, and day to properly identify "today"
        const isSameDay = eventDate.getFullYear() === currentDate.getFullYear() && 
                          eventDate.getMonth() === currentDate.getMonth() && 
                          eventDate.getDate() === currentDate.getDate();
                    
        // Compare for tomorrow
        const tomorrowDate = new Date(currentDate);
        tomorrowDate.setDate(currentDate.getDate() + 1);
        const isTomorrow = eventDate.getFullYear() === tomorrowDate.getFullYear() && 
                          eventDate.getMonth() === tomorrowDate.getMonth() && 
                          eventDate.getDate() === tomorrowDate.getDate();
        
        if (isSameDay) {
            return 'Today';
        } else if (isTomorrow) {
            return 'Tomorrow';
        } else if (eventDate > currentDate) {
            // Calculate the difference in days
            const diffTime = Math.ceil((eventDate - currentDate) / (1000 * 60 * 60 * 24));
            return `In ${diffTime} days`;
        } else {
            // For ongoing events that started in the past
            return 'Ongoing';
        }
    }
});
</script>