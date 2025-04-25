<?php
include_once __DIR__ . '/partials/_header.php';
if (!isset($_SESSION['NPK'])) {
  include_once __DIR__ . '/components/home.php';
  exit();
}
?>

<?php if (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] == 'RLS02'||$_SESSION['RLS_ID'] == 'RLS03' || $_SESSION['RLS_ID'] == 'RLS04') : ?>
  <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
    <div id="imageSliderContainer" class="carousel-inner">
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
  </div>
<br>
    <section id="advanced-features">
      <div class="features-row section-bg">
        <div class="container">
          <div class="row">
            <div id="profile1" class="col-12">
              
            </div>
          </div>
        </div>
      </div>
      <br>
      <div class="features-row section-bg">
        <div class="container">
          <div class="row">
            <div id="profile2" class="col-12">
             
            </div>
          </div>
        </div>
      </div>
    </section><!-- #advanced-features -->
    <div id="loading_spinner_events" class="spinner-border text-primary d-block mx-auto mb-4 my-5" role="status">
      <span class="sr-only">Loading...</span>
    </div>
    <br>
    <div id="event_content">
      <h3 style="font-weight:bold">Upcoming Events</h3>
        <div id="event">
        <ul id="events_data_containers" class="d-none">
        </ul>
      </div>
    </div>
  </div>

<?php endif; ?>
<?php if (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] == 'RLS01') : ?>
<h1 class="fw-bold">PT. Kayaba Training Center Administrator Dashboard</h1>
<div class="d-flex justify-content-between">
  <a href="index.php#training_statistics" class="text-decoration-none btn btn-light"><i class="fas fa-circle-down"></i> Training Statistics</a>
</div>
<div class="card mt-4 py-3">
  <!-- <div class="card-header border-0 bg-white pb-0">
    <h3 class="fw-semibold">Statistics Filter Form</h3>
    <p>Filter statistics based on grade, gender, department, section, and subsection</p>
    <hr>
  </div> -->
  <div class="card-body pt-0 mt-2">
    <div id="query_spinner" class="spinner-border text-primary d-block mx-auto mb-4" role="status">
      <span class="sr-only">Loading...</span>
    </div>
    <form id="query_form" action="" method="GET" class="d-none">
      <div class="d-flex gap-3">
        <div style="width: 33%">
          <label for="dept_select">Department</label>
          <select name="department" id="dept_select" class="form-select">
          </select>
        </div>
        <div style="width: 33%">
          <label for="section_select">Section</label>
          <select name="section" id="section_select" class="form-select" value="<?php echo $section; ?>">
            <option disabled <?php echo !$section ? "selected" : ""; ?> selected value> -- Select section -- </option>
            <option value="">All</option>
            <?php foreach ($sections as $sec) : ?>
              <option value="<?php echo $sec['SEC_ID']; ?>" <?php echo $section == $sec['SEC_ID'] ? "selected" : ""; ?>><?php echo $sec['SECTION']; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div style="width: 33%">
          <label for="subsec_select">Subsection</label>
          <select name="subsection" id="subsec_select" class="form-select" value="<?php echo $subsection; ?>">
            <option disabled <?php echo !$subsection ? "selected" : ""; ?> selected value> -- Select subsection -- </option>
            <option value="">All</option>
            <?php foreach ($subsections as $subsec) : ?>
              <option value="<?php echo $subsec['SUB_SEC_ID']; ?>" <?php echo $subsection == $subsec['SUB_SEC_ID'] ? "selected" : ""; ?>><?php echo $subsec['SUBSECTION']; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="d-flex gap-3 mt-4">
        <div style="width: 50%">
          <label for="grade_select">Grade</label>
          <select name="grade" id="grade_select" class="form-select mt-2">
            <option disabled <?php echo !$grade ? "selected" : ""; ?> selected value> -- Select grade -- </option>
            <option value="">All</option>
            <?php foreach ($grades as $gr) : ?>
              <option value="<?php echo $gr['GRADE']; ?>" <?php echo $grade == $gr['GRADE'] ? "selected" : ""; ?>><?php echo $gr['GRADE']; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div style="width: 50%">
          <label for="gender_select">Gender</label>
          <select name="gender" id="gender_select" class="form-select mt-2">
            <option disabled <?php echo !$gender ? "selected" : ""; ?> selected value> -- Select gender -- </option>
            <option value="">All</option>
            <?php foreach ($genders as $gen) : ?>
              <option value="<?php echo $gen['GENDER']; ?>" <?php echo $gender == $gen['GENDER'] ? "selected" : ""; ?>><?php echo $gen['GENDER']; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
    </form>
  </div>
