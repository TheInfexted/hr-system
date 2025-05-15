<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0 fw-semibold">Today's Attendance</h5>
            </div>
            <div class="card-body">
                <?php if(empty($today_attendance)): ?>
                    <div class="alert alert-info rounded-3 mb-0">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="alert-heading">You haven't clocked in yet!</h5>
                                <p class="mb-2">Click the button below to record your attendance for today.</p>
                                <form action="<?= base_url('attendance/clock') ?>" method="post">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-success px-4">
                                        <i class="bi bi-box-arrow-in-right me-2"></i> Clock In
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-success fs-6 me-3 px-3 py-2">
                                    <?= $today_attendance['status'] ?>
                                </span>
                                <p class="mb-0"><strong>Status</strong></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-0"><strong>Clock In:</strong> 
                                <?= !empty($today_attendance['time_in']) ? date('h:i A', strtotime($today_attendance['time_in'])) : 'Not yet' ?>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-0"><strong>Clock Out:</strong> 
                                <?php if(!empty($today_attendance['time_out'])): ?>
                                    <?= date('h:i A', strtotime($today_attendance['time_out'])) ?>
                                <?php else: ?>
                                    <form action="<?= base_url('attendance/clock') ?>" method="post" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-danger px-3">
                                            <i class="bi bi-box-arrow-right me-2"></i> Clock Out
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    
                    <?php if(!empty($today_attendance['time_in']) && !empty($today_attendance['time_out'])): ?>
                        <div class="alert alert-success rounded-3 mt-4 mb-0">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                                <div>Your attendance is complete for today!</div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Upcoming Events Section -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold">Upcoming Events</h5>
                <a href="<?= base_url('events') ?>" class="btn btn-sm btn-outline-primary rounded-pill">View All</a>
            </div>
            <div class="card-body p-0">
                <div id="upcoming-events-container">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2">Loading events...</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 fw-semibold">Quick Links</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <a href="<?= base_url('attendance/employee') ?>" class="btn btn-outline-primary d-flex align-items-center px-4 py-3 w-100">
                            <i class="bi bi-calendar-check me-3 fs-4"></i>
                            <div class="text-start">
                                <strong>View My Attendance</strong>
                                <div class="small text-secondary">Access attendance history</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="<?= base_url('profile') ?>" class="btn btn-outline-info d-flex align-items-center px-4 py-3 w-100">
                            <i class="bi bi-person me-3 fs-4"></i>
                            <div class="text-start">
                                <strong>My Profile</strong>
                                <div class="small text-secondary">View your information</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="<?= base_url('payslips') ?>" class="btn btn-outline-success d-flex align-items-center px-4 py-3 w-100">
                            <i class="bi bi-file-earmark-text me-3 fs-4"></i>
                            <div class="text-start">
                                <strong>My Payslips</strong>
                                <div class="small text-secondary">View payment history</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="<?= base_url('events') ?>" class="btn btn-outline-warning d-flex align-items-center px-4 py-3 w-100">
                            <i class="bi bi-calendar-event me-3 fs-4"></i>
                            <div class="text-start">
                                <strong>Company Events</strong>
                                <div class="small text-secondary">View upcoming events</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0 fw-semibold">My Information</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="avatar-placeholder bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                        <?= strtoupper(substr($employee['first_name'], 0, 1) . substr($employee['last_name'], 0, 1)) ?>
                    </div>
                    <h5 class="mt-3 mb-1"><?= $employee['first_name'] . ' ' . $employee['last_name'] ?></h5>
                    <p class="text-muted mb-0"><?= $employee['position'] ?? 'Employee' ?></p>
                </div>

                <div class="list-group list-group-flush border-top pt-3">
                    <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                        <span class="text-muted">Employee ID</span>
                        <p class="form-control-static fw-medium">
                            <?php 
                            // Get company prefix
                            $companyModel = new \App\Models\CompanyModel();
                            $company = $companyModel->find($employee['company_id']);
                            $prefix = $company['prefix'] ?? '';
                            
                            if (!empty($prefix)) {
                                echo $prefix . '-' . str_pad($employee['id'], 4, '0', STR_PAD_LEFT);
                            } else {
                                echo str_pad($employee['id'], 4, '0', STR_PAD_LEFT);
                            }
                            ?>
                        </p>
                    </div>
                    <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                        <span class="text-muted">Department</span>
                        <span class="fw-medium"><?= $employee['department'] ?? 'Not specified' ?></span>
                    </div>
                    <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                        <span class="text-muted">Email</span>
                        <span class="fw-medium"><?= $employee['email'] ?></span>
                    </div>
                    <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                        <span class="text-muted">Phone</span>
                        <span class="fw-medium"><?= $employee['phone'] ?? 'Not specified' ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fetch upcoming events - keeping the original JavaScript fetch logic
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