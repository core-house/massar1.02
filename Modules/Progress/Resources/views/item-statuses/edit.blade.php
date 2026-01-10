@extends('progress::layouts.daily-progress')

@section('title', 'Edit Item Status')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Edit Item Status</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('item-statuses.index') }}">Item Statuses</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('item-statuses.update', $itemStatus->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                     <!-- Hidden field for is_active checkbox unchecked state behavior -->
                    <input type="hidden" name="is_active" value="0">

                    <div class="row">
                        <!-- Name -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="las la-tag"></i></span>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $itemStatus->name) }}" required>
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
                                <input type="text" name="color" class="form-control @error('color') is-invalid @enderror" value="{{ old('color', $itemStatus->color) }}" placeholder="Enter status color (e.g., #28a745 or success)">
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
                                <input type="text" name="icon" class="form-control @error('icon') is-invalid @enderror" value="{{ old('icon', $itemStatus->icon) }}" placeholder="Enter (e.g., las la-check-circle)">
                                @error('icon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                             <small class="text-muted">Line Awesome class</small>
                        </div>

                         <!-- Description -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ $itemStatus->description }}</textarea>
                        </div>

                        <!-- Order -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Order</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="las la-sort-amount-up"></i></span>
                                <input type="number" name="order" class="form-control" value="{{ $itemStatus->order }}">
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6 mb-3">
                             <label class="form-label d-block">Status</label>
                            <div class="form-check form-switch form-switch-lg d-flex align-items-center">
                                <input class="form-check-input me-2" type="checkbox" role="switch" id="isActive" name="is_active" value="1" {{ $itemStatus->is_active ? 'checked' : '' }}>
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
