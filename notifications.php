<?php
include_once __DIR__ . '/partials/_header.php';
if (!isset($_SESSION['NPK'])
  // || !isset($_SESSION['RLS_ID']) || (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] != 'RLS01')
) {
  header("Location: login.php");
}
?>

<div class="d-flex justify-content-between align-items-center">
  <h1 class="fw-bold my-0">Notifications</h1>
  <button id="clear_notifs_btn" type="button" class="btn d-none btn-dark">Clear notifications</button>
</div>

<!-- notifications -->
<div id="notifications_container" class="mt-4 d-flex flex-column gap-1"></div>
<!-- loading animation -->
<div id="loading_spinner" class="spinner-border d-block mx-auto my-4" role="status">
  <span class="sr-only">Loading...</span>
</div>
<!-- show more button -->
<button id="show_more_btn" type="button" class="mt-4 bg-transparent mx-auto d-none border-0">Show more<br /><i class="fas fa-angle-down"></i></button>

<script type="text/javascript" defer>
  $(document).ready(function () {
  let showLimit = 5;
  let offset = 0;
RefreshBagdeNotif();
  getNotifications();

  // clear notifications button
  $("#clear_notifs_btn").click(function () {
    // show loading animation
    RefreshBagdeNotif();
    $("#loading_spinner").removeClass("d-none").addClass("d-block");
    // clear all notifications
    clearNotifications();

    // show "no notifications found"
    $("#notifications_container").html("<p>No notifications found</p>");
    $("#loading_spinner").removeClass("d-block").addClass("d-none");
    $("#clear_notifs_btn").removeClass("d-block").addClass("d-none");
    $("#show_more_btn").removeClass("d-block").addClass("d-none");
  });

  // show more button
  $("#show_more_btn").click(function () {
    // add showlimit and offset
    showLimit += 5;
    offset += 5;

    // show loading animation
    $("#loading_spinner").removeClass("d-none").addClass("d-block");

    // get notifications
    getNotifications();
  });

  function RefreshBagdeNotif(){
    $.get(`includes/notifications.inc.php?limit=${showLimit}&offset=${offset}`)
        .done(function (type, status, xhr) {
          console.log("Response received:", xhr.responseJSON);
          const resData = xhr.responseJSON || {};
          const notifsCount = resData.notifs_count || 0; //
          $('.notification').append(`<span class="badge">${notifsCount}</span>`);
        })
        .fail(function (xhr, type, status) {
          console.log(type, status, xhr.responseJSON, xhr.status);
        });
  }

  function clearNotifications() {
    $.get(`includes/notifications.inc.php?type=3`)
      .done(function (type, status, xhr) {
        RefreshBagdeNotif();
        location.reload();
            })
      .fail(function (xhr, type, status) {
        console.log(xhr.status, xhr.responseJSON, type, status);
      });
  }

  function getNotifications() {
  $.get(`includes/notifications.inc.php?limit=${showLimit}&offset=${offset}`)
    .done(function (type, status, xhr) {
      if (xhr.status === 204) {
        $("#notifications_container").html("<p>No notifications found</p>");
        $("#loading_spinner").removeClass("d-block").addClass("d-none");
        $("#clear_notifs_btn").removeClass("d-block").addClass("d-none");
        return;
      }
      const role = "<?php echo $_SESSION['RLS_ID']; ?>";

      console.log("Response received:", xhr.responseJSON);

      const resData = xhr.responseJSON || {};
      const notifs = resData.notifs || []; 
      const notifsCount = resData.notifs_count || 0; 

      $("#loading_spinner").removeClass("d-block").addClass("d-none");
      $("#clear_notifs_btn").removeClass("d-none").addClass("d-block");

      if (showLimit < notifsCount) {
        $("#show_more_btn").removeClass("d-none").addClass("d-block");
      } else {
        $("#show_more_btn").removeClass("d-block").addClass("d-none");
      }

      notifs.forEach(function (item) {
        const oks = ["NTFT03"];
        const isOk = oks.includes(item["NTF_T_ID"]);

        const html = `
          <div class="card position-relative" data-notif-id="${item["NTF_ID"]}">
            <div class="card-body d-flex gap-3 align-items-center">
              <div>
                <i class="fas ${isOk ? "fa-circle-check text-success" 
       : item["NTF_T_ID"] === 'NTFT05' ? "fa-times-circle text-danger" 
       : "fa-circle-exclamation text-warning"} fs-3"></i>
              </div>
              <div class='w-100'>
                <div class='d-flex justify-content-between align-items-center'>
                  <h5 class="my-0 d-block"><a href="${(role === "RLS02" || role === "RLS01")
    ? `events/approve.php?evt_id=${item["EVT_ID"]}`
    : `events/register.php?evt_id=${item["EVT_ID"]}`}" class="text-decoration-none text-primary">${
          item["NTF_TITLE"]
        }</a></h5>
                </div>
                <div class="row">
                  <div class="col-10">
                    <p class="mb-0 mt-1">${item["DESCRIPTION"]}</p>
                  </div>
                  <div class="col-2">
                    <p class='d-block my-0'>${item["CREATE_DATE"]}</p>
                  </div>
                </div>
              </div>
            </div>
            <button class="btn btn-sm btn-danger delete-notif-btn position-absolute top-0 end-0 m-2" style="font-size: 0.8rem; padding: 0.25rem 0.5rem;">&times;</button>
          </div>
        `;

        $("#notifications_container").append(html);
      });

      // Attach click event for delete buttons
      $(".delete-notif-btn").click(function () {
        const notifId = $(this).closest(".card").data("notif-id");
        deleteNotification(notifId);
        RefreshBagdeNotif();
      });
    })
    .fail(function (xhr, type, status) {
      console.log(type, status, xhr.responseJSON, xhr.status);
    });
}

function deleteNotification(notifId) {
  $.post(`includes/notifications.inc.php?type=9`, {ntf_id: notifId })
    .done(function (response) {
      $(`.card[data-notif-id="${notifId}"]`).remove();
    })
    .fail(function (xhr, type, status) {
      console.log(type, status, xhr.responseJSON, xhr.status);
    });
}

});

</script>

<?php
include_once __DIR__ . '/partials/_footer.php';
?>