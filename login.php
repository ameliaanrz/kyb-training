<?php
include_once __DIR__ . '/partials/_guest_header.php';
require_once __DIR__ . '/includes/create-captcha.php';
?>

<div class="login-register-outer-container">
  <div class="card login-register-container py-3">
    <div class="card-body">
      <div class="card-title">
        <h1 class="text-center fs-3 fw-semibold">KYB Training Center Login</h1>
        <hr class="main-hr mx-auto" style="width: 25% !important;">
      </div>
      <div class="card-text mt-4">
        <form id="login_form" action='' method="POST">
          <label for="npk_input" class="form-label">NPK</label>
          <input type="text" name="npk" id="npk_input" class="form-control" placeholder="Enter your provided 6 digits NPK">
          <small id="npk_error" class="text-danger d-block mt-1"></small>

          <label for="password_input" class="form-label mt-2">Password</label>
          <input type="password" name="password" id="password_input" class="form-control" placeholder="Enter your provided password">
          <small id="password_error" class="text-danger d-block mt-1"></small>

          <!-- Captcha Start -->
          <div id="captcha_content" class="d-flex flex-row">
            <!-- Captcha content will be loaded here from components/captcha.php -->
          </div>
          <small id="captcha_error" class="text-danger d-block mt-1"></small>
          <!-- Captcha End -->

          <button id="login_btn" type="button" class="btn btn-danger px-4 py-2 ms-auto mt-2 d-block">Login</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- OTP Modal -->
<div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title; text-center;" id="otpModalLabel">Verification Code</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="d-flex justify-content-center">
          <input type="text" class="otp-input form-control text-center" maxlength="1">
          <input type="text" class="otp-input form-control text-center" maxlength="1">
          <input type="text" class="otp-input form-control text-center" maxlength="1">
          <input type="text" class="otp-input form-control text-center" maxlength="1">
          <input type="text" class="otp-input form-control text-center" maxlength="1">
          <input type="text" class="otp-input form-control text-center" maxlength="1">
        </div>
        <br>
        <div class="d-flex justify-content-center">
          <button class="fw-semibold text-danger border-0 bg-transparent p-0" id="resend_otp_btn">Re-send OTP</button>
        </div>
        <small id="otp_error" class="text-danger d-block mt-1"></small>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" id="verify_otp_btn" class="btn btn-danger px-4 py-2 ms-auto mt-2 d-block">Verify OTP</button>
      </div>
    </div>
  </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script type="text/javascript" defer>
  $(document).ready(function() {
    $("#captcha_content").load("components/captcha.php");

    // Trigger klik login saat tekan Enter di input field
    $("#npk_input, #password_input, #captcha_input").keydown(function(e) {
      if (e.keyCode == 13) {
        $("#login_btn").click();
      }
    });

    $("#login_btn").click(function() {
      const npk = $("#npk_input").val();
      const password = $("#password_input").val();
      const captcha = $("#captcha_input").val();

      $("#npk_error").html("");
      $("#password_error").html("");
      $("#captcha_error").html("");

      let hasError = false;

      // Validasi di sisi klien sebelum request ke server
      if (npk === "") {
        $("#npk_error").html("NPK is required");
        hasError = true;
      }

      if (password === "") {
        $("#password_error").html("Password is required");
        hasError = true;
      }

      if (captcha === "") {
        $("#captcha_error").html("Captcha is required");
        hasError = true;
      }

      if (hasError) {
        return; // Hentikan proses jika ada error
      }

      $.post("includes/login.inc.php", {
          npk,
          password,
          captcha
        })
        .done(function() {
          $('#otpModal').modal('show');
          startCountdown(); // Start countdown for OTP resend
        })
        .fail(function(xhr) {
          if (xhr?.responseJSON) {
            const errors = xhr.responseJSON;
            $("#npk_error").html(errors["npk"] || "");
            $("#password_error").html(errors["password"] || "");
            $("#captcha_error").html(errors["captcha"] || "");

            // Reload captcha
            $("#captcha_content").load("components/captcha.php");
          }
        });
    });

    // Trigger klik login saat tekan Enter di input field
    $(".otp-input").keydown(function(e) {
      if (e.keyCode == 13) {
        e.preventDefault(); // Mencegah form submit jika ada
        $("#verify_otp_btn").click();
      }
    });

    $(".otp-input").on("input", function() {
      let input = $(this);
      let value = input.val();

      if (value.length === 1) {
        input.next(".otp-input").focus();
      }
    });

    $(".otp-input").on("keydown", function(e) {
      let input = $(this);

      if (e.key === "Backspace" && input.val() === "") {
        input.prev(".otp-input").focus();
      }
    });

    $("#verify_otp_btn").click(function() {
      let otp = $(".otp-input").map(function() {
        return $(this).val();
      }).get().join("");

      if (otp.length < 6) {
        $("#otp_error").html("Complete the OTP input.");
        return;
      }

      $.post("includes/otp.inc.php", {
          otp
        })
        .done(function() {
          location.reload(); // Jika sukses, reload halaman atau redirect
        })
        .fail(function(xhr) {
          if (xhr?.responseJSON) {
            const errors = xhr.responseJSON;
            $("#otp_error").html(errors["otp"] || "");
          }
        });
    });

    let countdown = 300; // 3 minutes in seconds
    const resendButton = $("#resend_otp_btn");

    function startCountdown() {
      const interval = setInterval(() => {
        if (countdown <= 0) {
          clearInterval(interval);
          resendButton.prop("disabled", false).text("Re-send OTP");
        } else {
          let minutes = Math.floor(countdown / 60);
          let seconds = countdown % 60;
          let formattedTime = `${minutes}:${seconds.toString().padStart(2, "0")}`; // Format MM:SS

          resendButton.prop("disabled", true).text(`Resend OTP in ${formattedTime}`);
          countdown--;
        }
      }, 1000);
    }

    resendButton.click(function() {
      if (countdown <= 0) {
        // Call the function to resend OTP
        $.post("includes/resend-otp.inc.php", {
            npk: $("#npk_input").val()
          })
          .done(function(response) {
            // Handle success
            countdown = 300; // Reset countdown
            startCountdown();
          })
          .fail(function(xhr) {
            // Handle error
          });
      }
    });

    $("#reload_captcha_btn").click(function() {
      $("#captcha_content").load("components/captcha.php");
    });
  });
</script>

<?php
include_once __DIR__ . '/partials/_footer.php';
?>