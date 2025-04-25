<?php
include_once __DIR__ . '/../partials/_header.php';
if (!isset($_SESSION['NPK']) || !isset($_SESSION['RLS_ID'])) {
  header("Location: /login.php");
}
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <!-- breadcrumb nav -->
  <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb" class="d-block my-0 py-0">
    <?php if (!isset($_SESSION['RLS_ID']) || $_SESSION['RLS_ID'] != 'RLS04') : ?>
      <ol class="breadcrumb my-0 py-0">
        <li class="breadcrumb-item"><a href="eventusr.php" class="text-decoration-none">Events</a></li>
        <li class="breadcrumb-item active" aria-current="page">Event content</li>
      </ol>
    <?php endif; ?>
  </nav>
  <div id="btnRegist"></div>
</div>
<!-- loading spinner -->
<div id="loading_spinner" class="spinner-border text-primary d-block mx-auto my-5" role="status">
  <span class="sr-only">Loading...</span>
</div>
<!-- core content container -->
<div id="headingTrain">

</div>
<div id="content_container" class="d-none">

</div>
<div id="VR" class="fs-6 mb-3 d-none">
  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-headset-vr" viewBox="0 0 16 16">
    <path d="M8 1.248c1.857 0 3.526.641 4.65 1.794a5 5 0 0 1 2.518 1.09C13.907 1.482 11.295 0 8 0 4.75 0 2.12 1.48.844 4.122a5 5 0 0 1 2.289-1.047C4.236 1.872 5.974 1.248 8 1.248"></path>
    <path d="M12 12a4 4 0 0 1-2.786-1.13l-.002-.002a1.6 1.6 0 0 0-.276-.167A2.2 2.2 0 0 0 8 10.5c-.414 0-.729.103-.935.201a1.6 1.6 0 0 0-.277.167l-.002.002A4 4 0 1 1 4 4h8a4 4 0 0 1 0 8"></path>
  </svg>
  <a style="font-weight:bold">This Training Has VR Program</a>
</div>



