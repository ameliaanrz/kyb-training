<?php
include_once __DIR__ . '/../partials/_header.php';
if (!isset($_SESSION['NPK']) || !isset($_SESSION['RLS_ID']) || (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] != 'RLS01')) {
  header("Location: /login.php");
}
?>

<h1 class="fs-2 fw-semibold">Edit <span id="main_title" class="fw-bold"></span> Matrix Chapter Content</h1>
<p>Administrator could add, update, or delete subchapters and core contents of this particular chapter including uploading files, paragraph, links, and lists.</p>
<hr>
<div class="d-flex justify-content-between align-items-center mb-4">
  <!-- breadcrumb nav -->
  <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb" class="d-block my-0 py-0">
    <ol class="breadcrumb my-0 py-0">
      <li class="breadcrumb-item"><a href="matrix.php" class="text-decoration-none">Matrix</a></li>
      <li class="breadcrumb-item active" aria-current="page">Matrix File</li>
    </ol>
  </nav>
  <button id="create_core_content_btn" data-bs-toggle="modal" data-bs-target="#crud_modal" type="button" class="btn create-core-content-btn btn-outline-success mb-3"><i class="fas fa-plus"></i> Upload File</button>
</div>
<div id="filter_form" class="form gap-1 d-flex">
      <select name="year" id="year_select" class="form-select">
        <option value="" disabled default selected>-- Filter by year --</option>
        <option value="">All</option>
      </select>
    </div>
<br>
<div id="core_content_container" class="d-none"></div>
<!-- loading spinner -->
<div id="loading_spinner" class="spinner-border text-primary d-block mx-auto my-5" role="status">
  <span class="sr-only">Loading...</span>
</div>
<!-- core content container -->

<!-- CRUD Modal -->
<div class="modal fade" id="crud_modal" tabindex="-1" aria-labelledby="crud_modal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="modal_header" class="modal-header">
        <h5 class="modal-title" id="modal_title"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div id="modal_body" class="modal-body">
        <!-- modal loading spinner -->
        <div id="modal_loading_spinner" class="spinner-border text-primary d-block mx-auto my-4" role="status">
          <span class="sr-only">Loading...</span>
        </div>
      </div>
      <div id="modal_footer" class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button id="modal_action_btn" type="button" class="btn"></button>
      </div>
    </div>
  </div>
</div>
<style>
  .form-check-input {
    background-color: red;
    border-color: red;
    padding-bottom: 2rem;
    padding-left: 3.25rem;
    position: relative;
  }

  .form-check-input:after {
    position: absolute;
    left: 50%;
    top: 5%;
    content: "no";
    color: white;
    font-size: 1rem;
  }

  .form-check-input:checked {
    background-color: green;
    border-color: green;
  }

  .form-check-input:checked:after {
    left: 20%;
    content: "yes";
  }

  .form-switch .form-check-input {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='white'/%3e%3c/svg%3e");
  }

  .form-switch .form-check-input:focus {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='white'/%3e%3c/svg%3e");
    -moz-box-shadow: none !important;
    -webkit-box-shadow: none !important;
    box-shadow: none !important;
  }
</style>

