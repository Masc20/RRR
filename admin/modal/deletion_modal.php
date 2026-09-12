<!-- Deletion Modal-->
<div class="modal fade" id="delModal" tabindex="-1" role="dialog" aria-labelledby="delModalLabel" aria-hidden="true"> 
    <div class="modal-dialog formDialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="delModalLabel">Delete Confirmation</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
        	<label>Are you sure ? Do you want to delete <strong id="nameLabel"></strong></label>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary" type="button" id="btnYes">Yes</button>
          <button class="btn btn-secondary" type="button" data-dismiss="modal">No</button>      
        </div>
      </div>
    </div>
</div>