</div>
<div class="card mt-3 py-3" id="training_statistics">
  <!-- stats body -->
  <div id="loading_spinner" class="spinner-border text-primary d-block mx-auto my-4" role="status">
    <span class="sr-only">Loading...</span>
  </div>
  <div id="statistics_container" class="d-none">
    <div class="card-header border-0 bg-white pb-0">
      <h3 class="fw-semibold">KYB Training Center Training Statistics</h3>
      <!-- <p>Statistics of trainings held by PT. Kayaba Indonesia</p> -->
      <hr>
    </div>
    <div class="px-4 py-2 mt-2 card-body">
      <div class="d-flex flex-wrap" style="gap: 12px">
        <div id="trainings_total" class="card" style="width: calc(33% - 10px)">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <h2 class="fs-6 text-uppercase fw-semibold text-secondary">Trainings Total</h2>
                <!-- <p id="trainings_total_counter" class="fs-4 fw-bold mb-0" data-counter-target=""></p> -->
                <p id="trainings_total_counter" class="fs-4 fw-bold mb-0"></p>
              </div>
              <div class="rounded-circle d-flex justify-content-center align-items-center" style="height: 50px; width: 50px; background-color: #4D3C77">
                <i class="fas fs-4 fa-person-chalkboard text-white"></i>
              </div>
            </div>
            <small class="mt-2 mb-0 text-secondary">This month&apos;s total trainings</small>
          </div>
        </div>
        <div id="running_trainings" class="card" style="width: calc(33% - 10px)">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <h2 class="fs-6 text-uppercase fw-semibold text-secondary">Running Trainings</h2>
                <!-- <p id="running_trainings_counter" class="fs-4 fw-bold mb-0" data-counter-target=""></p> -->
                <p id="running_trainings_counter" class="fs-4 fw-bold mb-0"></p>
              </div>
              <div class="bg-danger rounded-circle d-flex justify-content-center align-items-center" style="height: 50px; width: 50px">
                <i class="fas fs-4 fa-arrow-trend-up text-white"></i>
              </div>
            </div>
            <small class="mt-2 mb-0 text-secondary">This month&apos;s total ongoing trainings</small>
          </div>
        </div>
        <div id="next_trainings" class="card" style="width: calc(33% - 10px)">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <h2 class="fs-6 text-uppercase fw-semibold text-secondary">Next Trainings</h2>
                <!-- <p id="next_trainings_counter" class="fs-4 fw-bold mb-0" data-counter-target=""></p> -->
                <p id="next_trainings_counter" class="fs-4 fw-bold mb-0"></p>
              </div>
              <div class="bg-primary rounded-circle d-flex justify-content-center align-items-center" style="height: 50px; width: 50px">
                <i class="fas fs-4 fa-arrow-right text-white"></i>
              </div>
            </div>
            <small class="mt-2 mb-0 text-secondary">This month&apos;s total upcoming trainings</small>
          </div>
        </div>
        <div id="completed_trainings" class="card" style="width: calc(33% - 10px)">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <h2 class="fs-6 text-uppercase fw-semibold text-secondary">Completed Trainings</h2>
                <!-- <p id="completed_trainings_counter" class="fs-4 fw-bold mb-0" data-counter-target=""></p> -->
                <p id="completed_trainings_counter" class="fs-4 fw-bold mb-0"></p>
              </div>
              <div class="bg-success rounded-circle d-flex justify-content-center align-items-center" style="height: 50px; width: 50px">
                <i class="fas fs-4 fa-check text-white"></i>
              </div>
            </div>
            <small class="mt-2 mb-0 text-secondary">This month&apos;s total completed trainings</small>
          </div>
        </div>
      </div>
    </div>
    <div class="card-header border-0 bg-white pb-0 mt-4">
      <h3 class="fw-semibold">KYB Training Center Participants Statistics</h3>
      <!-- <p>Statistics of training participants held by PT. Kayaba Indonesia</p> -->
      <hr>
    </div>
    <div class="px-4 py-2 mt-2 card-body">
      <div class="d-flex flex-wrap" style="gap: 12px">
        <div id="employees_total" class="card" style="width: calc(33% - 10px)">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <h2 class="fs-6 text-uppercase fw-semibold text-secondary">Employees Total</h2>
                <!-- <p id="employees_total_counter" class="fs-4 fw-bold mb-0" data-counter-target=""></p> -->
                <p id="employees_total_counter" class="fs-4 fw-bold mb-0"></p>
              </div>
              <div class="rounded-circle d-flex justify-content-center align-items-center" style="height: 50px; width: 50px; background-color: #1F6E8C">
                <i class="fas fs-4 fa-user text-white"></i>
              </div>
            </div>
            <small class="mt-2 mb-0 text-secondary">Total employees ever done trainings</small>
          </div>
        </div>
        <div id="female_participants" class="card" style="width: calc(33% - 10px)">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <h2 class="fs-6 text-uppercase fw-semibold text-secondary">Female Participants</h2>
                <!-- <p id="female_participants_counter" class="fs-4 fw-bold mb-0" data-counter-target=""></p> -->
                <p id="female_participants_counter" class="fs-4 fw-bold mb-0"></p>
              </div>
              <div class="rounded-circle d-flex justify-content-center align-items-center" style="height: 50px; width: 50px; background-color: #DA70D6;">
                <i class="fas fs-4 fa-venus text-white"></i>
              </div>
            </div>
            <small class="mt-2 mb-0 text-secondary">Female employees ever done trainings&apos; total</small>
          </div>
        </div>
        <div id="male_participants" class="card" style="width: calc(33% - 10px)">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <h2 class="fs-6 text-uppercase fw-semibold text-secondary">Male Participants</h2>
                <!-- <p id="male_participants_counter" class="fs-4 fw-bold mb-0" data-counter-target=""></p> -->
                <p id="male_participants_counter" class="fs-4 fw-bold mb-0"></p>
              </div>
              <div class="rounded-circle d-flex justify-content-center align-items-center" style="height: 50px; width: 50px; background-color: #7F00FF">
                <i class="fas fs-4 fa-mars text-white"></i>
              </div>
            </div>
            <small class="mt-2 mb-0 text-secondary">Male employees ever done trainings&apos; total</small>
          </div>
        </div>
        <div id="female_hours" class="card" style="width: calc(33% - 10px)">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <h2 class="fs-6 text-uppercase fw-semibold text-secondary">Female Hours</h2>
                <!-- <p id="female_hours_counter" class="fs-4 fw-bold mb-0" data-counter-target=""></p> -->
                <p id="female_hours_counter" class="fs-4 fw-bold mb-0"></p>
              </div>
              <div class="rounded-circle d-flex justify-content-center align-items-center" style="height: 50px; width: 50px; background-color: #DA70D6;">
                <i class="fas fs-4 fa-clock text-white"></i>
              </div>
            </div>
            <small class="mt-2 mb-0 text-secondary">Female participants&apos; total training hours</small>
          </div>
        </div>
        <div id="male_hours" class="card" style="width: calc(33% - 10px)">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <h2 class="fs-6 text-uppercase fw-semibold text-secondary">Male Hours</h2>
                <!-- <p id="male_hours_counter" class="fs-4 fw-bold mb-0" data-counter-target=""></p> -->
                <p id="male_hours_counter" class="fs-4 fw-bold mb-0"></p>
              </div>
              <div class="rounded-circle d-flex justify-content-center align-items-center" style="height: 50px; width: 50px; background-color: #7F00FF">
                <i class="fas fs-4 fa-clock text-white"></i>
              </div>
            </div>
            <small class="mt-2 mb-0 text-secondary">Male participants&apos; total training hours</small>
          </div>
        </div>
      </div>
    </div>
    <div class="w-full d-flex justify-content-end mt-4">
      <a id="print_statistics_report" href="includes/reports.inc.php?type=RACT01" class="text-decoration-none btn btn-primary w-25"><i class="fas fa-file"></i> Print statistics report</a>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- counter library import -->
