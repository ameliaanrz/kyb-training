$(document).ready(function () {
  const startDate = $("#start_date");
  const endDate = $("#end_date");
  const days = $("#days");
  let daysCount;

  const startTime = $("#start_time");
  const endTime = $("#end_time");
  const duration = $("#duration");
  let timeCount;

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
});
