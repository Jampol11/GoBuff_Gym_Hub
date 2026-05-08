<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div><h2 class="page-title">Dietary Log</h2><p class="page-subtitle">Track daily nutrition and calorie intake</p></div>
    </div>

    <div class="row g-4">
        <!-- Log Form -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-success text-white"><h6 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Add Food Log</h6></div>
                <div class="card-body">
                    <form action="<?= base_url('/diet') ?>" method="POST" novalidate>
                        <?= csrf_field() ?>

                        <?php if (!empty($members)): ?>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Member</label>
                            <select class="form-select" name="member_id" required>
                                <option value="">Select member</option>
                                <?php foreach ($members as $m): ?>
                                    <option value="<?= $m['id'] ?>" <?= $member_id == $m['id'] ? 'selected' : '' ?>>
                                        <?= e($m['first_name'] . ' ' . $m['last_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Date</label>
                            <input type="date" class="form-control" name="log_date" value="<?= $date ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Meal Type</label>
                            <select class="form-select" name="meal_type" required>
                                <option value="breakfast">Breakfast</option>
                                <option value="lunch">Lunch</option>
                                <option value="dinner">Dinner</option>
                                <option value="snack">Snack</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Food Items <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="food_items" rows="2" required placeholder="e.g. Rice, Chicken, Vegetables"></textarea>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Calories</label>
                                <input type="number" class="form-control" name="calories" min="0" step="0.1" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Protein (g)</label>
                                <input type="number" class="form-control" name="protein" min="0" step="0.1">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Carbs (g)</label>
                                <input type="number" class="form-control" name="carbs" min="0" step="0.1">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Fat (g)</label>
                                <input type="number" class="form-control" name="fat" min="0" step="0.1">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Notes</label>
                            <input type="text" class="form-control" name="notes">
                        </div>
                        <button type="submit" class="btn btn-success w-100"><i class="bi bi-plus-circle me-1"></i>Add Log</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Logs & Summary -->
        <div class="col-lg-8">
            <!-- Date Filter -->
            <div class="card mb-4">
                <div class="card-body py-3">
                    <form method="GET" action="<?= base_url('/diet') ?>" class="row g-2 align-items-center">
                        <?php if (!empty($members)): ?>
                        <div class="col-md-4">
                            <select class="form-select" name="member_id">
                                <option value="">Select member</option>
                                <?php foreach ($members as $m): ?>
                                    <option value="<?= $m['id'] ?>" <?= $member_id == $m['id'] ? 'selected' : '' ?>>
                                        <?= e($m['first_name'] . ' ' . $m['last_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        <div class="col-md-4">
                            <input type="date" class="form-control" name="date" value="<?= $date ?>">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Daily Summary -->
            <div class="card mb-4">
                <div class="card-header"><h6 class="mb-0 fw-semibold">Daily Summary — <?= format_date($date) ?></h6></div>
                <div class="card-body">
                    <div class="row text-center g-3">
                        <div class="col-3">
                            <div class="fs-3 fw-bold text-danger"><?= number_format($daily_calories, 0) ?></div>
                            <small class="text-muted">Total Calories</small>
                        </div>
                        <?php
                        $totalProtein = array_sum(array_column($logs, 'protein'));
                        $totalCarbs   = array_sum(array_column($logs, 'carbs'));
                        $totalFat     = array_sum(array_column($logs, 'fat'));
                        ?>
                        <div class="col-3">
                            <div class="fs-3 fw-bold text-primary"><?= number_format($totalProtein, 1) ?>g</div>
                            <small class="text-muted">Protein</small>
                        </div>
                        <div class="col-3">
                            <div class="fs-3 fw-bold text-warning"><?= number_format($totalCarbs, 1) ?>g</div>
                            <small class="text-muted">Carbs</small>
                        </div>
                        <div class="col-3">
                            <div class="fs-3 fw-bold text-info"><?= number_format($totalFat, 1) ?>g</div>
                            <small class="text-muted">Fat</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Log Entries -->
            <div class="card">
                <div class="card-header"><h6 class="mb-0 fw-semibold">Food Log Entries</h6></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr><th>Meal</th><th>Food Items</th><th>Calories</th><th>Protein</th><th>Carbs</th><th>Fat</th><th>Action</th></tr>
                            </thead>
                            <tbody>
                                <?php if (empty($logs)): ?>
                                    <tr><td colspan="7" class="text-center py-4 text-muted">No food logs for this date</td></tr>
                                <?php else: ?>
                                    <?php foreach ($logs as $log): ?>
                                        <tr>
                                            <td><span class="badge bg-<?= $log['meal_type'] === 'breakfast' ? 'warning' : ($log['meal_type'] === 'lunch' ? 'success' : ($log['meal_type'] === 'dinner' ? 'primary' : 'secondary')) ?>"><?= ucfirst($log['meal_type']) ?></span></td>
                                            <td><?= e($log['food_items']) ?></td>
                                            <td><?= number_format($log['calories'], 0) ?> kcal</td>
                                            <td><?= number_format($log['protein'], 1) ?>g</td>
                                            <td><?= number_format($log['carbs'], 1) ?>g</td>
                                            <td><?= number_format($log['fat'], 1) ?>g</td>
                                            <td>
                                                <form method="POST" action="<?= base_url('/diet/' . $log['id'] . '/delete') ?>"
                                                      class="d-inline" onsubmit="return confirm('Delete this entry?')">
                                                    <?= csrf_field() ?>
                                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