<!-- <script src="public/js/lib/counter-master/counter.min.js" type="text/javascript"></script> -->
<!-- custom stats script -->

<?php if (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] == 'RLS02' || $_SESSION['RLS_ID'] == 'RLS03' || $_SESSION['RLS_ID'] == 'RLS04') : ?>
<script type="text/javascript" defer>
  $(document).ready(function() {
    let search = '';
    let filterMonth = '';

    getOverview();
    getEvents();

    function getOverview(){
      $.get(`includes/overview.inc.php?type=OVR01`).done(function(a, b, xhr) {
        if (xhr.responseJSON) {
          const overview = xhr.responseJSON['0'];
          let html = '';
          let html1 = '';
          let html2 = '';
          if (overview && overview['IMG_SLIDER']) {

          const images = overview['IMG_SLIDER'].split(',');
          let html = '';

          images.forEach((img, index) => {
              html += `<div class="carousel-item ${index === 0 ? 'active' : ''}">
                          <div class="blur-background" style="background-image: url('${img.trim()}');"></div>
                          <img src="${img.trim()}" alt="Slide ${index + 1}">
                      </div>`;
          });

          $("#imageSliderContainer").html(html);

      } else {
          let html = `<div class="carousel-item active">
                          <div class="blur-background" style="background-image: url('');"></div>
                          <img src="" alt="NOT FOUND">
                      </div>`;

          $("#imageSliderContainer").html(html);
      }

          if (overview && overview['IMG_PROFILE_1'] && overview['TITLE_PROFILE_1'] && overview['DESC_PROFILE_1']) {
            html1 += `
              <div class="profile-card" data-aos="fade-right">
                <div class="profile-content">
                  <h2 class="profile-title">${overview['TITLE_PROFILE_1']}</h2>
                  <p class="profile-desc">${overview['DESC_PROFILE_1']}</p>
                </div>
                <img class="profile-img" src="${overview['IMG_PROFILE_1']}" alt="">
              </div>
            `;
          } else {
            html1 = `
              <div class="profile-card" data-aos="fade-right">
                <div class="profile-content">
                  <h2 class="profile-title">NO CONTENT!!</h2>
                  <p class="profile-desc"></p>
                </div>
                <img class="profile-img" src="" alt="NOT FOUND">
              </div>
            `;
          }
          $("#profile1").html(html1);

          if (overview && overview['IMG_PROFILE_2'] && overview['TITLE_PROFILE_2'] && overview['DESC_PROFILE_2']) {
            html2 += `
              <div class="profile-card" data-aos="fade-left">
                <div class="profile-content">
                  <h2 class="profile-title">${overview['TITLE_PROFILE_2']}</h2>
                  <p class="profile-desc">${overview['DESC_PROFILE_2']}</p>
                </div>
                <img class="profile-img" src="${overview['IMG_PROFILE_2']}" alt="">
              </div>
            `;
          } else {
            html2 += `
              <div class="profile-card" data-aos="fade-left">
                <div class="profile-content">
                  <h2 class="profile-title">NO CONTENT!!</h2>
                  <p class="profile-desc"></p>
                </div>
                <img class="profile-img" src="" alt="NOT FOUND">
              </div>
            `;
          }
          $("#profile2").html(html2);
        }
      }).fail(function(xhr, a, b) {
        console.log(a, b, xhr.status, xhr.responseJSON);
      })
    }
    var sessionDPT_ID = "<?php echo $_SESSION['DPT_ID']; ?>";

    function getEvents() {
      $.get(`includes/events.inc.php?search=${search}&month=${filterMonth}`).done(function(a, b, xhr) {
        if (xhr.responseJSON) {
           const currentDate = new Date();
            const currentMonth = currentDate.getMonth() + 1; // Mengambil bulan saat ini (dimulai dari 0)
            const currentYear = currentDate.getFullYear(); // Mengambil tahun saat ini
            // Ambil data events dari respon
            const events = xhr.responseJSON['events'];

            // Filter events yang sesuai kriteria
            const filteredEvents = events.filter(data => {
                const eventStartDate = new Date(data['START_DATE']);
                const eventMonth = eventStartDate.getMonth() + 1;
                const eventYear = eventStartDate.getFullYear();

                return (
                    
                    eventStartDate > currentDate && 
                    eventMonth === currentMonth && 
                    eventYear === currentYear &&
                    data['ACTIVATED'] != 1
                );
            });

            let html = '';


            // Iterasi melalui events yang telah difilter
            filteredEvents.forEach(datas => {
                const evtToArray = datas['EVT_TO'].split(',');
                // Periksa apakah session DPT_ID ada dalam array evtToArray
                if (evtToArray.includes('all') || evtToArray.includes(sessionDPT_ID)) {
                  var purpose = datas['PURPOSE'];
                  // Cek panjang teks
                  if (purpose.length > 100) {
                      purpose = purpose.substring(0, 100) + '...';
                  }

                  // Buat elemen HTML untuk card
                  html += `
                      <div class="card">
                          <div class="card-body">
                              <h5 class="card-title" style="font-weight:bold">${datas['TRAINING']}</h5>
                              <p class="card-text">${purpose}</p>
                              <a class="btn btn-danger" href="events/trainingcontent.php?t_id=${datas['T_ID']}&evt_id=${datas['EVT_ID']}">Click For More Info!</a>
                          </div>
                      </div>
                  `;
                }
            });

            // Jika tidak ada events yang sesuai kriteria, tampilkan pesan "No events found"
            if (html === '') {
                html = `<div class="col-12 text-center "><p class="text-center">No events found</p></div>`;
            }
            $("#events_data_containers").html(html);
          } else {
            $("#events_data_containers").html(`<div class="col-12 text-center"><p class="text-center">No events found</p></div>`);
          }
          // show and hide html elements
          setTimeout(() => {
            // show events table and hide spinner
            $("#events_content").removeClass('d-none');
            $("#events_data_containers").removeClass('d-none');
            $("#loading_spinner_events").addClass('d-none').removeClass('d-block');
          $('#events_data_containers').lightSlider({
            // Konfigurasi lightslider
            item: 3, // Jumlah item yang ditampilkan
            loop: false, // Looping slide atau tidak
            slideMove: 1, // Jumlah slide yang digerakkan per klik
            slideMargin: 10, // Margin antar slide
            controls: true, // Tampilkan tombol kontrol
            pager: false // Sembunyikan pager
          });

          }, 500)
      }).fail(function(xhr, a, b) {
        console.log(a, b, xhr.status, xhr.responseJSON);
      })
    }
  });

