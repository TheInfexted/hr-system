<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Today's Attendance</h5>
            </div>
            <div class="card-body">
                <?php if(empty($today_attendance)): ?>
                    <div class="alert alert-info">
                        <h5 class="alert-heading">You haven't clocked in yet!</h5>
                        <p>Click the button below to record your attendance for today.</p>
                        <form action="<?= base_url('attendance/clock') ?>" method="post">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Clock In
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>Status:</strong> 
                                <span class="badge bg-success"><?= $today_attendance['status'] ?></span>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Clock In:</strong> 
                                <?= !empty($today_attendance['time_in']) ? date('h:i A', strtotime($today_attendance['time_in'])) : 'Not yet' ?>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Clock Out:</strong> 
                                <?php if(!empty($today_attendance['time_out'])): ?>
                                    <?= date('h:i A', strtotime($today_attendance['time_out'])) ?>
                                <?php else: ?>
                                    <form action="<?= base_url('attendance/clock') ?>" method="post" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i> Clock Out
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    
                    <?php if(!empty($today_attendance['time_in']) && !empty($today_attendance['time_out'])): ?>
                        <div class="alert alert-success mt-3">
                            <i class="bi bi-check-circle-fill me-2"></i> Your attendance is complete for today!
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Upcoming Events Section -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Upcoming Events</h5>
                <a href="<?= base_url('events') ?>" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div id="upcoming-events-container">
                    <div class="text-center py-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Links</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <a href="<?= base_url('attendance/employee') ?>" class="btn btn-primary w-100 py-3">
                            <i class="bi bi-calendar-check me-2"></i> View My Attendance
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="<?= base_url('profile') ?>" class="btn btn-info w-100 py-3 text-white">
                            <i class="bi bi-person me-2"></i> My Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">My Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> <?= $employee['first_name'] . ' ' . $employee['last_name'] ?></p>
                <p><strong>Employee ID:</strong> <?= str_pad($employee['id'], 5, '0', STR_PAD_LEFT) ?></p>
                <p><strong>Position:</strong> <?= $employee['position'] ?? 'Not specified' ?></p>
                <p><strong>Department:</strong> <?= $employee['department'] ?? 'Not specified' ?></p>
                <p><strong>Email:</strong> <?= $employee['email'] ?></p>
                <p><strong>Phone:</strong> <?= $employee['phone'] ?? 'Not specified' ?></p>
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
                // Sort events by proximity to current date (closest events first)
                const sortedEvents = [...data.events].sort((a, b) => {
                    const dateA = new Date(a.start_date);
                    const dateB = new Date(b.start_date);
                    return dateA - dateB;
                });
                
                // Create list of events
                const eventsList = document.createElement('ul');
                eventsList.className = 'list-group list-group-flush';
                
                // Add each event to the list
                sortedEvents.forEach(event => {
                    // Format date for display
                    const eventDate = new Date(event.start_date);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    
                    const eventDay = eventDate.getDate();
                    const eventMonth = eventDate.toLocaleString('default', { month: 'short' });
                    
                    // Calculate days until the event
                    let daysUntil = '';
                    
                    // Compare year, month, and day to properly identify "today"
                    const isSameDay = eventDate.getFullYear() === today.getFullYear() && 
                                     eventDate.getMonth() === today.getMonth() && 
                                     eventDate.getDate() === today.getDate();
                    
                    // Compare for tomorrow
                    const tomorrowDate = new Date(today);
                    tomorrowDate.setDate(today.getDate() + 1);
                    const isTomorrow = eventDate.getFullYear() === tomorrowDate.getFullYear() && 
                                      eventDate.getMonth() === tomorrowDate.getMonth() && 
                                      eventDate.getDate() === tomorrowDate.getDate();
                    
                    if (isSameDay) {
                        daysUntil = 'Today';
                    } else if (isTomorrow) {
                        daysUntil = 'Tomorrow';
                    } else if (eventDate > today) {
                        // Calculate the difference in days
                        const diffTime = Math.ceil((eventDate - today) / (1000 * 60 * 60 * 24));
                        daysUntil = `In ${diffTime} days`;
                    } else {
                        daysUntil = 'Ongoing';
                    }
                    
                    // Choose badge color based on how soon the event is
                    let badgeClass = 'bg-primary';
                    if (daysUntil === 'Today') {
                        badgeClass = 'bg-danger';
                    } else if (daysUntil === 'Tomorrow') {
                        badgeClass = 'bg-warning';
                    } else if (daysUntil.includes('In 2 days') || daysUntil.includes('In 3 days')) {
                        badgeClass = 'bg-info';
                    }
                    
                    // Add time info if available
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
                    
                    // Create event item
                    const eventItem = document.createElement('li');
                    eventItem.className = 'list-group-item';
                    
                    // Create event item content
                    eventItem.innerHTML = `
                        <div class="d-flex w-100 justify-content-between align-items-start">
                            <div class="me-3 text-center">
                                <div class="${badgeClass} text-white rounded px-2 py-1">
                                    <div class="small">${eventMonth}</div>
                                    <div class="fw-bold">${eventDay}</div>
                                </div>
                                <div class="small text-muted mt-1">${daysUntil}</div>
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
            } else {
                // No events found
                container.innerHTML = `
                    <div class="text-center text-muted py-4">
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
                <div class="alert alert-danger m-3">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Failed to load upcoming events. Please try refreshing the page.
                </div>
            `;
        });
});
</script>
<?= $this->endSection() ?>