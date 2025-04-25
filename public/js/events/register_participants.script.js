$(document).ready(function () {
  $("#register_participants_btn").click(function (e) {
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
      $.post("includes/notifications.inc.php?type=4", {
        evt_id,
      })
        .done(function (type, status, xhr) {
          console.log(xhr.status, xhr.responseJSON);
        })
        .fail(function (xhr, type, status) {
          console.log(type, status, xhr.status, xhr.responseJSON);
        });
    }

    // register participants
    $("#register_participants_form").submit();
  });

  const selectInputs = $("select");
  const queryForm = $("#query_form");
  const shownListsForm = $("#select_list_form");
  const checkAllInput = $("#checkall");
  const checks = $(".users-check");

  checkAllInput.on("change", function (e) {
    const state = this.checked;

    checks.each(function () {
      if (!this.hasAttribute("disabled")) {
        this.checked = state;
      }
    });
  });

  selectInputs.each(function () {
    this.onchange = function () {
      if (this.id == "lists_shown") {
        shownListsForm.submit();
      } else {
        queryForm.submit();
      }
    };
  });
});
