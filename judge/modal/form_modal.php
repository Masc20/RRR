<!-- Form Modal-->
<div class="modal fade" id="formModal" tabindex="-1" role="dialog" 
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

        <div class="modal-footer">
          <h5 class="mr-auto">Total : <u class="text-danger" id="tot"></u> pts</h5>
          <button class="btn btn-primary" type="button" id="btnSave" 
                  onclick="ajax_createUpdate('candidates', true);"></button>
          <button class="btn btn-secondary" type="button" data-dismiss="modal" 
              onclick="$('#formContent').html(null);">
            CLOSE
          </button>
        </div>
      </div>
  </div>
</div>