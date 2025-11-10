    @extends('admin.dashboard')

    {{-- Dynamic Sidebar --}}
    @section('sidebar')
        @include('components.sidebar.manufacturing')
    @endsection

    {{-- @section('content')
        <livewire:edit-manufacturing-invoice :invoiceId="$id" />
    @endsection --}}


    @section('content')
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-warning text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="mb-0">
                                    <i class="fas fa-edit me-2"></i>
                                    تعديل فاتورة التصنيع
                                </h4>
                                <a href="{{ route('manufacturing.index') }}" class="btn btn-light btn-sm">
                                    <i class="fas fa-arrow-left me-1"></i> رجوع للقائمة
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @livewire('edit-manufacturing-invoice', ['invoiceId' => $id])
        </div>
    @endsection
