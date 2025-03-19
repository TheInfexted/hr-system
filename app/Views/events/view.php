<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Event Details</h4>
        <div>
            <?php if(session()->get('role_id') == 1 || session()->get('role_id') == 2): ?>
            <a href="<?= base_url('events/edit/' . $event['id']) ?>" class="btn btn-primary me-2">
                <i class="bi bi-pencil me-2"></i> Edit
            </a>
            <?php endif; ?>
            <a href="<?= base_url('events') ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i> Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2 class="mb-3"><?= $event['title'] ?></h2>
                
                <?php
                    $statusClass = 'secondary';
                    switch($event['status']) {
                        case 'active':
                            $statusClass = 'success';
                            break;
                        case 'cancelled':
                            $statusClass = 'danger';
                            break;
                        case 'completed':
                            $statusClass = 'secondary';
                            break;
                    }
                ?>
                <div class="mb-3">
                    <span class="badge bg-<?= $statusClass ?> fs-6"><?= ucfirst($event['status']) ?></span>
                </div>
                
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Description</h5>
                        <p class="card-text"><?= nl2br($event['description']) ?></p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Event Information</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <strong><i class="bi bi-calendar3 me-2"></i> Date:</strong>
                                <div class="mt-1">
                                    <?php if($event['start_date'] == $event['end_date']): ?>
                                        <?= date('F d, Y', strtotime($event['start_date'])) ?>
                                    <?php else: ?>
                                        <?= date('F d, Y', strtotime($event['start_date'])) ?> - <?= date('F d, Y', strtotime($event['end_date'])) ?>
                                    <?php endif; ?>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <strong><i class="bi bi-geo-alt me-2"></i> Location:</strong>
                                <div class="mt-1"><?= $event['location'] ?></div>
                            </li>
                            <li class="list-group-item">
                                <strong><i class="bi bi-building me-2"></i> Company:</strong>
                                <div class="mt-1"><?= $event['company_name'] ?></div>
                            </li>
                            <li class="list-group-item">
                                <strong><i class="bi bi-person me-2"></i> Created By:</strong>
                                <div class="mt-1"><?= $event['created_by_name'] ?></div>
                            </li>
                            <li class="list-group-item">
                                <strong><i class="bi bi-clock me-2"></i> Created At:</strong>
                                <div class="mt-1"><?= date('F d, Y h:i A', strtotime($event['created_at'])) ?></div>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Countdown for upcoming events -->
                <?php if ($event['status'] == 'active' && strtotime($event['start_date']) > time()): ?>
                <div class="card bg-light">
                    <div class="card-body">
                        <h5 class="card-title">Countdown</h5>
                        <div id="countdown" class="text-center">
                            <div class="row g-2">
                                <div class="col-3">
                                    <div class="bg-white rounded p-2">
                                        <div id="days" class="fs-4 fw-bold">--</div>
                                        <small>Days</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="bg-white rounded p-2">
                                        <div id="hours" class="fs-4 fw-bold">--</div>
                                        <small>Hours</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="bg-white rounded p-2">
                                        <div id="minutes" class="fs-4 fw-bold">--</div>
                                        <small>Minutes</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="bg-white rounded p-2">
                                        <div id="seconds" class="fs-4 fw-bold">--</div>
                                        <small>Seconds</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if ($event['status'] == 'active' && strtotime($event['start_date']) > time()): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Countdown timer
    const countDownDate = new Date("<?= $event['start_date'] ?>").getTime();
    
    // Update the countdown every 1 second
    const countdownTimer = setInterval(function() {
        // Get current date and time
        const now = new Date().getTime();
        
        // Find the distance between now and the countdown date
        const distance = countDownDate - now;
        
        // Time calculations for days, hours, minutes and seconds
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        // Display the result
        document.getElementById("days").innerHTML = days;
        document.getElementById("hours").innerHTML = hours;
        document.getElementById("minutes").innerHTML = minutes;
        document.getElementById("seconds").innerHTML = seconds;
        
        // If the countdown is finished, display message
        if (distance < 0) {
            clearInterval(countdownTimer);
            document.getElementById("countdown").innerHTML = "<div class='alert alert-info mb-0'>This event has started!</div>";
        }
    }, 1000);
});
</script>
<?php endif; ?>
<?= $this->endSection() ?>