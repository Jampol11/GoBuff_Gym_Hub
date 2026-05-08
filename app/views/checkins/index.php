<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div>
            <h2 class="page-title">Check-Ins</h2>
            <p class="page-subtitle">Member attendance tracking</p>
        </div>
        <a href="<?= base_url('/checkins/stats') ?>" class="btn btn-outline-info">
            <i class="bi bi-bar-chart-fill me-1"></i>Statistics
        </a>
    </div>

    <!-- Check-In Form -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h6 class="mb-0"><i class="bi bi-door-open-fill me-2"></i>Manual Check-In</h6>
        </div>
        <div class="card-body">
            <form action="<?= base_url('/checkins') ?>" method="POST" class="row g-3 align-items-end">
                <?= csrf_field() ?>
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Membership Code / ID</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
                        <input type="text" class="form-control form-control-lg" name="membership_code"
                               placeholder="Enter membership code..." required autofocus>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Method</label>
                    <select class="form-select" name="method">
                        <option value="manual">Manual</option>
                        <option value="qr_code">QR Code</option>
                        <option value="card">Card</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="bi bi-check-circle-fill me-1"></i>Check In
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Today's Check-Ins -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-semibold"><i class="bi bi-calendar-day me-2 text-info"></i>Today's Check-Ins</h6>
            <span class="badge bg-info"><?= count($today) ?></span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Member</th><th>Code</th><th>Time In</th><th>Time Out</th><th>Method</th><th>Status</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($today)): ?>
                            <tr><td colspan="7" class="text-center py-4 text-muted">No check-ins today</td></tr>
                        <?php else: ?>
                            <?php foreach ($today as $c): ?>
                                <tr>
                                    <td class="fw-semibold"><?= e($c['member_name']) ?></td>
                                    <td><code><?= e($c['member_code']) ?></code></td>
                                    <td><?= date('h:i A', strtotime($c['check_in_time'])) ?></td>
                                    <td><?= $c['check_out_time'] ? date('h:i A', strtotime($c['check_out_time'])) : '—' ?></td>
                                    <td><?= ucfirst(e($c['method'])) ?></td>
                                    <td><?= status_badge($c['status']) ?></td>
                                    <td>
                                        <?php if ($c['status'] === 'checked_in'): ?>
                                            <form method="POST" action="<?= base_url('/checkins/' . $c['id'] . '/checkout') ?>">
                                                <?= csrf_field() ?>
                                                <button class="btn btn-sm btn-outline-secondary">
                                                    <i class="bi bi-box-arrow-right me-1"></i>Check Out
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted small">Done</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- All Check-Ins -->
    <div class="card">
        <div class="card-header"><h6 class="mb-0 fw-semibold">All Check-In Records</h6></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr><th>#</th><th>Member</th><th>Date</th><th>Time In</th><th>Time Out</th><th>Method</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($checkins as $i => $c): ?>
                            <tr>
                                <td><?= $pagination['offset'] + $i + 1 ?></td>
                                <td><?= e($c['member_name']) ?></td>
                                <td><?= format_date($c['check_in_time']) ?></td>
                                <td><?= date('h:i A', strtotime($c['check_in_time'])) ?></td>
                                <td><?= $c['check_out_time'] ? date('h:i A', strtotime($c['check_out_time'])) : '—' ?></td>
                                <td><?= ucfirst(e($c['method'])) ?></td>
                                <td><?= status_badge($c['status']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if ($pagination['total_pages'] > 1): ?>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <small class="text-muted">Showing <?= $pagination['offset'] + 1 ?>–<?= min($pagination['offset'] + $pagination['per_page'], $pagination['total']) ?> of <?= $pagination['total'] ?></small>
            <?= pagination_links($pagination, base_url('/checkins')) ?>
        </div>
        <?php endif; ?>
    </div>
</div>
