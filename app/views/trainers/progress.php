<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div><h2 class="page-title">Progress Tracking</h2><p class="page-subtitle">Monitor member fitness progress</p></div>
    </div>

    <!-- Add Progress Form -->
    <?php if (has_role(['gym_owner','admin','trainer'])): ?>
    <div class="card mb-4">
        <div class="card-header bg-info text-white"><h6 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Record Progress</h6></div>
        <div class="card-body">
            <form action="<?= base_url('/trainers/progress') ?>" method="POST" class="row g-3">
                <?= csrf_field() ?>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Member</label>
                    <select class="form-select" name="member_id" required>
                        <option value="">Select member</option>
                        <?php
                        $memberModel = new Member();
                        foreach ($memberModel->findAll('first_name ASC') as $m):
                        ?>
                            <option value="<?= $m['id'] ?>"><?= e($m['first_name'] . ' ' . $m['last_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Weight (kg)</label>
                    <input type="number" class="form-control" name="weight_kg" step="0.1" min="0">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Height (cm)</label>
                    <input type="number" class="form-control" name="height_cm" step="0.1" min="0">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">BMI</label>
                    <input type="number" class="form-control" name="bmi" step="0.01" min="0">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Body Fat %</label>
                    <input type="number" class="form-control" name="body_fat_pct" step="0.1" min="0" max="100">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Notes</label>
                    <input type="text" class="form-control" name="notes">
                </div>
                <div class="col-auto align-self-end">
                    <button type="submit" class="btn btn-info text-white"><i class="bi bi-save me-1"></i>Record</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Progress Records -->
    <div class="card">
        <div class="card-header"><h6 class="mb-0 fw-semibold">Progress Records</h6></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr><th>Member</th><th>Weight</th><th>Height</th><th>BMI</th><th>Body Fat</th><th>Notes</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($records)): ?>
                            <tr><td colspan="7" class="text-center py-5 text-muted">No progress records found</td></tr>
                        <?php else: ?>
                            <?php foreach ($records as $r): ?>
                                <tr>
                                    <td class="fw-semibold"><?= e($r['member_name']) ?></td>
                                    <td><?= e($r['weight_kg']) ?> kg</td>
                                    <td><?= e($r['height_cm']) ?> cm</td>
                                    <td>
                                        <?php $bmi = (float)$r['bmi']; ?>
                                        <span class="badge bg-<?= $bmi < 18.5 ? 'info' : ($bmi < 25 ? 'success' : ($bmi < 30 ? 'warning' : 'danger')) ?>">
                                            <?= number_format($bmi, 1) ?>
                                        </span>
                                    </td>
                                    <td><?= e($r['body_fat_pct']) ?>%</td>
                                    <td><?= e($r['notes'] ?? '—') ?></td>
                                    <td><?= format_datetime($r['recorded_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
