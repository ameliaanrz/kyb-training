$(document).ready(function () {
  // listen to submit button submission
  $("#submit_button").click(function (e) {
    e.preventDefault();

    /**
     * Create notification process is here
     */

    // get post datas
    const t_id = $("#t_id").val();
    const org_id = $("#organizer").val();
    const new_org = $("#new_organizer").val();
    const ta_id = $("#trainer").val();
    const new_trainer = $("#new_trainer").val();
    const loc_id = $("#location").val();
    const new_location = $("#new_location").val();
    const start_date = $("#start_date").val();
    const end_date = $("#end_date").val();
    const start_time = $("#start_time").val();
    const end_time = $("#end_time").val();

    // create notif
    if (
      t_id &&
      (org_id || new_org) &&
      (ta_id || new_trainer) &&
      (loc_id || new_location) &&
      start_date &&
      end_date &&
      start_time &&
      end_time
    ) {
      $.post("includes/notifications.inc.php?type=2", {
        t_id,
      })
        .done(function (type, status, xhr) {
          console.log(xhr.status, xhr.responseJSON);
        })
        .fail(function (xhr, type, status) {
          console.log(xhr.status, xhr.responseJSON);
        });
    }

    // create new event
    $("#create_event_form").submit();
  });

  const tIdSelect = document.getElementById("t_id");
  const searchForm = document.getElementById("search_form");

  const startDate = document.getElementById("start_date");
  const endDate = document.getElementById("end_date");
  const days = document.getElementById("days");
  let daysCount;

  const startTime = document.getElementById("start_time");
  const endTime = document.getElementById("end_time");
  const duration = document.getElementById("duration");
  let timeCount;

  tIdSelect.onchange = function () {
    searchForm.submit();
  };

  function calcDays() {
    const date1 = new Date(startDate.value);
    const date2 = new Date(endDate.value);

    daysCount = Math.ceil(Math.abs(date2 - date1) / (1000 * 60 * 60 * 24)) + 1;
    days.value = daysCount;
    if (date1 > date2) {
      days.value *= -1;
    }
  }

  function calcTime() {
    const time1 = startTime.value;
    const time2 = endTime.value;
    timeCount = Number(time2.split(":")[0]) - Number(time1.split(":")[0]);
    duration.value = timeCount;
  }

  startDate.onchange = function () {
    calcDays();
  };

  endDate.onchange = function () {
    calcDays();
  };

  startTime.onchange = function () {
    calcTime();
  };

  endTime.onchange = function () {
    calcTime();
  };
});