<!-- End CRUD modal -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript" defer>
  $(document).ready(function() {

    let filterYear = '';
    let html = ``;

    // content types
    const FILE_TYPE_PARAGRAPH = "1";
    const FILE_TYPE_IMAGE = "2";
    const FILE_TYPE_VIDEO = "3";
    const FILE_TYPE_PDF = "4";
    const FILE_TYPE_LINK = "5";
    const FILE_TYPE_UNORDERED_LIST = "6";
    const FILE_TYPE_ORDERED_LIST = "7";
    const FILE_TYPE_H1 = "8";
    const FILE_TYPE_H2 = "9";
    const FILE_TYPE_H3 = "10";

    // get url params
    const queryString = new URLSearchParams(window.location.search);
    const dpt_id = queryString.get('dpt_id');

    // get contents
    getContents();
    getYear();

    //filter by year 
    $("#year_select").change(function() {
      // set filter month to curr month_select value
      filterYear = this.value;

      console.log("Filter Year:", filterYear); // Cek apakah value terkirim

      // show events loading spinner
      $("#loading_spinner").removeClass('d-none').addClass('d-block');

      // hide table
      $("#core-content_container").addClass('d-none');

      // remove previous contents
      $("#core-content_container").html(html);

      // get events
      getContents();

      setTimeout(() => {
        // remove events loading spinner
        $("#loading_spinner").removeClass('d-block').addClass('d-none');

        // show table
        $("#core-content_container").removeClass('d-none');
      }, 500)
    })

    // show create core content handler
    $(".create-core-content-btn").click(function() {
      // set modal title
      $("#modal_title").html(`Create new Content`);

      // set modal body
      $("#modal_body").html(`
        <form enctype="multipart/form-data" action="includes/matrix.inc.php?type=MACT15&dpt_id=${dpt_id}" method="POST" id="core_content_form">
          <select id="file_type" name="file_type" class="form-select">
            <option value="" disabled selected>-- Select content type --</option>
            <option value="4">PDF</option>
          </select>
          <div id="content_container" class="mt-2"></div>
          <small id="content_error" class="text-danger"></small>
        </form>
      `);


      // input value variables
      let fileType = $("#file_type").val();
      let fileDoc = '';

      // core content type change listener
      $("#file_type").change(function() {
        fileType = this.value;
        let html = ``;
        switch (this.value) {
          case FILE_TYPE_IMAGE:
          case FILE_TYPE_PDF:
            html = `<input type="file" name="file" id="core_content_input" class="form-control" />`;
            break;
        }

        // set content container to html
        $("#content_container").html(html);
        // Initialize TinyMCE for paragraphs
        if (fileDoc['TYPE'] == FILE_TYPE_PARAGRAPH) {
          tinymce.init({
            selector: `#tiny_${fileDoc['MTX_ID']}`,
            height: 300,
            menubar: false,
            plugins: [
              'advlist autolink lists link image charmap print preview anchor',
              'searchreplace visualblocks code fullscreen',
              'insertdatetime media table paste code help wordcount'
            ],
            toolbar: 'undo redo | formatselect | bold italic backcolor | \
                        alignleft aligncenter alignright alignjustify | \
                        bullist numlist outdent indent | removeformat | help'
          });
        }

        // set input behavior of each FILE type
        if (fileType == FILE_TYPE_PARAGRAPH || fileType == FILE_TYPE_LINK) {
          $("#core_content_input").keyup(function() {
            fileDoc = this.value;
          })
        } else if (fileType == FILE_TYPE_ORDERED_LIST || fileType == FILE_TYPE_UNORDERED_LIST) {
          fileDoc = [];
          $("#add_core_content_input").click(function() {
            addList();
          })

          function addList() {
            const tmp = $("#core_content_input").val();
            fileDoc.push(tmp);
            $("#lists_input_container").append(`
              <span id="item_${tmp}" class="bg-light rounded-2 px-3 py-1">${tmp} <i id="remove_${tmp}" class="fas fa-times remove-list-btn" style="cursor: pointer"></i></span>
            `);
            $(".remove-list-btn").click(function() {
              const tmpId = this.id.split('_').pop();
              $(`#item_${tmpId}`).remove();
              fileDoc = fileDoc.filter(e => e != tmpId);
            })
            $("#core_content_input").val('');
          }
        }
      })

      // set modal action button
      $("#modal_action_btn").html(`<i class="fas fa-plus"></i> Create content`);
      $("#modal_action_btn").removeClass('btn-primary').removeClass("btn-danger").addClass("btn-success");

      // modal action button handler
      $("#modal_action_btn").click(function() {
        // create core content
        if (fileType == FILE_TYPE_IMAGE || fileType == FILE_TYPE_PDF) {
          $("#core_content_form").submit();
        } else {
          $.post(`includes/matrix.inc.php?type=MACT15&dpt_id=${dpt_id}`, {
            file_type: fileType,
            file: fileDoc
          }).done(function(a, b, xhr) {
            // console.log(a, b, xhr.status, xhr.responseJSON);
            // reload current page
            location.reload();
          }).fail(function(xhr, a, b) {
            console.log("Error fetching contents:", xhr.status, xhr.responseJSON);
            if (xhr.responseJSON) {
              const errors = xhr.responseJSON;
              $("#core_content_error").html(errors['file']);
            }
          })
        }
      })
    })

    function getYear(){
      $.get(`includes/matrix.inc.php?type=MACT013&dpt_id=${dpt_id}`).done(function(a, b, xhr) {
        if (xhr.responseJSON) {
          // set months
          const years = xhr.responseJSON['years'];
          console.log(years);
          $("#year_select").html(`
            <option value="" disabled default selected>-- Filter by year --</option>
            <option value="">All</option>
          `);
          if (years) {
            years.forEach(function(year) {
              $("#year_select").append(`
                <option value="${year['YEAR']}">
                  ${year['YEAR']}
                </option>
              `);
            })
          }
        }
      }).fail(function(xhr, a, b) {
        console.log(a, b, xhr.status, xhr.responseJSON);
      })
    }

    function getContents() {
      console.log("Fetching contents for year:", filterYear);
      // get training core content
      $.get(`includes/matrix.inc.php?type=MACT013&dpt_id=${dpt_id}&year=${filterYear}`).done(function(a, b, xhr) {
        console.log(xhr);
        // no content found
        if (xhr.status == 204) {
          // hide loading animation
          $("#loading_spinner").removeClass('d-block').addClass('d-none');
          $('#divtraincheck').html("");
          // show no contents found
          $("#core_content_container").html("<p>No training contents found</p>");
          $("#core_content_container").removeClass('d-none');
          // show core content container

          return;
        }

        if (xhr.responseJSON) {
          // get response datas
          const fileDocs = xhr.responseJSON['fileDocs'];
          console.log(fileDocs);

          if (Array.isArray(fileDocs) && fileDocs.length > 0) {
            $("#core_content_container").html('');
            
            // handle VR_STATUS
            fileDocs.forEach(function(fileDoc) {
              console.log(fileDoc);
              let html = ``;
              let lists = [];
              let fileType;
              console.log(typeof(fileDoc['TYPE']));
              switch (String(fileDoc['TYPE'])) {
                case FILE_TYPE_PDF:
                  html = `
                  <div class="mb-3 d-flex justify-content-right gap-2">
                    <h5 class="upload-date">Uploaded In: ${fileDoc['UPLOAD_YEAR']}</h2>
                  </div>
                  <embed src="public/imgs/uploads/${fileDoc['FILE']}" width="600px" height="500px" class="mb-2 d-block mx-auto" />
                  `;
                  html += `
                  <div class="mb-3 d-flex justify-content-center gap-2">
                    <button id="change_${fileDoc['MTX_ID']}" class="btn change-up btn-light"><i class="fas fa-arrow-up"></i></button>
                    <button id="change_${fileDoc['MTX_ID']}" class="btn change-down btn-light"><i class="fas fa-arrow-down"></i></button>
                    <button data-bs-toggle="modal" data-bs-target="#crud_modal" id="update_content_${fileDoc['MTX_ID']}" class="btn btn-light update-core-content-btn py-1 text-primary">Update</button>
                    <button data-bs-toggle="modal" data-bs-target="#crud_modal" id="delete_content_${fileDoc['MTX_ID']}" class="btn btn-light delete-core-content-btn py-1 text-danger">Delete</button>
                  </div>
                  <br><br>
                  `;
                  break;
              }

              // // change position handler
              // $(".change-up").click(function() {
              //   const itemId = this.id.split('_').pop();
              //   changePosition(itemId, 'up');
              // })

              // $('.change-down').click(function() {
              //   const itemId = this.id.split('_').pop();
              //   changePosition(itemId, 'down');
              // })

              // Change position handler using event delegation
              $("#core_content_container").on("click", ".change-up", function() {
                const itemId = this.id.split('_').pop();
                changePosition(itemId, 'up');
              });

              $("#core_content_container").on("click", ".change-down", function() {
                const itemId = this.id.split('_').pop();
                changePosition(itemId, 'down');
              });


              function changePosition(itemId, type) {
                $.post(`includes/matrix.inc.php?type=MACT20`, {
                  mtx_id: itemId,
                  type: type
                }).done(function(a, b, xhr) {
                  location.reload();
                }).fail(function(xhr, a, b) {
                  console.log(a, b, xhr.status, xhr.responseJSON);
                })
              }

              $('#switchbtn').click(function() {
                const vrstats = this.checked ? 'AKTIF' : 'TIDAK';
                $.post(`includes/matrix.inc.php?type=MACT21&dpt_id=${dpt_id}`, {
                  vrstats
                }).done(function(a, b, xhr) {
                  // reload current page {
                  location.reload();
                }).fail(function(xhr, a, b) {
                  console.log(a, b, xhr.status, xhr.responseJSON);
                })
              })

              // add core content
              $("#core_content_container").append(html);

              // show update core content handler
              $(".update-core-content-btn").click(function() {
                // get core content id
                const tmpMtxId = this.id.split("_").pop();

                // get core content type
                const tmpFileType = fileDocs.find(fileDoc => fileDoc['MTX_ID'] == tmpMtxId)['TYPE'];

                // get core content actual content
                const tmpFile = fileDocs.find(fileDoc => fileDoc['MTX_ID'] == tmpMtxId)['FILE'];

                // set modal title
                $("#modal_title").html(`Update Core Content <strong>${tmpMtxId}</strong>`);

                // set modal body
                $("#modal_body").html(`
                <form enctype="multipart/form-data" action="includes/matrix.inc.php?type=MACT16&mtx_id=${tmpMtxId}&dpt_id=${dpt_id}" method="POST" id="core_content_form">
                  <select id="file_type" name="file_type" class="form-select">
                    <option value="" disabled selected>-- Select content type --</option>
                    <option value="4" ${tmpFileType == 4? "selected": ""}>PDF</option>
                  </select>
                  <div id="content_container" class="mt-2"></div>
                  <small id="content_error" class="text-danger"></small>
                </form>
              `);

                // input value variables
                fileType = tmpFileType;
                let fileDoc;

                // set original content 
                if (tmpFileType == FILE_TYPE_ORDERED_LIST || tmpFileType == FILE_TYPE_UNORDERED_LIST) {
                  fileDoc = tmpFile.split(',');
                } else {
                  fileDoc = tmpFile;
                }

                // set content container
                setContentContainer(fileType);

                // core content type change listener
                $("#file_type").change(function() {
                  fileType = this.value;
                  setContentContainer(fileType);
                })

                function setContentContainer(fileType) {
                  // set content container
                  let html = ``;

                  // add title options if content type is 8, 9, 10
                  if (fileType == FILE_TYPE_H1 || fileType == FILE_TYPE_H2 || fileType == FILE_TYPE_H3) {
                    html = `
                    <select id="title_type" name="title_type" class="form-select mt-2">
                      <option value="" disabled selected>-- Select title size (default: XXL) --</option>
                      <option ${fileType == FILE_TYPE_H1? "selected": ""} value="8">XXL</option>
                      <option ${fileType == FILE_TYPE_H2? "selected": ""} value="9">XL</option>
                      <option ${fileType == FILE_TYPE_H3? "selected": ""} value="10">L</option>
                    </select>
                  `;
                  }

                  // set content input
                  switch (fileType) {
                    case FILE_TYPE_H1:
                    case FILE_TYPE_H2:
                    case FILE_TYPE_H3:
                      html += `
                        <input id="core_content_input" type="text" placeholder="Enter title here" class="form-control mt-2" value="${tmpFile}" />
                      `;
                      break;
                    case FILE_TYPE_PARAGRAPH:
                      html += `<textarea id="core_content_input" name="file" rows="6" class="form-control" placeholder="Enter paragraph here">${tmpFile}</textarea>`;
                      break;
                    case FILE_TYPE_IMAGE:
                      html += `<input type="file" name="file" id="core_content_input" class="form-control" />`;
                      break;
                    case FILE_TYPE_VIDEO:
                      html += `<input type="file" name="file" id="core_content_input" class="form-control" />`;
                      break;
                    case FILE_TYPE_PDF:
                      html += `<input type="file" name="file" id="core_content_input" class="form-control" />`;
                      break;
                    case FILE_TYPE_LINK:
                      html += `<input id="core_content_input" name="file" type="text" placeholder="Enter URL content here" class="form-control" value="${tmpFile}" />`;
                      break;
                    case FILE_TYPE_UNORDERED_LIST:
                    case FILE_TYPE_ORDERED_LIST:
                      html += `
                      <div class="input-group">
                        <input id="core_content_input" type="text" placeholder="Enter list item here" class="form-control" />
                        <button id="add_core_content_input" type="button" class="btn btn-secondary">Add</button>
                      </div>
                      `;
                      break;
                  }

                  // set content
                  switch (tmpFileType) {
                    case FILE_TYPE_H1:
                      html += `<h1 class='my-3'>${tmpFile}</h1>`;
                      break;
                    case FILE_TYPE_H2:
                      html += `<h2 class='my-3'>${tmpFile}</h2>`;
                      break;
                    case FILE_TYPE_H3:
                      html += `<h3 class='my-3'>${tmpFile}</h3>`;
                      break;
                    case FILE_TYPE_PARAGRAPH:
                      html += `<p class="my-3">${tmpFile}</p>`;
                      break;
                    case FILE_TYPE_LINK:
                      html += `<a href="${tmpFile}" target="_blank" class="my-3 d-block">${tmpFile}</a>`;
                      break;
                    case FILE_TYPE_IMAGE:
                      html += `<img style="width: 400px" class="d-block mx-auto my-3" src="public/imgs/uploads/${tmpFile}" />`;
                      break;
                    case FILE_TYPE_VIDEO:
                      html += `
                        <video width="400" class="d-block my-3 mx-auto mb-2" controls>
                          <source src="public/imgs/uploads/${tmpFile}" type="video/${tmpFile.split('.').pop()}">
                          <source src="movie.ogg" type="video/ogg">
                          Your browser does not support the video tag.
                        </video>
                      `;
                      break;
                    case FILE_TYPE_PDF:
                      html += `
                      <embed src="public/imgs/uploads/${tmpFile}" width="400px" height="300px" class="mb-2 d-block mx-auto my-3" />
                      `;
                      break;
                    case FILE_TYPE_UNORDERED_LIST:
                    case FILE_TYPE_ORDERED_LIST:
                      fileDoc = tmpFile.split(',');
                      html += `
                        <div id="lists_input_container" class="d-flex gap-2 mt-2 flex-wrap">
                      `;
                      fileDoc.forEach(function(item) {
                        html += `<span id="item_${item}" class="bg-light rounded-2 px-3 py-1">${item} <i id="remove_${item}" class="fas fa-times remove-list-btn" style="cursor: pointer"></i></span>`;
                      })
                      html += `</div>`
                      break;
                  }

                  // set content container to html
                  $("#content_container").html(html);

                  $("#title_type").change(function() {
                    console.log(this.value);

                    fileType = this.value;
                  });

                  // remove list from lists input container
                  $(".remove-list-btn").click(function() {
                    const tmpId = this.id.split('_').pop();
                    document.getElementById(`item_${tmpId}`).remove();
                    fileDoc = fileDoc.filter(e => e != tmpId);
                  })
                }

                // set input behavior of each content type
                if (fileType == FILE_TYPE_PARAGRAPH || fileType == FILE_TYPE_LINK || fileType == FILE_TYPE_H1 || fileType == FILE_TYPE_H2 || fileType == FILE_TYPE_H3) {
                  $("#core_content_input").keyup(function() {
                    fileDoc = this.value;
                  })
                } else if (fileType == FILE_TYPE_ORDERED_LIST || fileType == FILE_TYPE_UNORDERED_LIST) {
                  $("#add_core_content_input").click(function() {
                    addList();
                  })
                }

                function addList() {
                  const tmp = $("#core_content_input").val();
                  fileDoc.push(tmp);
                  $("#lists_input_container").append(`
                    <span id="item_${tmp}" class="bg-light rounded-2 px-3 py-1">${tmp} <i id="remove_${tmp}" class="fas fa-times remove-list-btn" style="cursor: pointer"></i></span>
                  `);
                  // remove list from lists input container
                  $(".remove-list-btn").click(function() {
                    const tmpId = this.id.split('_').pop();
                    document.getElementById(`item_${tmpId}`).remove();
                    fileDoc = fileDoc.filter(e => e != tmpId);
                  })
                  $("#core_content_input").val('');
                }

                // set modal action button
                $("#modal_action_btn").html(`<i class="fas fa-pencil"></i> Update content`);
                $("#modal_action_btn").removeClass('btn-success').removeClass("btn-danger").addClass("btn-primary");

                // modal action button handler
                $("#modal_action_btn").click(function() {
                  if (fileType !== FILE_TYPE_ORDERED_LIST && fileType !== FILE_TYPE_UNORDERED_LIST) {
                    // get core content
                    fileDoc = $("#core_content_input").val();
                  }

                  // create core content
                  if (fileType == FILE_TYPE_IMAGE || fileType == FILE_TYPE_VIDEO || fileType == FILE_TYPE_PDF) {
                    $("#core_content_form").submit();
                  } else {
                    console.log(fileDoc, fileType);

                    $.post(`includes/matrix.inc.php?type=MACT16&dpt_id=${dpt_id}&mtx_id=${tmpMtxId}`, {
                      file_type: fileType,
                      file: fileDoc
                    }).done(function(a, b, xhr) {
                      // reload current page
                      location.reload();
                    }).fail(function(xhr, a, b) {
                      console.log(xhr.status, xhr.responseJSON);
                      if (xhr.responseJSON) {
                        const errors = xhr.responseJSON;
                        $("#core_content_error").html(errors['file']);
                      }
                    })
                  }
                })
              })

              // show delete core content handler
              $(".delete-core-content-btn").click(function() {
                // get core content id
                const tmpMtxId = this.id.split('_').pop();

                // set modal title
                $("#modal_title").html(`Delete Training Core Content <strong>${tmpMtxId}</strong>`);

                // set modal body
                $("#modal_body").html(`
                <p>Are you sure to delete training core content <strong>${tmpMtxId}</strong>? This action is irreversible!</p>
              `);

                // set modal action button
                $("#modal_action_btn").html(`<i class="fas fa-trash"></i> Delete content`);
                $("#modal_action_btn").removeClass('btn-success').removeClass('btn-primary').addClass("btn-danger");

                // modal action button handler
                $("#modal_action_btn").click(function() {
                  // delete core content
                  $.get(`includes/matrix.inc.php?type=MACT14&mtx_id=${tmpMtxId}&dpt_id=${dpt_id}`).done(function(a, b, xhr) {
                    // reload current page
                    location.reload();
                  }).fail(function(xhr, a, b) {
                    console.log(xhr.status, xhr.responseJSON);
                  })
                })
              })

              // show create core content handler
              $(".create-core-content-btn").click(function() {
                // set modal title
                $("#modal_title").html(`Create new Content`);

                // set modal body
                $("#modal_body").html(`
                  <form enctype="multipart/form-data" action="includes/matrix.inc.php?type=MACT15&dpt_id=${dpt_id}" method="POST" id="core_content_form">
                    <select id="file_type" name="file_type" class="form-select">
                      <option value="" disabled selected>-- Select content type --</option>
                      <option value="4">PDF</option>
                    </select>
                    <div id="content_container" class="mt-2"></div>
                    <small id="content_error" class="text-danger"></small>
                  </form>
                `);

                // input value variables
                let fileType = $("#file_type").val();
                let fileDoc = '';

                // core content type change listener
                $("#file_type").change(function() {
                  fileType = this.value;
                  let html = ``;
                  switch (this.value) {
                    case FILE_TYPE_PARAGRAPH:
                      html = `<textarea id="core_content_input" name="file" rows="6" class="form-control" placeholder="Enter paragraph here"></textarea>`;
                      break;
                    case FILE_TYPE_IMAGE:
                    case FILE_TYPE_VIDEO:
                    case FILE_TYPE_PDF:
                      html = `<input type="file" name="file" id="core_content_input" class="form-control" accept="image/*,.pdf,video/*"/>`;
                      break;
                    case FILE_TYPE_LINK:
                      html = `<input id="core_content_input" name="file" type="text" placeholder="Enter URL FILE here" class="form-control" />`;
                      break;
                    case FILE_TYPE_UNORDERED_LIST:
                      html = `
                      <div class="input-group">
                        <input id="core_content_input" type="text" placeholder="Enter URL content here" class="form-control" />
                        <button id="add_core_content_input" type="button" class="btn btn-secondary">Add</button>
                      </div>
                      `;
                      html += `
                  <div id="lists_input_container" class="d-flex gap-2 mt-2 flex-wrap"></div>
                  `;
                      break;
                    case FILE_TYPE_ORDERED_LIST:
                      html = `
                          <div class="input-group">
                            <input id="core_content_input" type="text" placeholder="Enter URL content here" class="form-control" />
                            <button id="add_core_content_input" type="button" class="btn btn-secondary">Add</button>
                          </div>
                          `;
                      html += `
                        <div id="lists_input_container" class="d-flex gap-2 mt-2 flex-wrap"></div>
                      `;
                      break;
                    case FILE_TYPE_H1:
                      html = `
                        <select id="title_type" name="title_type" class="form-select">
                          <option value="" disabled selected>-- Select title size (default: XXL) --</option>
                          <option value="8">XXL</option>
                          <option value="9">XL</option>
                          <option value="10">L</option>
                        </select>
                      `;
                      html += `
                          <input id="core_content_input" type="text" placeholder="Enter title here" class="form-control mt-2" />
                      `;
                      html += `
                        <div id="lists_input_container" class="d-flex gap-2 mt-2 flex-wrap"></div>
                      `;
                      break;
                  }

                  // set content container to html
                  $("#content_container").html(html);

                  // set input behavior of each content type
                  if (fileType == FILE_TYPE_PARAGRAPH || fileType == FILE_TYPE_LINK) {
                    $("#core_content_input").keyup(function() {
                      fileDoc = this.value;
                    })
                  } else if (fileType == FILE_TYPE_ORDERED_LIST || fileType == FILE_TYPE_UNORDERED_LIST) {
                    fileDoc = [];
                    $("#add_core_content_input").click(function() {
                      addList();
                    })

                    function addList() {
                      const tmp = $("#core_content_input").val();
                      fileDoc.push(tmp);
                      $("#lists_input_container").append(`
                        <span id="item_${tmp}" class="bg-light rounded-2 px-3 py-1">${tmp} <i id="remove_${tmp}" class="fas fa-times remove-list-btn" style="cursor: pointer"></i></span>
                      `);
                      $(".remove-list-btn").click(function() {
                        const tmpId = this.id.split('_').pop();
                        $(`#item_${tmpId}`).remove();
                        fileDoc = fileDoc.filter(e => e != tmpId);
                      })
                      $("#core_content_input").val('');
                    }
                  } else if (fileType == FILE_TYPE_H1 || fileType == FILE_TYPE_H2 || fileType == FILE_TYPE_H3) {
                    $("#title_type").change(function() {
                      fileType = this.value;
                    });
                    $("#core_content_input").keyup(function() {
                      fileDoc = this.value;
                    });
                  }
                })

                // set modal action button
                $("#modal_action_btn").html(`<i class="fas fa-plus"></i> Create content`);
                $("#modal_action_btn").removeClass('btn-primary').removeClass("btn-danger").addClass("btn-success");

                // modal action button handler
                $("#modal_action_btn").click(function() {
                  // create core content
                  if (fileType == FILE_TYPE_IMAGE || fileType == FILE_TYPE_VIDEO || fileType == FILE_TYPE_PDF) {
                    $("#core_content_form").submit();
                  } else {
                    $.post(`includes/matrix.inc.php?type=MACT15&dpt_id=${dpt_id}`, {
                      file_type: fileType,
                      file: fileDoc
                    }).done(function(a, b, xhr) {
                      // reload current page
                      location.reload();
                    }).fail(function(xhr, a, b) {
                      console.log(a, b, xhr.status, xhr.responseJSON);
                      if (xhr.responseJSON) {
                        const errors = xhr.responseJSON;
                        $("#core_content_error").html(errors['file']);
                      }
                    })
                  }
                })
              })
            })
          }
        } else {
          console.error("No contents found or contents is undefined.");

          // hide loading animation
          $("#loading_spinner").removeClass('d-block').addClass('d-none');


          // show no contents found
          $("#core_content_container").html("<p>No training contents found</p>");

          //checkbox hide
          $('#divtraincheck').html("<p>No training contents found</p>");




          // show core content container
          $("#core_content_container").removeClass('d-none');

          return;
        }

        // remove loading animation
        $("#loading_spinner").removeClass('d-block').addClass('d-none');

        // show core content container
        $("#core_content_container").removeClass('d-none');
      }).fail(function(a, b, xhr) {
        console.log(a, b, xhr.status, xhr.responseJSON);
      })
    }
  })
</script>

<?php
include_once __DIR__ . '/../partials/_footer.php';
?>