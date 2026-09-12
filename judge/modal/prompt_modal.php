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

          <div class="modal-footer">
            <h6>by: <u>Administrator</u></h6>
          </div>
        </div>
    </div>
  </div>