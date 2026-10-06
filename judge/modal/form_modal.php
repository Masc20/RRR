<!-- Form Modal-->
<div class="modal fade judge-modal" id="formModal" tabindex="-1" role="dialog" 
  aria-labelledby="formModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false"> 
  <div class="modal-dialog formDialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="formModalLabel"></h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close" 
                  onclick="$('#formContent').html(null);">
            <span aria-hidden="true">×</span>
          </button>
        </div>

        <div class="modal-body" id="formContent">

        </div>

        <div class="modal-footer d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center">
            <span class="text-dark mr-2 font-weight-bold" style="font-size: 0.95rem;">Category Total:</span>
            <span class="judge-total-box" id="tot">0</span>
            <span class="text-muted ml-2 font-weight-bold" style="font-size: 0.85rem;">pts</span>
          </div>
          <div>
            <button class="btn btn-primary font-weight-bold px-4" type="button" id="btnSave" 
                    onclick="ajax_createUpdate('candidates', true);"></button>
            <button class="btn btn-secondary font-weight-bold px-3" type="button" data-dismiss="modal" 
                onclick="$('#formContent').html(null);">
              CLOSE
            </button>
          </div>
        </div>
      </div>
  </div>
</div>

<style>
  /* Lock modal to screen viewport and enable clean inner scrolling */
  .judge-modal .modal-dialog {
    max-width: 700px !important;
    margin: 1.5rem auto !important;
  }

  .judge-modal .modal-content {
    max-height: calc(100vh - 80px) !important;
    display: flex !important;
    flex-direction: column !important;
  }

  .judge-modal .modal-header,
  .judge-modal .modal-footer {
    flex-shrink: 0 !important;
  }

  /* Make category tabs sticky at the top inside the modal body */
  .judge-modal .modal-body {
    overflow-y: auto !important;
    max-height: calc(100vh - 220px) !important;
    padding: 1rem !important;
  }

  /* Lock the nav-tabs to the top of the scrolling body so you don't have to scroll up */
  .judge-modal .modal-body .nav-tabs {
    position: sticky !important;
    top: -1rem !important;
    background: #ffffff !important;
    z-index: 1050 !important;
    padding-top: 0.25rem !important;
    margin-bottom: 1rem !important;
    border-bottom: 2px solid #dee2e6 !important;
  }
</style>