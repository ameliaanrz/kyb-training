$(document).ready(function () {
  $("input[type='submit']").click(function (e) {
    e.preventDefault();

    /**
     * create notification process is here
     */

    // get post datas
    const registeredUsers = $(
      "input[name='registered_users[]']"
    ).serializeArray();
    const evt_id = $("#evt_id_input").val();

    // create notification
    if (registeredUsers?.length) {
      $.post("includes/notifications.inc.php?type=5", {
        evt_id,
      })
        .done(function (type, status, xhr) {
          console.log(xhr.status, xhr.responseJSON);
        })
        .fail(function (xhr, type, status) {
          console.log(type, status, xhr.status, xhr.responseJSON);
        });
    }

    // set approval input to 1
    if (this.name == "approval") {
      $("input[name='approval_type']").val(1);
    } else {
      $("input[name='approval_type']").val(2);
    }

    // register participants
    $("#approval_form").submit();
  });

  const checkAllInput = document.getElementById("checkall");
  const checks = document.getElementsByName("registered_users[]");

  const selectInputs = $("select");
  const queryForm = $("#query_form");

  $("#lists_shown").on("change", function () {
    $("#select_list_form").submit();
  });

  checkAllInput.onchange = function (e) {
    checks.forEach(function (ch) {
      ch.checked = checkAllInput.checked;
    });
  };

  selectInputs.change(function () {
    queryForm.submit();
  });
});
