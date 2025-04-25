<?php
include_once __DIR__ . '/../partials/_header.php';
if (!isset($_SESSION['NPK']) || !isset($_SESSION['RLS_ID']) || (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] != 'RLS01')) {
  header("Location: /login.php");
}
?>

<h1 class="fs-2 fw-semibold">Edit <span id="main_title" class="fw-bold"></span> Training Chapter Content</h1>
<p>Administrator could add, update, or delete subchapters and core contents of this particular chapter including uploading files, paragraph, links, and lists.</p>
<hr>
<div class="d-flex justify-content-between align-items-center mb-4">
  <!-- breadcrumb nav -->
  <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb" class="d-block my-0 py-0">
    <ol class="breadcrumb my-0 py-0">
      <li class="breadcrumb-item"><a href="trainings.php" class="text-decoration-none">Trainings</a></li>
      <li class="breadcrumb-item active" aria-current="page">Training content</li>
    </ol>
  </nav>

  <button id="create_core_content_btn" data-bs-toggle="modal" data-bs-target="#crud_modal" type="button" class="btn create-core-content-btn btn-outline-success mb-3"><i class="fas fa-plus"></i> Create core content</button>
</div>
<div id="divtraincheck" class="d-block">
  <p>This training has a VR program?</p>
  <div class="form-check form-switch d-flex justify-content  mb-2">
    <input class="form-check-input" type="checkbox" id="switchbtn">
  </div>
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
    const CONTENT_TYPE_H1 = "8";
    const CONTENT_TYPE_H2 = "9";
    const CONTENT_TYPE_H3 = "10";

    // get url params
    const queryString = new URLSearchParams(window.location.search);
    const t_id = queryString.get('t_id');

    // get contents
    getContents();

    // show create core content handler
    $(".create-core-content-btn").click(function() {
      // set modal title
      $("#modal_title").html(`Create new Content`);

      // set modal body
      $("#modal_body").html(`
        <form enctype="multipart/form-data" action="includes/trainings.inc.php?type=TACT15&t_id=${t_id}" method="POST" id="core_content_form">
          <select id="content_type" name="content_type" class="form-select">
            <option value="" disabled selected>-- Select content type --</option>
            <option value="1">Paragraph</option>
            <option value="2">Image</option>
            <option value="3">Video</option>
            <option value="4">PDF</option>
            <option value="5">Link</option>
            <option value="6">Unordered List</option>
            <option value="7">Ordered List</option>
            <option value="">Title</option>
          </select>
          <div id="content_container" class="mt-2"></div>
          <small id="content_error" class="text-danger"></small>
        </form>
      `);


      // input value variables
      let contentType = $("#content_type").val();
      let content = '';

      // core content type change listener
      $("#content_type").change(function() {
        contentType = this.value;
        let html = ``;
        switch (this.value) {
          case CONTENT_TYPE_PARAGRAPH:
            html = `<textarea id="core_content_input" name="core_content" rows="6" class="form-control" placeholder="Enter paragraph here"></textarea>`;
            break;
          case CONTENT_TYPE_IMAGE:
          case CONTENT_TYPE_VIDEO:
          case CONTENT_TYPE_PDF:
            html = `<input type="file" name="core_content" id="core_content_input" class="form-control" />`;
            break;
          case CONTENT_TYPE_LINK:
            html = `<input id="core_content_input" name="core_content" type="text" placeholder="Enter URL content here" class="form-control" />`;
            break;
          case CONTENT_TYPE_UNORDERED_LIST:
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
          case CONTENT_TYPE_ORDERED_LIST:
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
        }

        // set content container to html
        $("#content_container").html(html);
        // Initialize TinyMCE for paragraphs
        if (content['CONTENT_TYPE'] == CONTENT_TYPE_PARAGRAPH) {
          tinymce.init({
            selector: `#tiny_${content['TCC_ID']}`,
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

        // set input behavior of each content type
        if (contentType == CONTENT_TYPE_PARAGRAPH || contentType == CONTENT_TYPE_LINK) {
          $("#core_content_input").keyup(function() {
            content = this.value;
          })
        } else if (contentType == CONTENT_TYPE_ORDERED_LIST || contentType == CONTENT_TYPE_UNORDERED_LIST) {
          content = [];
          $("#add_core_content_input").click(function() {
            addList();
          })

          function addList() {
            const tmp = $("#core_content_input").val();
            content.push(tmp);
            $("#lists_input_container").append(`
              <span id="item_${tmp}" class="bg-light rounded-2 px-3 py-1">${tmp} <i id="remove_${tmp}" class="fas fa-times remove-list-btn" style="cursor: pointer"></i></span>
            `);
            $(".remove-list-btn").click(function() {
              const tmpId = this.id.split('_').pop();
              $(`#item_${tmpId}`).remove();
              content = content.filter(e => e != tmpId);
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
        if (contentType == CONTENT_TYPE_IMAGE || contentType == CONTENT_TYPE_VIDEO || contentType == CONTENT_TYPE_PDF) {
          $("#core_content_form").submit();
        } else {
          $.post(`includes/trainings.inc.php?type=TACT15&t_id=${t_id}`, {
            content_type: contentType,
            core_content: content
          }).done(function(a, b, xhr) {
            // console.log(a, b, xhr.status, xhr.responseJSON);
            // reload current page
            location.reload();
          }).fail(function(xhr, a, b) {
            console.log("Error fetching contents:", xhr.status, xhr.responseJSON);
            if (xhr.responseJSON) {
              const errors = xhr.responseJSON;
              $("#core_content_error").html(errors['core_content']);
            }
          })
        }
      })
    })

    function getContents() {
      // get training core content
      $.get(`includes/trainings.inc.php?type=TACT013&t_id=${t_id}`).done(function(a, b, xhr) {
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
          const contents = xhr.responseJSON;
          console.log(contents);

          if (Array.isArray(contents) && contents.length > 0) {
            // handle VR_STATUS
            if (contents[0].hasOwnProperty("VR_STATUS")) {
              if (contents[0]["VR_STATUS"] == "AKTIF") {
                $("#switchbtn").prop("checked", true);
              } else if (contents[0]["VR_STATUS"] == "TIDAK") {
                $("#switchbtn").prop("checked", false);
              }
            } else {
              console.warn("VR_STATUS property is missing in the first content object.");
            }
            contents.forEach(function(content) {
              console.log(content);
              let html = ``;
              let lists = [];
              let contentType;
              console.log(typeof(content['CONTENT_TYPE']));
              switch (String(content['CONTENT_TYPE'])) {
                case CONTENT_TYPE_H1:
                  html = `
                      <div class="mb-3">
                        <h1>${content['CONTENT']}</h1>
                        <div class="d-inline">
                          <button id="change_${content['TCC_ID']}" class="btn change-up btn-light"><i class="fas fa-arrow-up"></i></button>
                          <button id="change_${content['TCC_ID']}" class="btn change-down btn-light"><i class="fas fa-arrow-down"></i></button>
                          <button id="update_content_${content['TCC_ID']}" data-bs-toggle="modal" data-bs-target="#crud_modal" class="btn update-core-content-btn btn-light py-1 text-primary">Update</button> 
                          <button id="delete_content_${content['TCC_ID']}" data-bs-toggle="modal" data-bs-target="#crud_modal" class="btn delete-core-content-btn btn-light py-1 text-danger">Delete</button>
                        </div>
                      </div>
                  `;
                  break;
                case CONTENT_TYPE_H2:
                  html = `
                      <div class="mb-3">
                        <h2>${content['CONTENT']}</h2>
                        <div class="d-inline">
                          <button id="change_${content['TCC_ID']}" class="btn change-up btn-light"><i class="fas fa-arrow-up"></i></button>
                          <button id="change_${content['TCC_ID']}" class="btn change-down btn-light"><i class="fas fa-arrow-down"></i></button>
                          <button id="update_content_${content['TCC_ID']}" data-bs-toggle="modal" data-bs-target="#crud_modal" class="btn update-core-content-btn btn-light py-1 text-primary">Update</button> 
                          <button id="delete_content_${content['TCC_ID']}" data-bs-toggle="modal" data-bs-target="#crud_modal" class="btn delete-core-content-btn btn-light py-1 text-danger">Delete</button>
                        </div>
                      </div>
                  `;
                  break;
                case CONTENT_TYPE_H3:
                  html = `
                      <div class="mb-3">
                        <h3>${content['CONTENT']}</h3>
                        <div class="d-inline">
                          <button id="change_${content['TCC_ID']}" class="btn change-up btn-light"><i class="fas fa-arrow-up"></i></button>
                          <button id="change_${content['TCC_ID']}" class="btn change-down btn-light"><i class="fas fa-arrow-down"></i></button>
                          <button id="update_content_${content['TCC_ID']}" data-bs-toggle="modal" data-bs-target="#crud_modal" class="btn update-core-content-btn btn-light py-1 text-primary">Update</button> 
                          <button id="delete_content_${content['TCC_ID']}" data-bs-toggle="modal" data-bs-target="#crud_modal" class="btn delete-core-content-btn btn-light py-1 text-danger">Delete</button>
                        </div>
                      </div>
                  `;
                  break;
                case CONTENT_TYPE_PARAGRAPH:
                  html = `
                      <div class="mb-3">
                        <p>${content['CONTENT']}</p>
                        <div class="d-inline">
                          <button id="change_${content['TCC_ID']}" class="btn change-up btn-light"><i class="fas fa-arrow-up"></i></button>
                          <button id="change_${content['TCC_ID']}" class="btn change-down btn-light"><i class="fas fa-arrow-down"></i></button>
                          <button id="update_content_${content['TCC_ID']}" data-bs-toggle="modal" data-bs-target="#crud_modal" class="btn update-core-content-btn btn-light py-1 text-primary">Update</button> 
                          <button id="delete_content_${content['TCC_ID']}" data-bs-toggle="modal" data-bs-target="#crud_modal" class="btn delete-core-content-btn btn-light py-1 text-danger">Delete</button>
                        </div>
                      </div>
                      `;

                  break;
                case CONTENT_TYPE_IMAGE:
                  html = `
                      <img src="public/imgs/uploads/${content['CONTENT']}" alt="" class="d-block mx-auto mb-2" style="width: 600px" />
                      <div class="d-flex justify-content-center gap-2 mb-3">
                        <button id="change_${content['TCC_ID']}" class="btn change-up btn-light"><i class="fas fa-arrow-up"></i></button>
                        <button id="change_${content['TCC_ID']}" class="btn change-down btn-light"><i class="fas fa-arrow-down"></i></button>
                        <button data-bs-toggle="modal" data-bs-target="#crud_modal" id="update_content_${content['TCC_ID']}" class="btn btn-light update-core-content-btn py-1 text-primary">Update</button> 
                        <button data-bs-toggle="modal" data-bs-target="#crud_modal" id="delete_content_${content['TCC_ID']}" class="btn btn-light delete-core-content-btn py-1 text-danger">Delete</button>
                      </div>
                      `;
                  break;
                case CONTENT_TYPE_VIDEO:
                  const ext = content['CONTENT'].split('.').pop();
                  html = `
                  <video width="600" class="d-block mx-auto mb-2" controls>
                    <source src="public/imgs/uploads/${content['CONTENT']}" type="video/${ext}">
                    <source src="movie.ogg" type="video/ogg">
                    Your browser does not support the video tag.
                  </video>
                  <div class="mb-2 d-flex justify-content-center gap-2">
                    <button id="change_${content['TCC_ID']}" class="btn change-up btn-light"><i class="fas fa-arrow-up"></i></button>
                    <button id="change_${content['TCC_ID']}" class="btn change-down btn-light"><i class="fas fa-arrow-down"></i></button>
                    <button data-bs-toggle="modal" data-bs-target="#crud_modal" id="update_content_${content['TCC_ID']}" class="btn btn-light update-core-content-btn py-1 text-primary">Update</button> 
                    <button data-bs-toggle="modal" data-bs-target="#crud_modal" id="delete_content_${content['TCC_ID']}" class="btn btn-light delete-core-content-btn py-1 text-danger">Delete</button>
                  </div>
                  `;
                  break;
                case CONTENT_TYPE_PDF:
                  html = `
                  <embed src="public/imgs/uploads/${content['CONTENT']}" width="600px" height="500px" class="mb-2 d-block mx-auto" />
                  `;
                  html += `
                  <div class="mb-3 d-flex justify-content-center gap-2">
                    <button id="change_${content['TCC_ID']}" class="btn change-up btn-light"><i class="fas fa-arrow-up"></i></button>
                    <button id="change_${content['TCC_ID']}" class="btn change-down btn-light"><i class="fas fa-arrow-down"></i></button>
                    <button data-bs-toggle="modal" data-bs-target="#crud_modal" id="update_content_${content['TCC_ID']}" class="btn btn-light update-core-content-btn py-1 text-primary">Update</button>
                    <button data-bs-toggle="modal" data-bs-target="#crud_modal" id="delete_content_${content['TCC_ID']}" class="btn btn-light delete-core-content-btn py-1 text-danger">Delete</button>
                  </div>
                  `;
                  break;
                case CONTENT_TYPE_LINK:
                  html = `
                  <div class="mb-3">
                    <a href="${content['CONTENT']}" target="_blank" class="d-inline" style="text-decoration: none">${content['CONTENT']}</a>
                    <div class="d-inline">
                      <button id="change_${content['TCC_ID']}" class="btn change-up btn-light"><i class="fas fa-arrow-up"></i></button>
                      <button id="change_${content['TCC_ID']}" class="btn change-down btn-light"><i class="fas fa-arrow-down"></i></button>
                      <button data-bs-toggle="modal" data-bs-target="#crud_modal" id="update_content_${content['TCC_ID']}" class="btn btn-light update-core-content-btn py-1 text-primary">Update</button> 
                      <button data-bs-toggle="modal" data-bs-target="#crud_modal" id="delete_content_${content['TCC_ID']}" class="btn btn-light delete-core-content-btn py-1 text-danger">Delete</button>
                    </div>
                  </div>
                  `;
                  break;
                case CONTENT_TYPE_UNORDERED_LIST:
                  lists = content['CONTENT'].split(',');
                  html = `<ul>`;
                  lists.forEach(function(list) {
                    html += `<li>${list}</li>`;
                  });
                  html += `</ul>`;
                  html += `
                  <div class="mb-2">
                    <button id="change_${content['TCC_ID']}" class="btn change-up btn-light"><i class="fas fa-arrow-up"></i></button>
                    <button id="change_${content['TCC_ID']}" class="btn change-down btn-light"><i class="fas fa-arrow-down"></i></button>
                    <button data-bs-toggle="modal" data-bs-target="#crud_modal" id="update_content_${content['TCC_ID']}" class="btn btn-light update-core-content-btn py-1 text-primary">Update</button>
                    <button data-bs-toggle="modal" data-bs-target="#crud_modal" id="delete_content_${content['TCC_ID']}" class="btn btn-light delete-core-content-btn py-1 text-danger">Delete</button>
                  </div>`;
                  break;
                case CONTENT_TYPE_ORDERED_LIST:
                  lists = content['CONTENT'].split(',');
                  html = `<ol>`;
                  lists.forEach(function(list) {
                    html += `<li>${list}</li>`;
                  });
                  html += `</ol>`;
                  html += `
                  <div class="mb-2">
                    <button id="change_${content['TCC_ID']}" class="btn change-up btn-light"><i class="fas fa-arrow-up"></i></button>
                    <button id="change_${content['TCC_ID']}" class="btn change-down btn-light"><i class="fas fa-arrow-down"></i></button>
                    <button data-bs-toggle="modal" data-bs-target="#crud_modal" id="update_content_${content['TCC_ID']}" class="btn btn-light update-core-content-btn py-1 text-primary">Update</button> 
                    <button data-bs-toggle="modal" data-bs-target="#crud_modal" id="delete_content_${content['TCC_ID']}" class="btn btn-light delete-core-content-btn py-1 text-danger">Delete</button>
                  </div>`;
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
                $.post(`includes/trainings.inc.php?type=TACT20`, {
                  tcc_id: itemId,
                  type: type
                }).done(function(a, b, xhr) {
                  location.reload();
                }).fail(function(xhr, a, b) {
                  console.log(a, b, xhr.status, xhr.responseJSON);
                })
              }

              $('#switchbtn').click(function() {
                const vrstats = this.checked ? 'AKTIF' : 'TIDAK';
                $.post(`includes/trainings.inc.php?type=TACT21&t_id=${t_id}`, {
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
                const tmpTccId = this.id.split("_").pop();

                // get core content type
                const tmpContentType = contents.find(content => content['TCC_ID'] == tmpTccId)['CONTENT_TYPE'];

                // get core content actual content
                const tmpCoreContent = contents.find(content => content['TCC_ID'] == tmpTccId)['CONTENT'];

                // set modal title
                $("#modal_title").html(`Update Core Content <strong>${tmpTccId}</strong>`);

                // set modal body
                $("#modal_body").html(`
                <form enctype="multipart/form-data" action="includes/trainings.inc.php?type=TACT16&tcc_id=${tmpTccId}&t_id=${t_id}" method="POST" id="core_content_form">
                  <select id="content_type" name="content_type" class="form-select">
                    <option value="" disabled selected>-- Select content type --</option>
                    <option value="1" ${tmpContentType == 1? "selected": ""}>Paragraph</option>
                    <option value="2" ${tmpContentType == 2? "selected": ""}>Image</option>
                    <option value="3" ${tmpContentType == 3? "selected": ""}>Video</option>
                    <option value="4" ${tmpContentType == 4? "selected": ""}>PDF</option>
                    <option value="5" ${tmpContentType == 5? "selected": ""}>Link</option>
                    <option value="6" ${tmpContentType == 6? "selected": ""}>Unordered List</option>
                    <option value="7" ${tmpContentType == 7? "selected": ""}>Ordered List</option>
                    <option value="8" ${tmpContentType == 8 || tmpContentType == 9 || tmpContentType == 10? "selected": ""}>Title</option>
                  </select>
                  <div id="content_container" class="mt-2"></div>
                  <small id="content_error" class="text-danger"></small>
                </form>
              `);

                // input value variables
                contentType = tmpContentType;
                let content;

                // set original content 
                if (tmpContentType == CONTENT_TYPE_ORDERED_LIST || tmpContentType == CONTENT_TYPE_UNORDERED_LIST) {
                  content = tmpCoreContent.split(',');
                } else {
                  content = tmpCoreContent;
                }

                // set content container
                setContentContainer(contentType);

                // core content type change listener
                $("#content_type").change(function() {
                  contentType = this.value;
                  setContentContainer(contentType);
                })

                function setContentContainer(contentType) {
                  // set content container
                  let html = ``;

                  // add title options if content type is 8, 9, 10
                  if (contentType == CONTENT_TYPE_H1 || contentType == CONTENT_TYPE_H2 || contentType == CONTENT_TYPE_H3) {
                    html = `
                    <select id="title_type" name="title_type" class="form-select mt-2">
                      <option value="" disabled selected>-- Select title size (default: XXL) --</option>
                      <option ${contentType == CONTENT_TYPE_H1? "selected": ""} value="8">XXL</option>
                      <option ${contentType == CONTENT_TYPE_H2? "selected": ""} value="9">XL</option>
                      <option ${contentType == CONTENT_TYPE_H3? "selected": ""} value="10">L</option>
                    </select>
                  `;
                  }

                  // set content input
                  switch (contentType) {
                    case CONTENT_TYPE_H1:
                    case CONTENT_TYPE_H2:
                    case CONTENT_TYPE_H3:
                      html += `
                        <input id="core_content_input" type="text" placeholder="Enter title here" class="form-control mt-2" value="${tmpCoreContent}" />
                      `;
                      break;
                    case CONTENT_TYPE_PARAGRAPH:
                      html += `<textarea id="core_content_input" name="core_content" rows="6" class="form-control" placeholder="Enter paragraph here">${tmpCoreContent}</textarea>`;
                      break;
                    case CONTENT_TYPE_IMAGE:
                      html += `<input type="file" name="core_content" id="core_content_input" class="form-control" />`;
                      break;
                    case CONTENT_TYPE_VIDEO:
                      html += `<input type="file" name="core_content" id="core_content_input" class="form-control" />`;
                      break;
                    case CONTENT_TYPE_PDF:
                      html += `<input type="file" name="core_content" id="core_content_input" class="form-control" />`;
                      break;
                    case CONTENT_TYPE_LINK:
                      html += `<input id="core_content_input" name="core_content" type="text" placeholder="Enter URL content here" class="form-control" value="${tmpCoreContent}" />`;
                      break;
                    case CONTENT_TYPE_UNORDERED_LIST:
                    case CONTENT_TYPE_ORDERED_LIST:
                      html += `
                      <div class="input-group">
                        <input id="core_content_input" type="text" placeholder="Enter list item here" class="form-control" />
                        <button id="add_core_content_input" type="button" class="btn btn-secondary">Add</button>
                      </div>
                      `;
                      break;
                  }

                  // set content
                  switch (tmpContentType) {
                    case CONTENT_TYPE_H1:
                      html += `<h1 class='my-3'>${tmpCoreContent}</h1>`;
                      break;
                    case CONTENT_TYPE_H2:
                      html += `<h2 class='my-3'>${tmpCoreContent}</h2>`;
                      break;
                    case CONTENT_TYPE_H3:
                      html += `<h3 class='my-3'>${tmpCoreContent}</h3>`;
                      break;
                    case CONTENT_TYPE_PARAGRAPH:
                      html += `<p class="my-3">${tmpCoreContent}</p>`;
                      break;
                    case CONTENT_TYPE_LINK:
                      html += `<a href="${tmpCoreContent}" target="_blank" class="my-3 d-block">${tmpCoreContent}</a>`;
                      break;
                    case CONTENT_TYPE_IMAGE:
                      html += `<img style="width: 400px" class="d-block mx-auto my-3" src="public/imgs/uploads/${tmpCoreContent}" />`;
                      break;
                    case CONTENT_TYPE_VIDEO:
                      html += `
                        <video width="400" class="d-block my-3 mx-auto mb-2" controls>
                          <source src="public/imgs/uploads/${tmpCoreContent}" type="video/${tmpCoreContent.split('.').pop()}">
                          <source src="movie.ogg" type="video/ogg">
                          Your browser does not support the video tag.
                        </video>
                      `;
                      break;
                    case CONTENT_TYPE_PDF:
                      html += `
                      <embed src="public/imgs/uploads/${tmpCoreContent}" width="400px" height="300px" class="mb-2 d-block mx-auto my-3" />
                      `;
                      break;
                    case CONTENT_TYPE_UNORDERED_LIST:
                    case CONTENT_TYPE_ORDERED_LIST:
                      content = tmpCoreContent.split(',');
                      html += `
                        <div id="lists_input_container" class="d-flex gap-2 mt-2 flex-wrap">
                      `;
                      content.forEach(function(item) {
                        html += `<span id="item_${item}" class="bg-light rounded-2 px-3 py-1">${item} <i id="remove_${item}" class="fas fa-times remove-list-btn" style="cursor: pointer"></i></span>`;
                      })
                      html += `</div>`
                      break;
                  }

                  // set content container to html
                  $("#content_container").html(html);

                  $("#title_type").change(function() {
                    console.log(this.value);

                    contentType = this.value;
                  });

                  // remove list from lists input container
                  $(".remove-list-btn").click(function() {
                    const tmpId = this.id.split('_').pop();
                    document.getElementById(`item_${tmpId}`).remove();
                    content = content.filter(e => e != tmpId);
                  })
                }

                // set input behavior of each content type
                if (contentType == CONTENT_TYPE_PARAGRAPH || contentType == CONTENT_TYPE_LINK || contentType == CONTENT_TYPE_H1 || contentType == CONTENT_TYPE_H2 || contentType == CONTENT_TYPE_H3) {
                  $("#core_content_input").keyup(function() {
                    content = this.value;
                  })
                } else if (contentType == CONTENT_TYPE_ORDERED_LIST || contentType == CONTENT_TYPE_UNORDERED_LIST) {
                  $("#add_core_content_input").click(function() {
                    addList();
                  })
                }

                function addList() {
                  const tmp = $("#core_content_input").val();
                  content.push(tmp);
                  $("#lists_input_container").append(`
                    <span id="item_${tmp}" class="bg-light rounded-2 px-3 py-1">${tmp} <i id="remove_${tmp}" class="fas fa-times remove-list-btn" style="cursor: pointer"></i></span>
                  `);
                  // remove list from lists input container
                  $(".remove-list-btn").click(function() {
                    const tmpId = this.id.split('_').pop();
                    document.getElementById(`item_${tmpId}`).remove();
                    content = content.filter(e => e != tmpId);
                  })
                  $("#core_content_input").val('');
                }

                // set modal action button
                $("#modal_action_btn").html(`<i class="fas fa-pencil"></i> Update content`);
                $("#modal_action_btn").removeClass('btn-success').removeClass("btn-danger").addClass("btn-primary");

                // modal action button handler
                $("#modal_action_btn").click(function() {
                  if (contentType !== CONTENT_TYPE_ORDERED_LIST && contentType !== CONTENT_TYPE_UNORDERED_LIST) {
                    // get core content
                    content = $("#core_content_input").val();
                  }

                  // create core content
                  if (contentType == CONTENT_TYPE_IMAGE || contentType == CONTENT_TYPE_VIDEO || contentType == CONTENT_TYPE_PDF) {
                    $("#core_content_form").submit();
                  } else {
                    console.log(content, contentType);

                    $.post(`includes/trainings.inc.php?type=TACT16&t_id=${t_id}&tcc_id=${tmpTccId}`, {
                      content_type: contentType,
                      core_content: content
                    }).done(function(a, b, xhr) {
                      // reload current page
                      location.reload();
                    }).fail(function(xhr, a, b) {
                      console.log(xhr.status, xhr.responseJSON);
                      if (xhr.responseJSON) {
                        const errors = xhr.responseJSON;
                        $("#core_content_error").html(errors['core_content']);
                      }
                    })
                  }
                })
              })

              // show delete core content handler
              $(".delete-core-content-btn").click(function() {
                // get core content id
                const tmpTccId = this.id.split('_').pop();

                // set modal title
                $("#modal_title").html(`Delete Training Core Content <strong>${tmpTccId}</strong>`);

                // set modal body
                $("#modal_body").html(`
                <p>Are you sure to delete training core content <strong>${tmpTccId}</strong>? This action is irreversible!</p>
              `);

                // set modal action button
                $("#modal_action_btn").html(`<i class="fas fa-trash"></i> Delete content`);
                $("#modal_action_btn").removeClass('btn-success').removeClass('btn-primary').addClass("btn-danger");

                // modal action button handler
                $("#modal_action_btn").click(function() {
                  // delete core content
                  $.get(`includes/trainings.inc.php?type=TACT14&tcc_id=${tmpTccId}&t_id=${t_id}`).done(function(a, b, xhr) {
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
                  <form enctype="multipart/form-data" action="includes/trainings.inc.php?type=TACT15&t_id=${t_id}" method="POST" id="core_content_form">
                    <select id="content_type" name="content_type" class="form-select">
                      <option value="" disabled selected>-- Select content type --</option>
                      <option value="1">Paragraph</option>
                      <option value="2">Image</option>
                      <option value="3">Video</option>
                      <option value="4">PDF</option>
                      <option value="5">Link</option>
                      <option value="6">Unordered List</option>
                      <option value="7">Ordered List</option>
                      <option value="8">Title</option>
                    </select>
                    <div id="content_container" class="mt-2"></div>
                    <small id="content_error" class="text-danger"></small>
                  </form>
                `);

                // input value variables
                let contentType = $("#content_type").val();
                let content = '';

                // core content type change listener
                $("#content_type").change(function() {
                  contentType = this.value;
                  let html = ``;
                  switch (this.value) {
                    case CONTENT_TYPE_PARAGRAPH:
                      html = `<textarea id="core_content_input" name="core_content" rows="6" class="form-control" placeholder="Enter paragraph here"></textarea>`;
                      break;
                    case CONTENT_TYPE_IMAGE:
                    case CONTENT_TYPE_VIDEO:
                    case CONTENT_TYPE_PDF:
                      html = `<input type="file" name="core_content" id="core_content_input" class="form-control" accept="image/*,.pdf,video/*"/>`;
                      break;
                    case CONTENT_TYPE_LINK:
                      html = `<input id="core_content_input" name="core_content" type="text" placeholder="Enter URL content here" class="form-control" />`;
                      break;
                    case CONTENT_TYPE_UNORDERED_LIST:
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
                    case CONTENT_TYPE_ORDERED_LIST:
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
                    case CONTENT_TYPE_H1:
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
                  if (contentType == CONTENT_TYPE_PARAGRAPH || contentType == CONTENT_TYPE_LINK) {
                    $("#core_content_input").keyup(function() {
                      content = this.value;
                    })
                  } else if (contentType == CONTENT_TYPE_ORDERED_LIST || contentType == CONTENT_TYPE_UNORDERED_LIST) {
                    content = [];
                    $("#add_core_content_input").click(function() {
                      addList();
                    })

                    function addList() {
                      const tmp = $("#core_content_input").val();
                      content.push(tmp);
                      $("#lists_input_container").append(`
                        <span id="item_${tmp}" class="bg-light rounded-2 px-3 py-1">${tmp} <i id="remove_${tmp}" class="fas fa-times remove-list-btn" style="cursor: pointer"></i></span>
                      `);
                      $(".remove-list-btn").click(function() {
                        const tmpId = this.id.split('_').pop();
                        $(`#item_${tmpId}`).remove();
                        content = content.filter(e => e != tmpId);
                      })
                      $("#core_content_input").val('');
                    }
                  } else if (contentType == CONTENT_TYPE_H1 || contentType == CONTENT_TYPE_H2 || contentType == CONTENT_TYPE_H3) {
                    $("#title_type").change(function() {
                      contentType = this.value;
                    });
                    $("#core_content_input").keyup(function() {
                      content = this.value;
                    });
                  }
                })

                // set modal action button
                $("#modal_action_btn").html(`<i class="fas fa-plus"></i> Create content`);
                $("#modal_action_btn").removeClass('btn-primary').removeClass("btn-danger").addClass("btn-success");

                // modal action button handler
                $("#modal_action_btn").click(function() {
                  // create core content
                  if (contentType == CONTENT_TYPE_IMAGE || contentType == CONTENT_TYPE_VIDEO || contentType == CONTENT_TYPE_PDF) {
                    $("#core_content_form").submit();
                  } else {
                    $.post(`includes/trainings.inc.php?type=TACT15&t_id=${t_id}`, {
                      content_type: contentType,
                      core_content: content
                    }).done(function(a, b, xhr) {
                      // reload current page
                      location.reload();
                    }).fail(function(xhr, a, b) {
                      console.log(a, b, xhr.status, xhr.responseJSON);
                      if (xhr.responseJSON) {
                        const errors = xhr.responseJSON;
                        $("#core_content_error").html(errors['core_content']);
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