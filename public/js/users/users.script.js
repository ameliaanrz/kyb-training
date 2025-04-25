$(function () {
  const queryForm = $("#query_form");
  $(
    "#company_select,#dept_select,#section_select,#subsec_select,#grade_select,#gender_select,#training_select"
  ).on("change", function () {
    queryForm.submit();
  });
  $("#lists_shown").on("change", function () {
    $("#select_list_form").submit();
  });
});
