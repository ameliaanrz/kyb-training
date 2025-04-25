<?php
$currUrl = $_SERVER['REQUEST_URI'];
?>

<nav class="navbar position-fixed top-0 left-0 navbar-expand-lg navbar-light bg-light top-nav">
  <div class="container">
    <!-- Main Title -->
    <a class="navbar-brand w-10 d-flex align-items-center gap-2" href="index.php">
      <img src="public/imgs/kyb.png" alt="" class="nav-img" />
      <span class="text-uppercase fw-semibold" style="letter-spacing: 1px; color: #082032;">Training Center</span>
    </a>
    <!-- Drop Down Button -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <!-- End Drop Down Button -->

    <!-- Nav Links -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <?php if (isset($_SESSION['NPK'])) : ?>
          <li class="nav-item">
            <a class="nav-link fw-semibold <?php echo $currUrl == '/' || strpos($currUrl, "index") !== false ? "active" : ""; ?>" href="index.php">Home</a>
          </li>
          <?php if (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] == 'RLS01') : ?>
            <li class="nav-item">
              <a class="nav-link fw-semibold <?php echo strpos($currUrl, "trainings") !== false ? "active" : ""; ?>" href="trainings.php">Trainings</a>
            </li>
            <li class="nav-item">
              <a class="nav-link fw-semibold <?php echo strpos($currUrl, "users") !== false ? "active" : ""; ?>" href="users.php">Participants</a>
            </li>
            <li class="nav-item">
              <a class="nav-link fw-semibold <?php echo strpos($currUrl, "events") !== false ? "active" : ""; ?>" href="events.php">Events</a>
            </li>
            <!--<li class="nav-item">
              <a class="nav-link fw-semibold <?php echo strpos($currUrl, "matrix") !== false ? "active" : ""; ?>" href="matrix.php">Matrix</a>
            </li>-->
            <li class="nav-item">
              <a class="nav-link fw-semibold <?php echo strpos($currUrl, "notif_view") !== false ? "active" : ""; ?>" href="notif_view.php">Notifications</a>
            </li>
            <li class="nav-item">
              <a class="nav-link fw-semibold <?php echo strpos($currUrl, "update_content") !== false ? "active" : ""; ?>" href="update_content.php">Content</a>
            </li>
          <?php endif; ?>
          <?php if (isset($_SESSION['RLS_ID']) && ($_SESSION['RLS_ID'] == 'RLS02' || $_SESSION['RLS_ID'] == 'RLS03')) : ?>
            <li class="nav-item">
              <a class="nav-link fw-semibold <?php echo strpos($currUrl, "eventusr") || strpos($currUrl, "events") !== false ? "active" : ""; ?>" href="eventusr.php">Events</a>
            </li>
          <?php endif; ?>
          <?php if (isset($_SESSION['RLS_ID']) && ($_SESSION['RLS_ID'] == 'RLS04')) : ?>
            <li class="nav-item">
              <a class="nav-link fw-semibold <?php echo strpos($currUrl, "eventprt") || strpos($currUrl, "events") !== false ? "active" : ""; ?>" href="eventprt.php">Events</a>
            </li>
          <?php endif; ?>
        <?php endif; ?>
      </ul>

      <?php if (isset($_SESSION['NPK'])) : ?>
        <!-- Profile Dropdown -->
        <div class="d-flex align-items-center gap-2">
          <?php if (isset($_SESSION['RLS_ID']) && ($_SESSION['RLS_ID'] == 'RLS01' || $_SESSION['RLS_ID'] == 'RLS02' || $_SESSION['RLS_ID'] == 'RLS03')) : ?>
            <a href="notifications.php" class="notification">
              <i class="fas fa-bell text-dark"></i>
            </a>
          <?php endif; ?>
          <div class="dropdown show">
            <button type="button" class="dropdown-toggle nav-name text-dark" data-bs-toggle="dropdown">
              <?php echo $_SESSION['NAME']; ?>
            </button>
            <div class="dropdown-menu">
              <a class="dropdown-item" href="profile.php">
                <i class="fas fa-user"></i> Profile
              </a>
              <button type="button" data-bs-toggle="modal" data-bs-target="#logoutModal" id="logout-dropdown-btn" class="dropdown-item text-danger logout-btn">
                <i class="fas fa-sign-out"></i> Logout
              </button>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
    <!-- End Nav Links -->
  </div>
</nav>