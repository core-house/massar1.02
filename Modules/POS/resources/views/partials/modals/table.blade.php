{{-- Table Selection Modal --}}
<div class="modal fade" id="tableModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">اختيار الطاولة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    @for($i = 1; $i <= 20; $i++)
                        <div class="col-md-3 col-sm-4 col-6">
                            <button type="button" 
                                    class="btn btn-outline-primary w-100 table-btn"
                                    data-table="{{ $i }}"
                                    style="height: 80px; border-radius: 10px;">
                                <i class="fas fa-table d-block mb-2"></i>
                                طاولة {{ $i }}
                            </button>
                        </div>
                    @endfor
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
            </div>
        </div>
    </div>
</div>