<script type="text/javascript" defer>
  $(document).ready(function() {
    // content types
    const CONTENT_TYPE_PARAGRAPH = "1";
    const CONTENT_TYPE_IMAGE = "2";
    const CONTENT_TYPE_VIDEO = "3";
    const CONTENT_TYPE_PDF = "4";
    const CONTENT_TYPE_LINK = "5";
    const CONTENT_TYPE_UNORDERED_LIST = "6";
    const CONTENT_TYPE_ORDERED_LIST = "7";
    const CONTENT_TYPE_H1 = '8';
    const CONTENT_TYPE_H2 = '9';
    const CONTENT_TYPE_H3 = '10';

    // get url params
    const queryString = new URLSearchParams(window.location.search);
    const t_id = queryString.get('t_id');
    const evt_id = queryString.get('evt_id');
    // get contents
    getAllTrainings();
    getEvents();
    getContents();

    // Fungsi untuk mengonversi nama bulan dari angka ke nama bulan dalam bahasa Indonesia
    function getMonthName(monthIndex) {
      var months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
      return months[monthIndex];
    }

    // Fungsi untuk mengonversi nama hari dari angka ke nama hari dalam bahasa Indonesia
    function getDayName(dayIndex) {
      var days = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
      return days[dayIndex];
    }

    function getEvents() {
      $.get(`includes/events.inc.php?evt_id=${evt_id}`).done(function(a, b, xhr) {
        if (xhr.responseJSON) {
          const evt = xhr.responseJSON['event'];
          var startdate = new Date(evt['START_DATE']);
          var enddate = new Date(evt['END_DATE']);

          var dayname = getDayName(startdate.getDay());
          var date = startdate.getDate();
          var monthname = getMonthName(startdate.getMonth());
          var tahun = startdate.getFullYear();

          var dayname2 = getDayName(enddate.getDay());
          var date2 = enddate.getDate();
          var monthname2 = getMonthName(enddate.getMonth());
          var tahun2 = enddate.getFullYear();

          // Check if startdate and enddate are the same day
          if (startdate.getFullYear() === enddate.getFullYear() &&
            startdate.getMonth() === enddate.getMonth() &&
            startdate.getDate() === enddate.getDate()) {
            // If the startdate and enddate are the same day
            $("#headingTrain").append(`<p>Dilaksanakan Pada: <br> Hari/Tanggal: ${dayname} ${date} ${monthname} ${tahun}</p>`);
          } else {
            $("#headingTrain").append(`<p>Dilaksanakan Pada: <br> Hari/Tanggal: ${dayname} ${date} ${monthname} ${tahun} sampai dengan ${dayname2} ${date2} ${monthname2} ${tahun2}</p>`);
          }

          // Check if today is within the startdate or enddate or the day before startdate or enddate
          var today = new Date();
          <?php if (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] == 'RLS03') : ?>
            today.setHours(0, 0, 0, 0); // Set to midnight for accurate comparison
            if (startdate <= today) {
              $('#btnRegist').html(`<a class="btn btn-danger disabled" href="#">Register participants</a>`);
            } else if (evt['ACTIVATED'] != 1) {

            } else {
              $('#btnRegist').html(`<a class="btn btn-danger" href="events/register.php?evt_id=${evt_id}">Register participants</a>`);

            }
          <?php endif; ?>
        }
      })
    }

    function getAllTrainings() {
      // get training data
      $.get(`includes/trainings.inc.php?type=TACT01&t_id=${t_id}`).done(function(a, b, xhr) {
        getContents();
        const res = xhr.responseJSON;
        companyPurposes = res.company_purposes ? res.company_purposes.map(item => item.BENEFIT) : [];
        participantPurposes = res.participants_purposes ? res.participants_purposes.map(item => item.BENEFIT) : [];
        const training = res.training;
        console.log(training);
        $("#headingTrain").append(`<h1 style="font-weight:bold">${training['TRAINING']}</h1>`);

        // add training description
        $("#headingTrain").append(`<p>${training['DESCRIPTION']}</p>`);

        // add training description
        $("#headingTrain").append(`<p>${training['PURPOSE']}</p>`);

        // Menambahkan kontainer tujuan perusahaan
        if (companyPurposes && companyPurposes.length > 0) {
          $("#headingTrain").append(`
                <div id='companyPurposesContainer'>
                    <p>Tujuan yang ingin didapat dari perusahaan adalah sebagai berikut:</p>
                </div>
            `);

          // Memasukkan data tujuan perusahaan
          companyPurposes.forEach(function(purpose) {
            $("#companyPurposesContainer").append(`
                    <p>&nbsp;&nbsp;&nbsp;&nbsp;- ${purpose}</p>
                `);
          });
        }

        // Menambahkan kontainer tujuan peserta
        if (participantPurposes && participantPurposes.length > 0) {
          $("#headingTrain").append(`
                <div id='participantPurposesContainer'>
                    <p>Tujuan yang ingin didapat dari peserta adalah sebagai berikut:</p>
                </div>
            `);

          // Memasukkan data tujuan peserta
          participantPurposes.forEach(function(purpose) {
            $("#participantPurposesContainer").append(`
                    <p>&nbsp;&nbsp;&nbsp;&nbsp;- ${purpose}</p>
                `);
          });
        }

      })
    }

    function getContents() {
      // get training core content
      $.get(`includes/trainings.inc.php?type=TACT013&t_id=${t_id}`).done(function(a, b, xhr) {
        console.log("Response Status:", xhr.status);
        console.log("Response Data:", xhr.responseJSON);
        // no content found
        if (xhr.status === 204) {
          // hide loading animation
          $("#loading_spinner").removeClass('d-block').addClass('d-none');
          $('#divtraincheck').html("");
          // show no contents found
          $("#content_container").html("<p>No training contents found</p>");

          // show core content container
          $("#content_container").removeClass('d-none');

          return;
        }

        if (xhr.responseJSON) {
          // get response datas
          const contents = xhr.responseJSON;
          console.log(contents);
          let html = ``;
          if (contents?.length > 0 && contents[0]?.VR_STATUS === 'AKTIF') {
            $("#VR").removeClass("d-none");
          }

          contents.forEach(function(content) {
            console.log(content);
            let lists = [];
            let contentType;
            switch (String(content['CONTENT_TYPE'])) {
              case CONTENT_TYPE_PARAGRAPH:
                html += `
          <div class="mb-3">
            <p>${content['CONTENT']}</p>
          </div>
      `;
                break;
              case CONTENT_TYPE_LINK:
                html += `
          <div class="mb-3">
            <a href="${content['CONTENT']}" target="_blank" class="d-inline" style="text-decoration: none">${content['CONTENT']}</a>
          </div>
      `;
                break;
              case CONTENT_TYPE_H1:
                html += `
          <div class="mb-3">
            <h1>${content['CONTENT']}</h1>
          </div>
      `;
                break;
              case CONTENT_TYPE_H2:
                html += `
          <div class="mb-3">
            <h2>${content['CONTENT']}</h2>
          </div>
      `;
                break;
              case CONTENT_TYPE_H3:
                html += `
          <div class="mb-3">
            <h3>${content['CONTENT']}</h3>
          </div>
      `;
                break;
              case CONTENT_TYPE_IMAGE:
                html += `
            <div class="mb-3">
      <a href="public/imgs/uploads/${content['CONTENT']}" download class="d-flex align-items-center gap-2">
    <img src="public/imgs/img-icon.png" alt="Download Image" class="thumbnail" style="width: 30px; cursor: pointer;" />
    <span>${content['CONTENT']}</span>
</a>
</div>
  `;
                break;
              case CONTENT_TYPE_PDF:
                html += `
            <div class="mb-3">
      <a href="public/imgs/uploads/${content['CONTENT']}" download class="d-flex align-items-center gap-2">
    <img src="public/imgs/pdf-icon.png" alt="Download PDF" class="thumbnail" style="width: 30px; cursor: pointer;" />
    <span>${content['CONTENT']}</span>
</a>
</div>
  `;
                break;
            }
          });
          $("#content_container").html(html);

          // show no contents found
          $("#loading_spinner").removeClass('d-block').addClass('d-none');

          // show core content container
          $("#content_container").removeClass('d-none');

          return;
        } else {
          // hide loading animation
          $("#loading_spinner").removeClass('d-block').addClass('d-none');
          $("#content_container").html("<p>No training contents found</p>");
          $("#content_container").removeClass('d-none');
        }
      }).fail(function(a, b, xhr) {
        console.log(a, b, xhr.status, xhr.responseJSON);
      })
    }
  })
</script>

<?php
include_once __DIR__ . '/../partials/_footer.php';
?>