<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div><h2 class="page-title">Check-In Statistics</h2></div>
        <a href="<?= base_url('/checkins') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h6 class="mb-0 fw-semibold">Weekly Check-Ins</h6></div>
                <div class="card-body"><canvas id="weeklyChart" height="120"></canvas></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h6 class="mb-0 fw-semibold">Monthly Check-Ins (12 months)</h6></div>
                <div class="card-body"><canvas id="monthlyChart" height="120"></canvas></div>
            </div>
        </div>
    </div>
</div>
<script>
const weeklyData = <?= json_encode($weekly) ?>;
const monthlyData = <?= json_encode($monthly) ?>;
const monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

new Chart(document.getElementById('weeklyChart'), {
    type: 'line',
    data: {
        labels: weeklyData.map(d => d.date),
        datasets: [{ label: 'Check-Ins', data: weeklyData.map(d => d.count),
            borderColor: '#0d6efd', backgroundColor: 'rgba(13,110,253,0.1)', fill: true, tension: 0.4 }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: {
        labels: monthlyData.map(d => monthNames[d.month - 1] + ' ' + d.year),
        datasets: [{ label: 'Check-Ins', data: monthlyData.map(d => d.count),
            backgroundColor: 'rgba(25,135,84,0.7)', borderColor: '#198754', borderWidth: 2, borderRadius: 4 }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});
</script>
