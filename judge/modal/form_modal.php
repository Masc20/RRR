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