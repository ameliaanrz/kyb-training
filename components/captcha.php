<?php
session_start();

require_once __DIR__ . '/../includes/create-captcha.php';
?>

<div class="d-flex gap-2 align-items-end mt-3 w-50">
  <i id="reload_captcha_btn" class="fas fa-rotate-right fs-5" style="cursor: pointer"></i>
  <img src="<?php echo createCaptcha(); ?>" alt="" class="border-1 rounded-2 border-black" style="width: 100%">
</div>
<div class="w-50 h-100 ps-2 mt-3">
  <input type="text" name="captcha" id="captcha_input" autocomplete="off" class="form-control mt-2" placeholder="Enter the captcha code">
</div>

<script type="text/javascript" defer>
  $("#reload_captcha_btn").click(function() {
    $("#captcha_content").load("components/captcha.php");
  });

  $("#captcha_input").keydown(function(e) {
      if (e.keyCode == 13) {
        $("#login_btn").click();
      }
    });
</script>