</script>
<?php endif; ?>
<?php if (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] == 'RLS01') : ?>
<script type="text/javascript" defer>
  $(document).ready(function() {
    // filters
    let dpt_id = '';
    let sec_id = '';
    let sub_sec_id = '';
    let grade = '';
    let gender = '';
    let search = '';
    let filterMonth = '';

    
    getFilters();
    getStatistics();
    getEvents();

    function changePrintStatsHref() {
      console.log(dpt_id);
      $("#print_statistics_report").attr("href", `includes/reports.inc.php?type=RACT01&dpt_id=${dpt_id}&sec_id=${sec_id}&sub_sec_id=${sub_sec_id}&grade=${grade}&gender=${gender}`);
    }

    function getFilters() {
      $.get(`includes/statistics.inc.php?type=SACT02`).done(function(a, b, xhr) {
        if (xhr?.responseJSON) {
          console.log(xhr.responseJSON);
          // set queries
          const departments = xhr.responseJSON['departments'];
          const sections = xhr.responseJSON['sections'];
          const subsections = xhr.responseJSON['subsections'];
          const grades = xhr.responseJSON['grades'];
          const genders = xhr.responseJSON['genders'];

          changePrintStatsHref();

          $("#dept_select").html(`
            <option disabled selected value> -- Select department -- </option>
            <option value=""}>All</option>
          `);

          departments.forEach(item => {
            $("#dept_select").append(`
              <option value="${item['DPT_ID']}" ${dpt_id == item['DPT_ID']? "selected": ""}>${item['DEPARTMENT']}</option>
            `);
          });

          $("#dept_select").change(function() {
            dpt_id = this.value;

            $("#loading_spinner").removeClass('d-none').addClass('d-block');
            $("#statistics_container").addClass('d-none');

            changePrintStatsHref();

            getStatistics();
          })

          $("#section_select").html(`
            <option disabled selected value> -- Select section -- </option>
            <option value="">All</option>
          `);

          sections.forEach(item => {
            $("#section_select").append(`
              <option value="${item['SEC_ID']}">${item['SECTION']}</option>
            `);
          });

          $("#section_select").change(function() {
            sec_id = this.value;

            $("#loading_spinner").removeClass('d-none').addClass('d-block');
            $("#statistics_container").addClass('d-none');

            changePrintStatsHref();

            getStatistics();
          })

          $("#subsec_select").html(`
            <option disabled selected value> -- Select subsection -- </option>
            <option value="">All</option>
          `);

          subsections.forEach(item => {
            $("#subsec_select").append(`
              <option value="${item['SUB_SEC_ID']}">${item['SUBSECTION']}</option>
            `);
          });

          $("#subsec_select").change(function() {
            sub_sec_id = this.value;

            $("#loading_spinner").removeClass('d-none').addClass('d-block');
            $("#statistics_container").addClass('d-none');

            changePrintStatsHref();

            getStatistics();
          })

          $("#grade_select").html(`
            <option disabled selected value> -- Select grade -- </option>
            <option value="">All</option>
          `);

          grades.forEach(item => {
            $("#grade_select").append(`
              <option value="${item['GRADE']}">${item['GRADE']}</option>
            `);
          });

          $("#grade_select").change(function() {
            grade = this.value;

            $("#loading_spinner").removeClass('d-none').addClass('d-block');
            $("#statistics_container").addClass('d-none');

            changePrintStatsHref();

            getStatistics();
          })

          $("#gender_select").html(`
            <option disabled selected value> -- Select gender -- </option>
            <option value="">All</option>
          `);

          genders.forEach(item => {
            $("#gender_select").append(`
              <option value="${item['GENDER']}">${item['GENDER']}</option>
            `);
          });

          $("#gender_select").change(function() {
            gender = this.value;

            $("#loading_spinner").removeClass('d-none').addClass('d-block');
            $("#statistics_container").addClass('d-none');

            changePrintStatsHref();

            getStatistics();
          })

          setTimeout(() => {
            // unshow query spinner
            $("#query_spinner").removeClass('d-block').addClass('d-none');

            // show query_form
            $("#query_form").removeClass('d-none');
          }, 500);
        }
      }).fail(function(xhr, a, b) {
        console.log(a, b, xhr.status, xhr.responseJSON);
      })
    }

    function getStatistics() {
      $.get(`includes/statistics.inc.php?dpt_id=${dpt_id}&sec_id=${sec_id}&sub_sec_id=${sub_sec_id}&grade=${grade}&gender=${gender}`).done(function(a, b, xhr) {

        if (xhr.responseJSON) {
          const trainings_total = xhr.responseJSON['trainings_total'];
          const running_trainings = xhr.responseJSON['running_trainings'];
          const next_trainings = xhr.responseJSON['next_trainings'];
          const completed_trainings = xhr.responseJSON['completed_trainings'];
          const employees_total = xhr.responseJSON['employees_total'];
          const male_participants = xhr.responseJSON['male_participants'];
          const female_participants = xhr.responseJSON['female_participants'];
          const male_hours = xhr.responseJSON['male_hours'];
          const female_hours = xhr.responseJSON['female_hours'];

          $("#trainings_total_counter").html(trainings_total);
          $("#running_trainings_counter").html(running_trainings);
          $("#next_trainings_counter").html(next_trainings);
          $("#completed_trainings_counter").html(completed_trainings);
          $("#employees_total_counter").html(employees_total);
          $("#male_participants_counter").html(male_participants);
          $("#female_participants_counter").html(female_participants);
          $("#male_hours_counter").html(male_hours ?? 0);
          $("#female_hours_counter").html(female_hours);

          setTimeout(function() {
            $("#loading_spinner").removeClass('d-block').addClass('d-none');
            $("#statistics_container").removeClass('d-none');
          }, 500);
        }
      }).fail(function(xhr, a, b) {
        console.log(a, b, xhr.status, xhr.responseText);
      })
    }
  });
</script>
<?php endif; ?>
<?php
include_once __DIR__ . '/partials/_footer.php';
?>