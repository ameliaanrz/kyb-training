$(function () {
  $("#lists_shown").on("change", function () {
    $("#select_list_form").submit();
  });
  $("#organizer_search, #approval_status, #completion_status").on(
    "change",
    function () {
      $("#filter_form").submit();
    }
  );
});
