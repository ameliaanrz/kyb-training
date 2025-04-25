$(document).ready(function () {
  const form = $("form");
  const editBtn = $("#editBtn");
  const cancelBtn = $("#cancelBtn");
  const deleteBtn = $("#deleteBtn");
  const textInputs = $("input[type='text']");
  const selectInputs = $("select");
  const dateInputs = $('input[type="date"]');
  const trainingStatusCheckbox = $("#training_status");
  const trainingStatusLabel = $("#training_status_label");
  const timeInputs = $('input[type="time"]');

  const editBtnGroup = $("#editBtnGroup");
  const updateBtnGroup = $("#updateBtnGroup");

  const startDate = $("#start_date");
  const endDate = $("#end_date");
  const days = $("#days");
  let daysCount;

  const startTime = $("#start_time");
  const endTime = $("#end_time");
  const duration = $("#duration");
  let timeCount;

  // handle click edit button
  editBtn.click(function () {
    editBtnGroup.removeClass("d-flex").addClass("d-none");
    updateBtnGroup.removeClass("d-none").addClass("d-flex");
    textInputs.removeAttr("disabled");
    selectInputs.removeAttr("disabled");
    dateInputs.removeAttr("disabled");
    timeInputs.removeAttr("disabled");
    trainingStatusCheckbox.removeAttr("disabled");
  });

  // handle click cancel button
  cancelBtn.click(function () {
    location.reload();
  });

  // change values for training status checkbox on change
  trainingStatusCheckbox.on("change", function () {
    if (this.checked) {
      this.value = 1;
      trainingStatusLabel.html(
        '<span class="text-success fw-semibold">Active</span>'
      );
    } else {
      this.value = 0;
      trainingStatusLabel.html(
        '<span class="text-danger fw-semibold">Not Activated</span>'
      );
    }
  });

  // calculate days
  function calcDays() {
    const date1 = new Date(startDate.val());
    const date2 = new Date(endDate.val());

    daysCount = Math.ceil(Math.abs(date2 - date1) / (1000 * 60 * 60 * 24)) + 1;
    days.val(daysCount);
    if (date1 > date2) {
      const tmp = days.val();
      days.val(tmp * -1);
    }
  }

  // calculate hours
  function calcTime() {
    const time1 = startTime.val();
    const time2 = endTime.val();
    timeCount = Number(time2.split(":")[0]) - Number(time1.split(":")[0]);
    duration.val(timeCount);
  }

  calcDays();
  calcTime();

  startDate.on("change", function () {
    calcDays();
  });

  endDate.on("change", function () {
    calcDays();
  });

  startTime.on("change", function () {
    calcTime();
  });

  endTime.on("change", function () {
    calcTime();
  });
});
