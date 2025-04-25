<?php
include_once __DIR__ . '/partials/_header.php';
if (!isset($_SESSION['NPK']) || !isset($_SESSION['RLS_ID']) || (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] != 'RLS01')) {
  header("Location: login.php");
}
?>

<h1 class="fw-bold">PT. Kayaba Training Center Set Notification</h1>
<!-- <p class="fs-5">Participants administrator dashboard to manage all PT. Kayaba Indonesia training participants</p> -->
<a href="notif_view.php#participants_list_container" class="text-decoration-none btn btn-light"><i
    class="fas fa-circle-down"></i> Notification List</a>
    <div class="card mt-4 py-3">
        <div class="card-body pt-2">
          <h2 class="fw-bold">Perhatian!</h2>
          <br><br>
          <!-- tulisan dengan tag h3 berika class fw-bold dan tulisan merah sebagai warning -->
          <p>NTFT01 adalah untuk Notifikasi Event kepada PIC dan juga Kepala Department sebagai contoh dibawah</p>
          <img src="public/imgs/EVENT_NOTIF.png" style="width:90%; cursor:pointer " alt="notfound" class="eventImage" data-image="public/imgs/EVENT_NOTIF.png" data-imgcontoh="public/imgs/contoh_event.png">
          <br><hr>
          <br>
          <p>NTFT02 adalah untuk Notifikasi Event kepada Admin HR Department sebagai contoh dibawah</p>
          <img src="public/imgs/APPROVAL_NOTIF.png" style="width:90%; cursor:pointer " alt="notfound" class="eventImage" data-image="public/imgs/APPROVAL_NOTIF.png" data-imgcontoh="public/imgs/contoh_approval.png" >
       <br><hr>
       <br> 
          <p>NTFT03 adalah untuk Notifikasi Event kepada PIC dan juga Kepala Department sebagai contoh dibawah</p>
          <img src="public/imgs/APPROVED_NOTIF.png" style="width:90%; cursor:pointer " alt="notfound" class="eventImage" data-image="public/imgs/APPROVED_NOTIF.png" data-imgcontoh="public/imgs/contoh_approved.png" >
        <br><hr>
        <br> 
          <p>NTFT04 adalah untuk Notifikasi Event kepada PIC sebagai contoh dibawah</p>
          <img src="public/imgs/APPROVAL_KADEPT.png" style="width:90%; cursor:pointer " alt="notfound" class="eventImage" data-image="public/imgs/APPROVAL_KADEPT.png" data-imgcontoh="public/imgs/contoh_approval_kadept.png" >
        <br><hr>
        <br> 
          <p>NTFT05 adalah untuk Notifikasi Penolakan Participant sebagai contoh dibawah</p>
          <img src="public/imgs/DISAPPROVE_NOTIF.png" style="width:90%; cursor:pointer " alt="notfound" class="eventImage" data-image="public/imgs/DISAPPROVE_NOTIF.png" data-imgcontoh="public/imgs/contoh_disaprove.png" >
        <br><hr>
        <!--<br> 
          <p>NTFT06 adalah untuk Notifikasi Reminder Participant sebagai contoh dibawah</p>
          <img src="public/imgs/EVENT_NOTIF.png" style="width:90%; cursor:pointer " alt="notfound" class="eventImage" data-image="public/imgs/EVENT_NOTIF.png" data-imgcontoh="public/imgs/contoh_disaprove.png" >
        <br><hr>-->
        <br>
          <p class="fw-bold text-danger"> * SEMUA CODE TELAH DI SET UNTUK SEBAGAI MANA YANG SUDAH DI TENTUKAN, ANDA HANYA BISA EDIT DESC NYA SAJA</p>
        </div>
    </div>
<div class="card mt-4 py-3">
  <!-- <div class="card-header pb-0 border-0 bg-white">
    <h3 class="fw-semibold">Participants List</h3>
    <p>List of all participants participating in PT. Kayaba Indonesia trainings</p>
    <hr>
  </div> -->
  <div class="card-body pt-2">
    <!-- loading spinner -->
    <div id="loading_spinner" class="spinner-border text-primary d-block mx-auto my-4" role="status">
      <span class="sr-only">Loading...</span>
    </div>
    <!-- query form -->
    <div id="query_form_container" class="card-body pt-0 mt-2 d-none">
    </div>
    <!-- participants list spinner -->
    <div id="participants_list_spinner" class="spinner-border text-primary d-none mx-auto my-4" role="status">
      <span class="sr-only">Loading...</span>
    </div>
    <!-- participants list -->
    <div id="participants_list_container" class="d-none">
      <table class="table mt-2 table-responsive table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th scope="col" class="text-center">CODE</th>
            <th scope="col" class="text-center">TITLE</th>
            <th scope="col" class="text-center">DESCRIPTION</th>
            <th scope="col" class="text-center">ACTION</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>

    <!-- Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Details</h5>
                    <button type="button"  class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <p>Gunakan variabel $namaevent di deskripsi / title notifikasi</p>
                    <img src="" class="img-fluid" alt="notfound" id="modalImageContoh">
          <br>
          <br>
                    <p>Hasilnya Akan seperti ini</p>
                    <img src="" class="img-fluid" alt="notfound" id="modalImage">
                </div>
            </div>
        </div>
    </div>

      <!-- formModal begin -->
    <div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="formModalBody" class="modal-body">

            </div>
            <div class="modal-footer">
                <button id="modal_close_btn" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button id="actionBtn" type="button" class="btn btn-primary"></button>
            </div>
            </div>
        </div>
    </div>
    <!-- formModal end -->

      <!-- pagination links -->
      <div class="d-flex justify-content-end mt-4">
        <div id="pagination_container"></div>
      </div>
    </div>
  </div>
