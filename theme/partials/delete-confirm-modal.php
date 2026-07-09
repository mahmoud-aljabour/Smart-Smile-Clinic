<div class="modal fade delete-confirm-modal" id="globalDeleteModal" tabindex="-1" aria-labelledby="globalDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-0 text-center">
        <div class="delete-confirm-icon">
          <i class="bi bi-exclamation-triangle"></i>
        </div>
        <h5 class="modal-title mb-2" id="globalDeleteModalLabel">Confirm Delete?</h5>
        <p class="text-muted small mb-3" id="globalDeleteMessage">Are you sure you want to delete this item? This action cannot be undone.</p>
        <dl class="delete-confirm-details text-start d-none" id="globalDeleteDetails"></dl>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          <i class="bi bi-x-lg"></i> Cancel
        </button>
        <button type="button" id="globalDeleteConfirmBtn" class="btn btn-danger">
          <i class="bi bi-trash"></i> <span id="globalDeleteConfirmText">Delete</span>
        </button>
      </div>
    </div>
  </div>
</div>
