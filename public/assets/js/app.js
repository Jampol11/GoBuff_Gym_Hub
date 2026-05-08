/**
 * GoBuff: Gym Hub — Main JavaScript
 */

document.addEventListener('DOMContentLoaded', function () {

  // ── Sidebar Toggle ──────────────────────────────────────────
  const sidebar        = document.getElementById('sidebar');
  const sidebarToggle  = document.getElementById('sidebarToggle');
  const sidebarClose   = document.getElementById('sidebarClose');
  const sidebarOverlay = document.getElementById('sidebarOverlay');

  function openSidebar() {
    sidebar?.classList.add('show');
    sidebarOverlay?.classList.add('show');
    document.body.style.overflow = 'hidden';
  }

  function closeSidebar() {
    sidebar?.classList.remove('show');
    sidebarOverlay?.classList.remove('show');
    document.body.style.overflow = '';
  }

  sidebarToggle?.addEventListener('click', () => {
    if (window.innerWidth < 992) {
      openSidebar();
    } else {
      // Desktop: collapse/expand
      sidebar?.classList.toggle('collapsed');
      document.querySelector('.main-content')?.classList.toggle('expanded');
    }
  });

  sidebarClose?.addEventListener('click', closeSidebar);
  sidebarOverlay?.addEventListener('click', closeSidebar);

  // Close sidebar on resize
  window.addEventListener('resize', () => {
    if (window.innerWidth >= 992) {
      closeSidebar();
    }
  });

  // ── Dark Mode Toggle ────────────────────────────────────────
  const themeToggle = document.getElementById('themeToggle');
  const themeIcon   = document.getElementById('themeIcon');
  const htmlEl      = document.documentElement;

  const savedTheme = localStorage.getItem('gobuff_theme') || 'light';
  applyTheme(savedTheme);

  themeToggle?.addEventListener('click', () => {
    const current = htmlEl.getAttribute('data-bs-theme');
    const next    = current === 'dark' ? 'light' : 'dark';
    applyTheme(next);
    localStorage.setItem('gobuff_theme', next);
  });

  function applyTheme(theme) {
    htmlEl.setAttribute('data-bs-theme', theme);
    if (themeIcon) {
      themeIcon.className = theme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
    }
  }

  // ── Auto-dismiss Alerts ─────────────────────────────────────
  const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
  alerts.forEach(alert => {
    setTimeout(() => {
      const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
      bsAlert?.close();
    }, 5000);
  });

  // ── Confirm Delete ──────────────────────────────────────────
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', function (e) {
      if (!confirm(this.dataset.confirm || 'Are you sure?')) {
        e.preventDefault();
      }
    });
  });

  // ── Notification Count Refresh ──────────────────────────────
  function refreshNotifCount() {
    fetch('/api/notifications/count')
      .then(r => r.json())
      .then(data => {
        const badges = document.querySelectorAll('.notif-badge');
        badges.forEach(b => {
          if (data.count > 0) {
            b.textContent = data.count > 9 ? '9+' : data.count;
            b.style.display = '';
          } else {
            b.style.display = 'none';
          }
        });
      })
      .catch(() => {});
  }

  // Refresh every 60 seconds
  setInterval(refreshNotifCount, 60000);

  // ── BMI Calculator ──────────────────────────────────────────
  const weightInput = document.querySelector('[name="weight_kg"]');
  const heightInput = document.querySelector('[name="height_cm"]');
  const bmiInput    = document.querySelector('[name="bmi"]');

  function calcBMI() {
    const w = parseFloat(weightInput?.value);
    const h = parseFloat(heightInput?.value) / 100;
    if (w > 0 && h > 0 && bmiInput) {
      bmiInput.value = (w / (h * h)).toFixed(2);
    }
  }

  weightInput?.addEventListener('input', calcBMI);
  heightInput?.addEventListener('input', calcBMI);

  // ── Membership Expiry Auto-calc ─────────────────────────────
  const planTypeSelect = document.querySelector('[name="plan_type"]');
  const startDateInput = document.querySelector('[name="start_date"]');
  const expiryInput    = document.querySelector('[name="expiry_date"]');

  function calcExpiry() {
    const start = startDateInput?.value;
    const type  = planTypeSelect?.value;
    if (!start || !type || !expiryInput) return;

    const d = new Date(start);
    switch (type) {
      case 'daily':       d.setDate(d.getDate() + 1);    break;
      case 'monthly':     d.setMonth(d.getMonth() + 1);  break;
      case 'quarterly':   d.setMonth(d.getMonth() + 3);  break;
      case 'semi_annual': d.setMonth(d.getMonth() + 6);  break;
      case 'annual':      d.setFullYear(d.getFullYear() + 1); break;
    }
    expiryInput.value = d.toISOString().split('T')[0];
  }

  planTypeSelect?.addEventListener('change', calcExpiry);
  startDateInput?.addEventListener('change', calcExpiry);

  // ── Table Search (client-side) ──────────────────────────────
  const tableSearch = document.getElementById('tableSearch');
  if (tableSearch) {
    tableSearch.addEventListener('input', function () {
      const q = this.value.toLowerCase();
      document.querySelectorAll('tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
      });
    });
  }

  // ── Tooltips ────────────────────────────────────────────────
  document.querySelectorAll('[title]').forEach(el => {
    new bootstrap.Tooltip(el, { trigger: 'hover' });
  });

  // ── File Input Preview ──────────────────────────────────────
  document.querySelectorAll('input[type="file"][accept*="image"]').forEach(input => {
    input.addEventListener('change', function () {
      const file = this.files[0];
      if (!file) return;
      const preview = document.getElementById(this.id + '_preview');
      if (preview) {
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
        reader.readAsDataURL(file);
      }
    });
  });

  // ── Form Validation ─────────────────────────────────────────
  document.querySelectorAll('form[novalidate]').forEach(form => {
    form.addEventListener('submit', function (e) {
      if (!this.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
      }
      this.classList.add('was-validated');
    });
  });

  // ── Active Nav Link ─────────────────────────────────────────
  const currentPath = window.location.pathname;
  document.querySelectorAll('.nav-link').forEach(link => {
    const href = link.getAttribute('href');
    if (href && href !== '#' && currentPath.startsWith(href) && href !== '/') {
      link.classList.add('active');
    }
  });

  console.log('%cGoBuff: Gym Hub v1.0.0', 'color:#0d6efd;font-weight:bold;font-size:14px');
});