</div>

<!-- require paginationjs -->
<script type="text/javascript" src="node_modules/paginationjs/dist/pagination.min.js"></script>

<!-- custom jquery script -->
<script type="text/javascript" defer>
  $(document).ready(function () {
    // variables
    let listsShown ='';
    let dpt_id = '';
    let sec_id = '';
    let sub_sec_id = '';
    let q_grade = '';
    let q_gender = '';
    let t_id = '';
    let search = '';

    getNotifs();
    changeListShown();

    $("#lists_shown").change(function() {
        changeListShown();
    });

    $(".close").click(function(){
      $("#imageModal").modal("hide");
    });

    
    $(".eventImage").click(function(){
            // Get the image source from the data-image attribute
            let imgSrc = $(this).data('image');
            let imgSrc2 = $(this).data('imgcontoh');
            console.log(imgSrc2);
            // Set the src attribute of the modal image
            $("#modalImage").attr('src', imgSrc);
            $("#modalImageContoh").attr('src', imgSrc2);
            // Show the modal
            $("#imageModal").modal('show');
        });

    function changeListShown() {
        listsShown = $("#lists_shown").val();
        // remove participants list container
      $("#participants_list_container").addClass('d-none');

      // show participants list spinner
      $("#participants_list_spinner").removeClass('d-none').addClass('d-block');
        setTimeout(function () {
        getNotifs();
      }, 1000);
    }

    function getNotifs() {
      $.get(`includes/notifications.inc.php?type=6`).done(function (a, b, xhr) {
        if (xhr.status == 204) {
          // no users found
          $("#pagination_container").pagination({
            dataSource: [],
            pageSize: listsShown,
            callback: function (data, pagination) {
              $("tbody").html(`
                <tr class="bg-white">
                  <td colspan="9" class="text-center">No users found</td>
                </tr>
              `);
            }
          });
        } else {
          if (xhr?.responseJSON) {
            let data = xhr.responseJSON;  
                let html = '';
                data.forEach(function (notif) {
                  html += `
                  <tr>
                    <td>${notif['NTF_T_ID']}</td>
                    <td>${notif['NTF_TITLE']}</td>
                    <td>${notif['NTF_DESC']}</td>
                    <td>
                        <button class="btn btn-outline-dark" type="button">
                          <a  data-bs-toggle="modal" id="show-update-btn-${notif['NTF_T_ID']}" data-bs-target="#formModal" class="dropdown-item show-update-btn" data-id="${notif['NTF_T_ID']}"><i class="fas fa-eye" ></i> Notif Detail</a>
                        </button>
                    </td>
                  </tr>
                `;
                });

                $("tbody").html(html);

                // show formModal
                $(".show-update-btn").click(function () {
                    let notifsId = $(this).data('id');
                    let selectedNotif = data.find(notif => notif['NTF_T_ID'] == notifsId);
                    if (selectedNotif) {
                        $("#formModalLabel").html(`Notification ${selectedNotif['NTF_T_ID']} Details`);
                        $("#formModalBody").html(`
                          <label for="titleUpdate" class="form-label">Title<span class="text-danger"> *</span></label><br>
                          <input name="titleUpdate" id="titleUpdate" class="form-control event-input" value="${selectedNotif['NTF_TITLE']}" disabled /><br>
                          <label for="TextDesc" class="form-label">Description<span class="text-danger"> *</span></label><br>
                          <textarea id="TextDesc"  class="form-control event-input" disabled>${selectedNotif['NTF_DESC']}</textarea>
                          <small id="TextDesc_error" class="text-danger"></small>


                        `);
                        $("#actionBtn").html('Update');
                        $("#actionBtn").click(function (e){
                          e.preventDefault();
                          $("#TextDesc").attr('disabled',false);
                          $("#actionBtn").html('Save');
                          $("#actionBtn").attr('id','SaveBtn');
                          $("#SaveBtn").attr('data-id',notifsId);

                          $("#SaveBtn").click(function(){
                           const ntf_id = $(this).data('id');
                            const ntf_desc = $("#TextDesc").val();
                            if (ntf_desc == '') {
                                $('#TextDesc_error').html('*This field is required');
                            }else{
                                $.post(`includes/notifications.inc.php?type=7`, {ntf_id,ntf_desc}).done(function(a,b,xhr){
                                  //swal succes
                                  Swal.fire({
                                            icon: 'success',
                                            title: 'Success!',
                                            text: 'Notification Updated!',
                                            confirmButtonColor: '#3085d6',
                                            confirmButtonText: 'OK'
                                          }).then((result) => {
                                            location.reload();
                                          });
                                }).fail(function(xhr,a,b){
                                  console.log(a,b,xhr.status,xhr.responseJSON);
                                })
                            }
                          });
                        });
                    }
                });
              
          }
        }
        

        setTimeout(function () {
          // remove loading spinner
          $("#loading_spinner").removeClass('d-block').addClass('d-none');

          // remove participants list spinner
          $("#participants_list_spinner").removeClass('d-block').addClass('d-none');

          // show participants list container
          $("#participants_list_container").removeClass('d-none');
        }, 500);
      }).fail(function (xhr, a, b) {
        console.log(a, b, xhr.status, xhr.responseJSON);
      })
    }

    $('#formModal').on('hidden.bs.modal', function () {
        location.reload();
    });

  })
</script>

<?php
include_once __DIR__ . '/partials/_footer.php';
?>