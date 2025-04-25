<?php
session_start(); // Pastikan session sudah dimulai
include_once __DIR__ . '/partials/_header.php';
if (!isset($_SESSION['NPK'])) {
  header("Location: login.php");
  exit();
}
error_log("NPK: " . $_SESSION['NPK']);
?>

<h1 class="fw-bold">PT. Kayaba Training Center Training Events</h1>
<div class="d-flex justify-content-between">
  <div>
    <br>
  </div>
</div>
<div id="filter_form" class="form gap-2 d-flex">
  <select name="month" id="month_select" class="form-select">
    <option value="" disabled default selected>-- Filter by month --</option>
    <option value="">All</option>
  </select>
  <select name="year" id="year_select" class="form-select">
    <option value="" disabled default selected>-- Filter by year --</option>
    <option value="">All</option>
  </select>
  <select name="completion" id="completion_select" class="form-select">
    <option disabled selected value> -- Completion status -- </option>
    <option value="">All</option>
    <option value="1">Not Completed</option>
    <option value="2">Completed</option>
  </select>
</div>
<hr>
<br>
<h2 id="currentMonthYearHeader" class="text-center fw-semibold  bg-danger text-white px-2"></h2>
<br>
<section id="events_data_containers" class="form gap-3 d-flex">

  <div id="list_spinner" class="spinner-border d-none text-primary mx-auto my-4" role="status">
    <span class="sr-only">Loading...</span>
  </div>

  <div class="d-flex justify-content-end mt-4">
    <div id="pagination_container"></div>
  </div>
</section>

<!-- require paginationjs -->
<script type="text/javascript" src="node_modules/paginationjs/dist/pagination.min.js"></script>

