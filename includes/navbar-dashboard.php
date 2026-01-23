<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">
    <button class="btn btn-outline-primary d-lg-none"
        type="button"
        data-bs-toggle="offcanvas"
        data-bs-target="#sidebarAdmin"
        aria-controls="sidebarAdmin">
    <i class="bi bi-list"></i>
</button>

        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="../../assets/logoW.png" alt="Logo" width="35" class="me-2">
            <strong>Student Base</strong>
        </a>

        <!-- Right-side menu -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#dashboardNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="dashboardNavbar">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                <!-- Notifications -->
                    <div class="dropdown me-2">
                      <button class="btn btn-light position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell"></i>
                        <span id="notifBadge"
                              class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">
                          0
                        </span>
                      </button>

                      <ul class="dropdown-menu dropdown-menu-end p-0" style="width: 340px;">
                        <li class="px-3 py-2 border-bottom fw-semibold">Notifications</li>
                        <li>
                          <div id="notifList" class="list-group list-group-flush" style="max-height: 320px; overflow:auto;">
                            <div class="p-3 text-muted small">Loading...</div>
                          </div>
                        </li>
                      </ul>
                    </div>               
                <!-- User dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <?= htmlspecialchars($_SESSION['name'] ?? 'User'); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="profile.php">Profile Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="/studentbase/public/logout.php">
                                 <i class="bi bi-box-arrow-right me-1"></i> Logout
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>
    </div>
</nav>

<script>
const notifBadge = document.getElementById('notifBadge');
const notifList  = document.getElementById('notifList');

// IMPORTANT: absolute paths so it works from ANY dashboard page
const FETCH_URL = '/studentbase/dashboards/shared/notifications-fetch.php';
const READ_URL  = '/studentbase/dashboards/shared/notifications-read.php';

function escapeHtml(str) {
  return String(str)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}

function renderNotifs(data) {
  if (!data.items || data.items.length === 0) {
    notifList.innerHTML = `<div class="p-3 text-muted small">No notifications yet.</div>`;
  } else {
    notifList.innerHTML = data.items.map(n => {
      const unread = parseInt(n.is_read) === 0;
      return `
        <button type="button"
                class="list-group-item list-group-item-action ${unread ? 'fw-semibold' : ''}"
                data-nid="${n.notification_id}">
          <div class="small">${escapeHtml(n.message)}</div>
          <div class="text-muted small">${n.created_at}</div>
        </button>
      `;
    }).join('');
  }

  if (data.unread > 0) {
    notifBadge.textContent = data.unread;
    notifBadge.classList.remove('d-none');
  } else {
    notifBadge.classList.add('d-none');
  }
}

function fetchNotifs() {
  fetch(FETCH_URL)
    .then(res => res.json())
    .then(data => {
      if (!data.ok) {
        notifList.innerHTML = `<div class="p-3 text-danger small">${escapeHtml(data.error || 'Failed')}</div>`;
        return;
      }
      renderNotifs(data);
    })
    .catch(() => {
      notifList.innerHTML = `<div class="p-3 text-danger small">Failed to load notifications.</div>`;
    });
}

// Mark as read on click
notifList.addEventListener('click', function(e) {
  const btn = e.target.closest('[data-nid]');
  if (!btn) return;

  const nid = btn.getAttribute('data-nid');

  fetch(READ_URL, {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'notification_id=' + encodeURIComponent(nid)
  }).then(() => fetchNotifs());
});

// initial + poll
fetchNotifs();
setInterval(fetchNotifs, 10000);
</script>

