@extends('progress::layouts.daily-progress')

@section('title', 'Add Item Status')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Add Item Status</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('item-statuses.index') }}">Item Statuses</a></li>
                    <li class="breadcrumb-item active">Add</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('item-statuses.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <!-- Name -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="las la-tag"></i></span>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Enter status name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Color -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Color</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="las la-palette"></i></span>
                                <input type="text" name="color" class="form-control @error('color') is-invalid @enderror" placeholder="Enter status color (e.g., #28a745 or success)" value="{{ old('color') }}">
                                @error('color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Hex color (#RRGGBB) or Bootstrap class (success, primary, etc.)</small>
                        </div>

                        <!-- Icon -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Icon</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="las la-icons"></i></span>
                                <input type="text" name="icon" class="form-control @error('icon') is-invalid @enderror" placeholder="Enter (e.g., las la-check-circle)" value="{{ old('icon') }}">
                                @error('icon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                             <small class="text-muted">Line Awesome class</small>
                        </div>

                        <!-- Description -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Enter description">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Order -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Order</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="las la-sort-amount-up"></i></span>
                                <input type="number" name="order" class="form-control @error('order') is-invalid @enderror" value="{{ old('order', 0) }}">
                                @error('order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label d-block">Status</label>
                            <div class="form-check form-switch form-switch-lg d-flex align-items-center">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input me-2" type="checkbox" role="switch" id="isActive" name="is_active" value="1" checked>
                                <label class="form-check-label" for="isActive">Active</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end gap-2">
                         <a href="{{ route('item-statuses.index') }}" class="btn btn-secondary"><i class="las la-times"></i> Cancel</a>
                        <button type="submit" class="btn btn-primary"><i class="las la-save"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
