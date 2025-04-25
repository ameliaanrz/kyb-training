<?php
include_once __DIR__ . '/partials/_header.php';
$editing = false;
if (!isset($_SESSION['NPK'])) {
  header("Location: login.php");
}
require_once __DIR__ . '/includes/admin-profile.inc.php';
?>

<h1 class="fs-2 fw-semibold">Admin Profile</h1>
<p><?php echo $adminProfile['NAME']; ?>&apos;s administrator profile details</p>
<hr>
<form action="" method="PUT" class="mt-4">
  <div>
    <label for="npk" class="form-label fw-semibold">NPK</label>
    <input type="text" name="npk" id="npk" class="form-control" value="<?php echo $adminProfile['NPK']; ?>" placeholder="Enter unique NPK" disabled>
  </div>
  <div class="mt-4">
    <label for="first_name" class="form-label fw-semibold">First and last name</label>
    <div class="input-group">
      <input type="text" name="first_name" id="first_name" class="form-control" value="<?php $tmp = explode(' ', $adminProfile['NAME']);
                                                                                        echo $tmp[0]; ?>" placeholder="Enter first name" disabled>
      <input type="text" name="last_name" id="last_name" value="<?php echo implode(' ', array_slice(explode(' ', $adminProfile['NAME']), 1, count(explode(' ', $adminProfile['NAME'])))); ?>" class="form-control" placeholder="Enter last name" disabled>
    </div>
  </div>
  <div class="mt-4">
    <label for="RLS_ID" class="fw-semibold">Role</label>
    <select name="role" id="RLS_ID" class="form-select mt-2" disabled>
      <option value="" default>-- Role is empty --</option>
      <option value="<?php echo $adminProfile['ROLE']; ?>" <?php echo !empty($adminProfile['ROLE']) ? "selected" : ""; ?>><?php echo $adminProfile['ROLE']; ?></option>
    </select>
  </div>
  <div class="mt-4">
    <label for="company" class="fw-semibold">Company</label>
    <select name="company" id="company" class="form-select mt-2" disabled>
      <option value="" default>-- Company is empty --</option>
      <option value="<?php echo $adminProfile['COMPANY']; ?>" <?php echo !empty($adminProfile['COMPANY']) ? "selected" : ""; ?>><?php echo $adminProfile['COMPANY']; ?></option>
    </select>
  </div>
  <div class="mt-4">
    <label for="department" class="fw-semibold">Department</label>
    <select name="department" id="department" class="form-select mt-2" disabled>
      <option value="" default>-- Department is empty --</option>
      <option value="<?php echo $adminProfile['DEPARTMENT']; ?>" <?php echo !empty($adminProfile['DEPARTMENT']) ? "selected" : ""; ?>><?php echo $adminProfile['DEPARTMENT']; ?></option>
    </select>
  </div>
  <div class="mt-4">
    <label for="section" class="fw-semibold">Section and subsection</label>
    <div class="input-group">
      <select name="section" id="section" class="form-select mt-2" disabled>
        <option value="" default>-- Section is empty --</option>
        <option value="<?php echo $adminProfile['SECTION']; ?>" <?php echo !empty($adminProfile['SECTION']) ? "selected" : ""; ?>><?php echo $adminProfile['SECTION']; ?></option>
      </select>
      <select name="subsection" id="subsection" class="form-select mt-2" disabled>
        <option value="" default>-- Subsection is empty --</option>
        <option value="<?php echo $adminProfile['SUBSECTION']; ?>" <?php echo !empty($adminProfile['SUBSECTION']) ? "selected" : ""; ?>><?php echo $adminProfile['SUBSECTION']; ?></option>
      </select>
    </div>
  </div>
</form>
<button type="button" data-bs-toggle="modal" data-bs-target="#logoutModal" class="btn btn-danger ms-auto mt-5 d-block">
  <i class="fas fa-sign-out"></i> Logout
</button>

<?php
include_once __DIR__ . '/partials/_footer.php';
?>