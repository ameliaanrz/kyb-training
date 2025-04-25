<?php
include_once __DIR__ . '/partials/_header.php';
if (isset($_SESSION['NPK'])) {
  header("Location: index.php");
}
require_once __DIR__ . '/includes/register.inc.php';
?>

<div class="login-register-outer-container">
  <div class="card login-register-container py-3">
    <div class="card-body">
      <div class="card-title">
        <h1 class="text-center fs-3 fw-semibold">Create new account</h1>
        <hr class="title-hr">
      </div>
      <div class="card-text mt-4">
        <form action="" method="POST">
          <label for="npk" class="form-label">NPK</label>
          <input type="text" name="npk" id="npk" class="form-control" value="<?php echo $npk; ?>" placeholder="Enter your NPK here" autocomplete="off">
          <?php if (isset($errors['npk'])) : ?>
            <small class="text-danger d-block mt-1"><?php echo $errors['npk']; ?></small>
          <?php endif; ?>
          <label for="name" class="form-label mt-3">Name</label>
          <input type="text" name="name" id="name" class="form-control" value="<?php echo $name; ?>" placeholder="Enter your full name" autocomplete="off">
          <?php if (isset($errors['name'])) : ?>
            <small class="text-danger d-block mt-1"><?php echo $errors['name']; ?></small>
          <?php endif; ?>
          <label for="password" class="form-label mt-3">Password</label>
          <input type="password" name="password" id="password" class="form-control" placeholder="Enter your provided password" autocomplete="off">
          <?php if (isset($errors['password'])) : ?>
            <small class="text-danger d-block mt-1"><?php echo $errors['password']; ?></small>
          <?php endif; ?>
          <label for="password_confirmation" class="form-label mt-3">Password</label>
          <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Re-enter your provided password" autocomplete="off">
          <?php if (isset($errors['password_confirmation'])) : ?>
            <small class="text-danger d-block mt-1"><?php echo $errors['password_confirmation']; ?></small>
          <?php endif; ?>
          <p class="mt-4 text-end">Already have an account? <a href="login.php" class="redirect-link">Login into your account</a></p>
          <button type="submit" class="btn btn-dark ms-auto d-block">Register</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php
include_once __DIR__ . '/partials/_footer.php';
?>