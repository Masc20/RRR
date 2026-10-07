<!-- Prompt Modal-->
  <div class="modal fade" id="promptModal" tabindex="-1" role="dialog" 
    aria-labelledby="promptModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false"> 
    <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="promptModalLabel">Hello, <?php echo $fullName; ?></h5>
          </div>

          <div class="modal-body">
            <h5 id="promptMsg" class="alert alert-info">
              <!-- prompt message will load here --> 
            </h5>
          </div>

          <div class="modal-footer d-flex justify-content-between align-items-center">
            <a href="../login/logout.php" class="btn btn-danger btn-sm">
              <i class="fa fa-fw fa-sign-out"></i> Log-out
            </a>
            <small class="text-muted">by: <u>Administrator</u></small>
          </div>
        </div>
    </div>
  </div>