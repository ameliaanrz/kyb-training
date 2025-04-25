<!-- logout modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="logoutModalLabel">Logout Confirmation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure to end current session?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <a href="includes/logout.inc.php" class="btn btn-danger"><i class="fas fa-sign-out"></i> Logout</a>
      </div>
    </div>
  </div>
</div>
<!-- logout modal end -->
</main>
<footer class="container">
  <p>&copy; 2023 PT. Kayaba Indonesia</p>
</footer>
<script type="text/javascript">
  $(document).ready(function() {
    
      // get notifications
      let showLimit = 5;
  let offset = 0;
      getNotifications();

    function getNotifications() {
      $.get(`includes/notifications.inc.php?limit=${showLimit}&offset=${offset}`)
        .done(function (type, status, xhr) {
          const resData = xhr.responseJSON;
          console.log(resData);
          const notifsCount = resData['notifs_count'];
          $('.notification').append(`<span class="badge">${notifsCount}</span>`);
        })
        .fail(function (xhr, type, status) {
          console.log(type, status, xhr.responseJSON, xhr.status);
        });
    }
  });
  </script>
<!-- bootstrap js -->
<!-- <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script> -->
<script src="node_modules/bootstrap/dist/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
<script src="node_modules/lightslider/dist/js/lightslider.min.js"></script>
<script src="node_modules/select2/dist/js/select2.min.js"></script>
<script src="node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
<script src="node_modules/sweetalert2/dist/sweetalert2.min.js"></script>

<!-- Template Main Javascript File -->
  <script src="public/js/main.js"></script>

</body>

</html>