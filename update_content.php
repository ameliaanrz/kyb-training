 <?php
include 'partials/_header.php';
?>
 <!-- Carousel -->
  <button id="edit" class="btn btn-success">Update Content</button>

 <br>
 <br>
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
    <br>
  </div>
<br>

  <div id="editFormModal" class="modal modal-xl fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Content</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <div class="container">
                        <!-- Row for Image Slider and Profile Images -->
                        <div class="row">
                            <div class="col-md-11">
                                <div class="form-group">
                                    <label for="imgSlider">Image Slider</label>
                                    <div class="input-group">
                                        <button class="btn btn-outline-secondary" type="button" id="chooseFileBtn">Choose File</button>
                                        <input type="text" class="form-control" id="imgSliderView" readonly>
                                        <input type="text" class="form-control" style="display:none" id="imgSlider" readonly>
                                        <input type="file" class="form-control" style="display:none" id="imgSliderUpload" accept="image/*"  readonly multiple>
                                    </div>
                                    <small id="imgSlider_error" class="text-danger"></small>
                                </div>
                                <div id="imagePreviews" class="d-flex flex-wrap">
                                    <!-- Image previews will be displayed here -->
                                </div>
                            </div>
                            
                        </div>

                        <div class="row">
                          <div class="col-md-7">
                                <div class="form-group">
                                    <label for="imgProfile1">Profile 1 Image</label>
                                    <div class="input-group">
                                        <button class="btn btn-outline-secondary" type="button" id="BtnProfile1">Choose File</button>
                                        <input type="text" class="form-control" id="imgProfile1View" readonly>
                                        <input type="text" class="form-control" style="display:none" id="imgProfile1" readonly>
                                        <input type="file" class="form-control" style="display:none" id="imgProfile1Upload"accept="image/*" readonly>
                                    </div>
                                    <small id="imgProfile1_error" class="text-danger"></small>
                                </div>
                                <div id="imagePreview1" class="d-flex flex-wrap">
                                    <!-- Image previews will be displayed here -->
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="titleProfile1">Profile 1 Title</label>
                                    <input type="text" class="form-control" id="titleProfile1">
                                    <small id="titleProfile1_error" class=" text-danger"></small>

                                </div>
                                
                                <div class="form-group">
                                    <label for="descProfile1">Profile 1 Description</label>
                                    <textarea class="form-control" rows="5" id="descProfile1"></textarea>
                                                                        <small id="descProfile1_error" class="text-danger"></small>
                                </div>
                              </div>
                        </div>
                        <!-- Row for Profile Titles -->
                        <div class="row">
                            <div class="col-md-7">
                                    <label for="imgProfile2">Profile 2 Image</label>
                                <div class="input-group">
                                        <button class="btn btn-outline-secondary" type="button" id="BtnProfile2">Choose File</button>
                                        <input type="text" class="form-control" id="imgProfile2View" readonly>
                                        <input type="text" class="form-control" style="display:none" id="imgProfile2" readonly>
                                        <input type="file" class="form-control" style="display:none" id="imgProfile2Upload" accept="image/*" readonly>
                                </div>
                                <small id="imgProfile2_error" class="text-danger"></small>

                                <div id="imagePreview2" class="d-flex flex-wrap">
                                    <!-- Image previews will be displayed here -->
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="titleProfile2">Profile 2 Title</label>
                                    <input type="text" class="form-control" id="titleProfile2">
                                    <small id="titleProfile2_error" class="text-danger"></small>
                                </div>
                                <div class="form-group">
                                    <label for="descProfile2">Profile 2 Description</label>
                                    <textarea class="form-control" rows="5" id="descProfile2"></textarea>
                                    <small id="descProfile2_error" class="text-danger"></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary" id="saveChanges">Save changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" defer>
    $(document).ready(function() {
      // Load the overview data on page load
      const id_overview ='';
      getOverview();
        $("#editFormModal").modal('show');

      $("#edit").click(function(){
        $("#editFormModal").modal('show');
      });

      $("#chooseFileBtn").click(function(){
        $("#imgSliderUpload").click();
      });

      $("#BtnProfile1").click(function(){
        $("#imgProfile1Upload").click();
      });

      $("#BtnProfile2").click(function(){
        $("#imgProfile2Upload").click();
      });

      $('#imgSliderUpload').on('change', function(event) {
          const filePath = 'public/imgs/uploads/' + event.target.files[0].name;
          let imgSliderVal = $('#imgSlider').val();
          if (imgSliderVal) {
              imgSliderVal += ',' + filePath;
          } else {
              imgSliderVal = filePath;
          }
          $('#imgSlider').val(imgSliderVal);
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreviews').append(`
                    <div class="image-preview">
                        <img src="${e.target.result}" alt="Image Preview">
                        <button class="remove-image">&times;</button>
                        <div>${file.name}</div>
                    </div>
                `);
            };
            reader.readAsDataURL(file);
        }
      });

      $('#imgProfile1Upload').on('change', function(event) {
          const filePath = 'public/imgs/uploads/' + event.target.files[0].name;
          $('#imgProfile1').val(filePath);
            $('#imgProfile1View').val(event.target.files[0].name);
          const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview1').html(`
                    <div class="image-preview">
                        <img src="${e.target.result}" alt="Image Preview">
                        <button class="remove-image">&times;</button>
                        <div>${file.name}</div>
                    </div>
                `);
            };
            reader.readAsDataURL(file);
        }
      });
      $('#imgProfile2Upload').on('change', function(event) {
          const filePath = 'public/imgs/uploads/' + event.target.files[0].name;
          $('#imgProfile2').val(filePath);
            $('#imgProfile2View').val(event.target.files[0].name);
          const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview2').html(`
                    <div class="image-preview">
                        <img src="${e.target.result}" alt="Image Preview">
                        <button class="remove-image">&times;</button>
                        <div>${file.name}</div>
                    </div>
                `);
            };
            reader.readAsDataURL(file);
        }
      });

      function getOverview() {
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

            // Populate form with the current overview data
            $('#imgSlider').val(overview['IMG_SLIDER']);
            $('#imgSlider').attr('data-id',overview['ID_OVERVIEW']);
            $('#imgProfile1').val(overview['IMG_PROFILE_1']);
            $('#titleProfile1').val(overview['TITLE_PROFILE_1']);
            $('#descProfile1').val(overview['DESC_PROFILE_1']);
            $('#imgProfile2').val(overview['IMG_PROFILE_2']);
            $('#titleProfile2').val(overview['TITLE_PROFILE_2']);
            $('#descProfile2').val(overview['DESC_PROFILE_2']);
            updateImagePreviews();
            }
        }).fail(function(xhr, a, b) {
            console.log(a, b, xhr.status, xhr.responseJSON);
        });
        }
 
        function updateImagePreviews() {
            const imgSlider = $('#imgSlider').val();
            const urls = imgSlider.split(',');
            const imagePreviews = $('#imagePreviews');
            
            const imgProfile1 =$('#imgProfile1').val();
            const urlimg1 = imgProfile1.split(',');
            const imagePreview1 = $('#imagePreview1');

            const imgProfile2 =$('#imgProfile2').val();
            const urlimg2 = imgProfile2.split(',');
            const imagePreview2 = $('#imagePreview2');

            let jud =[];
            let jud1 =[];
            let jud2=[];

            imagePreviews.empty();
            imagePreview1.empty();
            imagePreview2.empty();

            urls.forEach(url => {
                if (url.trim() !== '') {
                  const filename = url.trim().split('/').pop();
                  jud.push(filename);
                    const preview = $(`
                        <div class="image-preview">
                            <img src="${url.trim()}" alt="Image Preview">
                            <button class="remove-image">&times;</button>
                            <div>${filename}</div>
                        </div>
                    `);
                    preview.find('.remove-image').on('click', function() {
                        removeImage(url.trim());
                    });
                    imagePreviews.append(preview);
                }
            });
            urlimg1.forEach(url1 => {
                if (url1.trim() !== '') {
                  const filename = url1.trim().split('/').pop();
                  jud1.push(filename);
                    const preview = $(`
                        <div class="image-preview">
                            <img src="${url1.trim()}" alt="Image Preview">
                            <button class="remove-image">&times;</button>
                            <div>${filename}</div>
                        </div>
                    `);
                    preview.find('.remove-image').on('click', function() {
                        removeImage(url1.trim());
                    });
                    imagePreview1.append(preview);
                }
            });

            urlimg2.forEach(url => {
                if (url.trim() !== '') {
                  const filename = url.trim().split('/').pop();
                  jud2.push(filename);
                    const preview = $(`
                        <div class="image-preview">
                            <img src="${url.trim()}" alt="Image Preview">
                            <button class="remove-image">&times;</button>
                            <div>${filename}</div>
                        </div>
                    `);
                    preview.find('.remove-image').on('click', function() {
                        removeImage(url.trim());
                    });
                    imagePreview2.append(preview);
                }
            });

            $('#imgSliderView').val(jud.join(','));
            $('#imgProfile1View').val(jud1.join(','));
            $('#imgProfile2View').val(jud2.join(','));
        }

        $('#imgSlider').on('change', updateImagePreviews);

        function removeImage(url) {
            let urls = $('#imgSlider').val().split(',').map(u => u.trim());
            let urlimg1 = $('#imgProfile1').val().split(',').map(u => u.trim());
            let urlimg2 = $('#imgProfile2').val().split(',').map(u => u.trim());
            
            urls = urls.filter(u => u !== url);
            urlimg1 = urlimg1.filter(u => u !== url);
            urlimg2 = urlimg2.filter(u => u !== url);

            $('#imgSlider').val(urls.join(', '));
            $('#imgProfile1').val(urlimg1.join(', '));
            $('#imgProfile2').val(urlimg2.join(', '));
            updateImagePreviews();
        }

      // Save changes
     $('#saveChanges').click(function() {
      var formData = new FormData();
      
      formData.append('id_overview', $('#imgSlider').data('id'));
      formData.append('img_slider', $('#imgSlider').val());
      formData.append('sliderUpload', $('#imgSliderUpload')[0].files[0]);
      formData.append('img_profile1', $('#imgProfile1').val());
      formData.append('imgprofile1Upload', $('#imgProfile1Upload')[0].files[0]);
      formData.append('title_profile1', $('#titleProfile1').val());
      formData.append('desc_profile1', $('#descProfile1').val());
      formData.append('img_profile2', $('#imgProfile2').val());
      formData.append('imgprofile2Upload', $('#imgProfile2Upload')[0].files[0]);
      formData.append('title_profile2', $('#titleProfile2').val());
      formData.append('desc_profile2', $('#descProfile2').val());

      $.ajax({
        url: 'includes/overview.inc.php?type=OVR02',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
          location.reload();
        },
        error: function(xhr) {
          if(xhr.responseJSON){
            const errors = xhr.responseJSON;
            if(errors['img_slider']) {
              $('#imgSlider_error').text(errors['img_slider']);
            }
            if(errors['img_profile1']) {
              $('#imgProfile1_error').text(errors['img_profile1']);
            }
            if(errors['title_profile1']) {
              $('#titleProfile1_error').text(errors['title_profile1']);
            }
            if(errors['desc_profile1']) {
              $('#descProfile1_error').text(errors['desc_profile1']);
            }
            if(errors['img_profile2']) {
              $('#imgProfile2_error').text(errors['img_profile2']);
            }
            if(errors['title_profile2']) {
              $('#titleProfile2_error').text(errors['title_profile2']);
            }
            if(errors['desc_profile2']) {
              $('#descProfile2_error').text(errors['desc_profile2']);
            }
          }
        }
      });
    });

      function SwalNotifYesNo(type, title, desc, successMessage, callback) {
      Swal.fire({
        title: title,
        text: desc,
        icon: type,
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
      }).then((result) => {
        if (result.isConfirmed) {
          if (callback && typeof callback === "function") {
            callback().then(() => {
              Swal.fire(
                'Success!',
                successMessage,
                'success'
              ).then(() => {
                location.reload(); // Reload the page after showing the error message
              });
            }).catch(() => {
              Swal.fire(
                'Error!',
                'Fill All Field!',
                'error'
              ).then(() => {
                location.reload(); // Reload the page after showing the error message
              });
            });
          }
        }
      });
    }
    });
</script>
  <?php
include_once __DIR__ . '/partials/_footer.php';
?>