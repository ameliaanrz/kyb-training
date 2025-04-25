$(document).ready(function () {
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