<!-- custom jquery script -->
<script type="text/javascript" defer>
  $(document).ready(function() {
    const currentDate = new Date();
    const currentMonth = currentDate.getMonth() + 1;
    const currentYear = currentDate.getFullYear();
    const defaultMonth = currentMonth;
    const defaultYear = currentYear;

    // get url params
    const npk = "<?php echo $_SESSION['NPK']; ?>";

    // variables
    let lists_shown = 10;
    let t_id_filter = '';
    let t_id = '';
    let filterMonth = defaultMonth; // Gunakan bulan saat ini
    let months = [];
    let years = [];
    let filterYear = defaultYear; // Gunakan tahun saat ini
    let org_id_filter = '';
    let start_date_filter = '';
    let end_date_filter = '';
    let approval_filter = '';
    let approval_dept_filter = '';
    let completion_filter = '';
    let search_filter = '';
    let colomIndex = '';
    let direction = '';

    const month_names = {
      1: "Januari",
      2: "Februari",
      3: "Maret",
      4: "April",
      5: "Mei",
      6: "Juni",
      7: "Juli",
      8: "Agustus",
      9: "September",
      10: "Oktober",
      11: "November",
      12: "Desember"
    }

    const defaultMonthName = month_names[defaultMonth];
    $("#currentMonthYearHeader").text(`${defaultMonthName} ${defaultYear}`);

    getFilterDetails();
    getMonths();
    getYear();
    getTrainings();

    // listen to lists shown
    $("#lists_shown").change(function() {
      lists_shown = this.value;
      getTrainings();
    })

    $('th i').on('click', function() {
      const index = $(this).attr('id').split('-')[2];
      if (colomIndex === index) {
        if (direction === 'asc') {
          direction = 'desc';
        } else if (direction === 'desc') {
          direction = '';
        } else {
          direction = 'asc';
        }
      } else {
        colomIndex = index;
        direction = 'asc';
      }

      // Update icon class to reflect sorting direction
      $('th i').removeClass('fa-sort-up fa-sort-down').addClass('fa-sort'); // Reset all icons
      if (direction === 'asc') {
        $(this).removeClass('fa-sort').addClass('fa-sort-up');
      } else if (direction === 'desc') {
        $(this).removeClass('fa-sort').addClass('fa-sort-down');
      }

      setTimeout(function() {
        getTrainings();
      }, 1000);

      // show loading spinner and hide events list
      $("#loading_spinner").addClass('d-block').removeClass('d-none');
      $("#table").addClass('d-none');
    });

    $("#month_select").change(function() {
      filterMonth = this.value;
      const selectedMonthName = month_names[filterMonth] || defaultMonthName; // Gunakan default jika tidak ada pilihan
      $("#currentMonthYearHeader").text(`${selectedMonthName} ${filterYear}`);
      getTrainings();
    });

    $("#year_select").change(function() {
      filterYear = this.value;
      const selectedMonthName = month_names[filterMonth] || defaultMonthName; // Gunakan default jika tidak ada pilihan
      $("#currentMonthYearHeader").text(`${selectedMonthName} ${filterYear}`);
      getTrainings();
    });

    $("#completion_select").change(function() {
      completion_filter = this.value;
      getTrainings();
    });

    function getMonths() {
      const monthSelect = document.getElementById('month_select');
      for (let i = 1; i <= 12; i++) {
        const option = document.createElement('option');
        option.value = i;
        option.text = month_names[i];
        monthSelect.appendChild(option);
      }
    }

    function getYear() {
      $.get(`includes/users.inc.php?type=TACT07`).done(function(a, b, xhr) {
        if (xhr.responseJSON) {
          const years = xhr.responseJSON;
          const yearSelect = document.getElementById('year_select');
          yearSelect.innerHTML = `
        <option value="" disabled default selected>-- Filter by year --</option>
        <option value="">All</option>
      `;
          if (years && years.length > 0) {
            years.forEach(function(year) {
              yearSelect.innerHTML += `
            <option value="${year.year}">${year.year}</option>
          `;
            });
          }
        }
      }).fail(function(xhr, a, b) {
        console.log(a, b, xhr.status, xhr.responseJSON);
      });
    }

    function getTrainings() {
      // Show spinner and hide events
      $("#list_spinner").removeClass('d-none').addClass('d-block');
      $("#events_data_containers").addClass('d-none'); // Assuming you have a div with id 'trainings_list' for the cards

      // Get training events
      $.get(`includes/users.inc.php?type=TACT05&npk=${npk}&t_id=${t_id}&org_id=${org_id_filter}&start_date=${start_date_filter}&end_date=${end_date_filter}&approved=${approval_filter}&approved_dept=${approval_dept_filter}&completed=${completion_filter}&search=${search_filter}&colomIndex=${colomIndex}&direction=${direction}&filterMonth=${filterMonth}&filterYear=${filterYear}`).done(function(a, b, xhr) {
        if (xhr.status == 204) {
          $("#events_data_containers").html(`
                <div class="col-12 text-center">
                <p>No events found</p>
                </div>
            `);
        } else {
          if (xhr.responseJSON) {
            const events = xhr.responseJSON;
            const trainings = xhr.responseJSON;

            let cardsHTML = '';

            events.forEach(function(item) {
              console.log(item); // Tambahkan ini untuk memeriksa data
              const status = getStatus(item['START_DATE'], item['END_DATE']);
              const formattedStartDate = formatDate(item['START_DATE']);
              const formattedEndDate = formatDate(item['END_DATE']);
              cardsHTML += `
                                <div class="event-card">
                                    <div class="card-title">${item['TRAINING']}</div>
                                    <p class="card-text">${item['PURPOSE'].substring(0, 100)}...</p>
                                    <p class="card-text">Status: ${status}</p>
                                    <a class="btn-event btn-danger" href="events/trainingcontent.php?t_id=${item['T_ID']}&evt_id=${item['EVT_ID']}" class="btn btn-primary">View More</a>
                                </div>
                    `;
            });

            $("#events_data_containers").html(cardsHTML);
          }
        }

        // Remove spinner and show events
        setTimeout(function() {
          $("#list_spinner").removeClass('d-block').addClass('d-none');
          $("#events_data_containers").removeClass('d-none');
        }, 500);
      }).fail(function(xhr, a, b) {
        console.log(a, b, xhr.status, xhr.responseJSON);
      }).always(function() {
        // Remove spinner and show events
        $("#list_spinner").removeClass('d-block').addClass('d-none');
        $("#events_data_containers").removeClass('d-none');
      });
    }

    function formatDate(dateString) {
      if (!dateString) return '';
      const [year, month, day] = dateString.split('-');
      return `${day}-${month}-${year}`;
    }

    function getStatus(startDate, endDate) {
      const now = new Date();
      startDate = new Date(startDate);
      endDate = new Date(endDate);

      if (now < startDate) {
        return "<span class='text-primary'>Upcoming</span>";
      } else if (now >= startDate && now <= endDate) {
        return "<span class='text-warning'>In Progress</span>";
      } else {
        return "<span class='text-success'>Completed</span>";
      }
    }

    function getFilterDetails() {
      $.get(`includes/users.inc.php?type=TACT04&npk=${npk}&t_id=${t_id_filter}&org_id=${org_id_filter}`).done(function(a, b, xhr) {
        if (xhr.responseJSON) {
          const trainings = xhr.responseJSON['trainings'];
          const organizers = xhr.responseJSON['organizers'];

          $("#training_select").html(`
            <option selected value>-- Select training --</option>
            <option value="">All</option>
          `);

          trainings.forEach(function(training) {
            $("#training_select").append(`
              <option value="${training['T_ID']}" ${t_id_filter == training['T_ID']? "selected": ""}>${training['TRAINING']}</option>
            `);
          })

          $("#organizer_select").html(`
            <option selected value>-- Select organizer --</option>
            <option value="">All</option>
          `);

          organizers.forEach(function(organizer) {
            $("#organizer_select").append(`
              <option value="${organizer['ORG_ID']}" ${org_id_filter == organizer['ORG_ID']? "selected": ""}>${organizer['ORGANIZER']}</option>
            `);
          })

          // change listener
          $("#training_select").change(function() {
            t_id_filter = this.value;


            // show list spinner and hide training list
            $("#list_spinner").removeClass('d-none').addClass('d-block');
            $("#training_list").addClass('d-none');

            changePrintHref();

            // re-get trainings
            getTrainings();
            getFilterDetails();
          })

          $("#organizer_select").change(function() {
            org_id_filter = this.value;

            // show list spinner and hide training list
            $("#list_spinner").removeClass('d-none').addClass('d-block');
            $("#training_list").addClass('d-none');

            changePrintHref();

            // re-get trainings
            getTrainings();
            getFilterDetails();
          })

          $("#start_date_input").change(function() {
            start_date_filter = this.value;

            setTimeout(function() {
              // show list spinner and hide training list
              $("#list_spinner").removeClass('d-none').addClass('d-block');
              $("#training_list").addClass('d-none');

              changePrintHref();

              // re-get trainings
              getTrainings();
            }, 1000);
          })

          $("#end_date_input").change(function() {
            end_date_filter = this.value;

            setTimeout(function() {
              // show list spinner and hide training list
              $("#list_spinner").removeClass('d-none').addClass('d-block');
              $("#training_list").addClass('d-none');

              changePrintHref();

              // re-get trainings
              getTrainings();
            }, 1000);
          })



          $("#completion_select").change(function() {
            completion_filter = this.value;

            // show list spinner and hide training list
            $("#list_spinner").removeClass('d-none').addClass('d-block');
            $("#training_list").addClass('d-none');

            // re-get trainings
            getTrainings();
          })

          $("#search_input").keyup(function() {
            search_filter = this.value;

            setTimeout(function() {
              // show list spinner and hide training list
              $("#list_spinner").removeClass('d-none').addClass('d-block');
              $("#training_list").addClass('d-none');

              // re-get trainings
              getTrainings();
            }, 1000);
          })

          // remove spiner and show filters
          setTimeout(function() {
            $("#filter_container").removeClass('d-none');
            $("#filter_spinner").removeClass('d-block').addClass('d-none');
          }, 500);
        }
      }).fail(function(xhr, a, b) {
        console.log(a, b, xhr.status, xhr.responseJSON);
      })
    }
  })
</script>

<?php
include_once __DIR__ . '/partials/_footer.php';
?>