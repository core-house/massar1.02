<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white border-0">
                <div class="d-flex align-items-center">
                    <i class="mdi mdi-delete-alert-outline me-2 fs-4"></i>
                    <h5 class="modal-title mb-0">Confirm Delete</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-4">
                    <i class="mdi mdi-delete-alert text-danger" style="font-size: 4rem;"></i>
                </div>
                <h4 class="mb-3">Are you sure you want to delete this CV?</h4>
                <p class="text-muted mb-4">This action cannot be undone. All CV data and associated files will be permanently removed.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-light btn-lg me-3" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-2"></i>Cancel
                </button>
                <button wire:click="confirmDelete" class="btn btn-danger btn-lg" data-bs-dismiss="modal">
                    <i class="mdi mdi-delete me-2"></i>
                    <span wire:loading.remove wire:target="confirmDelete">Delete CV</span>
                    <span wire:loading wire:target="confirmDelete">Deleting...</span>
                </button>
            </div>
        </div>
    </div>
</div>
