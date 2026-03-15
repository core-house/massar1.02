@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.crm')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('crm::crm.return_details'),
        'items' => [
            ['label' => __('crm::crm.dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('crm::crm.returns'), 'url' => route('returns.index')],
            ['label' => __('crm::crm.return_details')],
        ],
    ])

    <div class="row">
        <div class="col-lg-8">
            <!-- Main Return Card -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-opacity-10 border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1 fw-bold">
                                <i class="fas fa-undo-alt me-2"></i>
                                {{ __('crm::crm.returns') }} #{{ $return->return_number }}
                            </h4>
                            <small class="text-muted">
                                <i class="far fa-calendar me-1"></i>
                                {{ $return->return_date->format('Y-m-d') }}
                            </small>
                        </div>
                        <div class="text-end">
                            @php
                                $typeConfig = [
                                    'refund' => ['color' => 'primary', 'icon' => 'fa-money-bill-wave'],
                                    'exchange' => ['color' => 'info', 'icon' => 'fa-exchange-alt'],
                                    'credit_note' => ['color' => 'warning', 'icon' => 'fa-file-invoice'],
                                ];
                                $type = $typeConfig[$return->return_type] ?? $typeConfig['refund'];
                            @endphp
                            <span class="badge bg-{{ $type['color'] }} fs-6">
                                <i class="fas {{ $type['icon'] }} me-1"></i>
                                {{ __(ucfirst(str_replace('_', ' ', $return->return_type))) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('message'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Info Grid -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <i class="fas fa-user fa-2x text-primary me-3"></i>
                                <div>
                                    <small class="text-muted d-block">{{ __('crm::crm.client') }}</small>
                                    <strong>{{ $return->client->cname ?? 'N/A' }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <i
                                    class="fas fa-info-circle fa-2x text-{{ $return->status === 'pending' ? 'warning' : ($return->status === 'approved' ? 'success' : 'danger') }} me-3"></i>
                                <div>
                                    <small class="text-muted d-block">{{ __('crm::crm.status') }}</small>
                                    <span
                                        class="badge bg-{{ $return->status === 'pending' ? 'warning' : ($return->status === 'approved' ? 'success' : ($return->status === 'rejected' ? 'danger' : 'secondary')) }}">
                                        {{ __(ucfirst($return->status)) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <i class="fas fa-user-plus fa-2x text-info me-3"></i>
                                <div>
                                    <small class="text-muted d-block">{{ __('crm::crm.created_by') }}</small>
                                    <strong>{{ $return->createdBy->name ?? 'N/A' }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <i class="fas fa-user-check fa-2x text-success me-3"></i>
                                <div>
                                    <small class="text-muted d-block">{{ __('crm::crm.approved_by') }}</small>
                                    <strong>{{ $return->approvedBy->name ?? __('crm::crm.not_approved_yet') }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($return->original_invoice_number)
                        <div class="alert alert-info border-0">
                            <i class="fas fa-file-invoice me-2"></i>
                            <strong>{{ __('crm::crm.original_invoice_number') }}:</strong> {{ $return->original_invoice_number }}
                            @if ($return->original_invoice_date)
                                <span
                                    class="ms-2 text-muted">({{ $return->original_invoice_date->format('Y-m-d') }})</span>
                            @endif
                        </div>
                    @endif

                    @if ($return->reason)
                        <div class="border-top pt-3 mb-3">
                            <h6 class="fw-bold mb-2">
                                <i class="fas fa-comment-dots me-2 text-primary"></i>
                                {{ __('crm::crm.reason_from_client') }}
                            </h6>
                            <p class="text-muted">{{ $return->reason }}</p>
                        </div>
                    @endif

                    @if ($return->notes)
                        <div class="border-top pt-3 mb-3">
                            <h6 class="fw-bold mb-2">
                                <i class="fas fa-sticky-note me-2 text-warning"></i>
                                {{ __('crm::crm.notes') }}
                            </h6>
                            <p class="text-muted">{{ $return->notes }}</p>
                        </div>
                    @endif

                    {{-- Attachments Section (PDF, Documents) --}}
                    @php
                        $attachments = $return->getMedia('return-attachments');
                    @endphp
                    @if ($attachments->count() > 0)
                        <div class="border-top pt-3 mb-3">
                            <h6 class="fw-bold mb-2">
                                <i class="fas fa-paperclip me-2 text-info"></i>
                                {{ __('crm::crm.attachments') }}
                            </h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($attachments as $media)
                                    <a href="{{ route('returns.download-attachment', ['return' => $return, 'mediaId' => $media->id]) }}" 
                                       class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="las la-download me-1"></i> 
                                        {{ $media->file_name }}
                                        <small class="text-muted">({{ $media->human_readable_size }})</small>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Images Gallery Section --}}
                    @php
                        $images = $return->getMedia('return-images');
                        $imagesData = $images->map(function($img) {
                            return [
                                'url' => $img->getUrl(),
                                'thumb' => $img->getUrl('thumb')
                            ];
                        })->values()->toArray();
                    @endphp
                    
                    {{-- Always show the images section --}}
                    <div class="border-top pt-3 mb-3">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-images me-2 text-success"></i>
                            {{ __('crm::crm.return_images') }} 
                            @if($images->count() > 0)
                                ({{ $images->count() }})
                            @endif
                        </h6>
                        
                        @if ($images->count() > 0)
                            {{-- Show images if they exist --}}
                            <div x-data="imageGallery()" 
                                 x-init="init(@js($imagesData))"
                                 x-cloak>
                            
                            {{-- Thumbnails Grid --}}
                            <div class="row g-3">
                                @foreach ($images as $index => $image)
                                    <div class="col-lg-2 col-md-3 col-4">
                                        <div class="position-relative overflow-hidden rounded-3 shadow-sm" 
                                             style="cursor: pointer; aspect-ratio: 1/1;"
                                             @click="openModal({{ $index }})">
                                            <img src="{{ $image->getUrl('thumb') }}" 
                                                 alt="Return image {{ $index + 1 }}" 
                                                 class="w-100 h-100 hover-zoom"
                                                 style="object-fit: cover; transition: all 0.3s ease;">
                                            
                                            {{-- Overlay on hover --}}
                                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
                                                 style="background: rgba(0,0,0,0); transition: background 0.3s ease;"
                                                 onmouseover="this.style.background='rgba(0,0,0,0.5)'"
                                                 onmouseout="this.style.background='rgba(0,0,0,0)'">
                                                <i class="las la-search-plus text-white" style="font-size: 2rem; opacity: 0; transition: opacity 0.3s ease;"
                                                   onmouseover="this.style.opacity='1'"
                                                   onmouseout="this.style.opacity='0'"></i>
                                            </div>
                                            
                                            {{-- Image number badge --}}
                                            <div class="position-absolute top-0 start-0 m-2">
                                                <span class="badge bg-dark bg-opacity-75 rounded-pill px-2">
                                                    {{ $index + 1 }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Lightbox Modal --}}
                            <template x-if="showModal">
                                <div x-cloak
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0"
                                     x-transition:enter-end="opacity-100"
                                     x-transition:leave="transition ease-in duration-200"
                                     x-transition:leave-start="opacity-100"
                                     x-transition:leave-end="opacity-0"
                                     @keydown.escape.window="closeModal()"
                                     @keydown.arrow-left.window="previousImage()"
                                     @keydown.arrow-right.window="nextImage()"
                                     class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" 
                                     style="background: rgba(0,0,0,0.95); z-index: 9999; backdrop-filter: blur(5px);"
                                     @click.self="closeModal()">
                                
                                <div class="position-relative w-100 h-100 d-flex align-items-center justify-content-center p-4">
                                    {{-- Close Button --}}
                                    <button @click="closeModal()" 
                                            class="btn btn-light rounded-circle position-absolute"
                                            style="top: 20px; right: 20px; z-index: 10001; width: 50px; height: 50px; box-shadow: 0 4px 12px rgba(0,0,0,0.3);"
                                            title="{{ __('Close') }} (ESC)">
                                        <i class="las la-times la-2x"></i>
                                    </button>

                                    {{-- Image Counter --}}
                                    <div class="position-absolute" style="top: 20px; left: 50%; transform: translateX(-50%); z-index: 10001;">
                                        <span class="badge bg-dark px-4 py-2 fs-5 rounded-pill shadow" 
                                              style="backdrop-filter: blur(10px); background: rgba(0,0,0,0.7) !important;"
                                              x-text="getCounterText()"></span>
                                    </div>

                                    {{-- Navigation Buttons (only show if more than 1 image) --}}
                                    <template x-if="images.length > 1">
                                        <div class="position-absolute bottom-0 start-50 translate-middle-x mb-5" 
                                             style="z-index: 10001; display: flex; gap: 12px; align-items: center;">
                                            <button @click="previousImage()" 
                                                    class="btn btn-light rounded-circle nav-btn-cute"
                                                    style="width: 50px; height: 50px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); border: 2px solid rgba(255,255,255,0.95); padding: 0; display: flex; align-items: center; justify-content: center;"
                                                    title="{{ __('Previous') }} (←)">
                                                <i class="las la-chevron-left" style="font-size: 1.8rem; color: #0d6efd; margin-right: 2px;"></i>
                                            </button>

                                            {{-- Thumbnail Navigation Carousel --}}
                                            <div class="d-flex gap-2 bg-dark bg-opacity-75 rounded-pill px-3 py-2 shadow-lg overflow-auto" 
                                                 style="backdrop-filter: blur(10px); max-width: 500px; justify-content: center;">
                                                <template x-for="(img, index) in images" :key="index">
                                                    <div @click="goToImage(index)" 
                                                         class="rounded overflow-hidden flex-shrink-0 thumbnail-cute"
                                                         style="cursor: pointer; width: 50px; height: 50px; transition: all 0.3s ease;"
                                                         :class="currentIndex === index ? 'ring ring-primary ring-2' : 'opacity-60 hover:opacity-100'">
                                                        <img :src="img.thumb" 
                                                             class="w-100 h-100" 
                                                             style="object-fit: cover;"
                                                             :alt="'Thumbnail ' + (index + 1)">
                                                    </div>
                                                </template>
                                            </div>

                                            <button @click="nextImage()" 
                                                    class="btn btn-light rounded-circle nav-btn-cute"
                                                    style="width: 50px; height: 50px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); border: 2px solid rgba(255,255,255,0.95); padding: 0; display: flex; align-items: center; justify-content: center;"
                                                    title="{{ __('Next') }} (→)">
                                                <i class="las la-chevron-right" style="font-size: 1.8rem; color: #0d6efd; margin-left: 2px;"></i>
                                            </button>
                                        </div>
                                    </template>

                                    {{-- Main Image Container --}}
                                    <div class="text-center" style="max-width: 90%; max-height: 85vh;">
                                        <img :src="currentImage" 
                                             :key="currentIndex"
                                             class="img-fluid rounded-3 shadow-lg"
                                             style="max-height: 85vh; max-width: 100%; object-fit: contain;"
                                             alt="Return image"
                                             x-transition:enter="transition ease-out duration-300"
                                             x-transition:enter-start="opacity-0 transform scale-95"
                                             x-transition:enter-end="opacity-100 transform scale-100">
                                    </div>

                                    {{-- Bottom Actions Bar --}}
                                    <div class="position-absolute" 
                                         style="bottom: 20px; left: 20px; z-index: 10001;">
                                        <div class="d-flex gap-2 align-items-center bg-dark bg-opacity-75 rounded-pill px-4 py-3 shadow-lg" 
                                             style="backdrop-filter: blur(10px);">
                                            {{-- Download Button --}}
                                            <a :href="currentImage" 
                                               download 
                                               class="btn btn-primary rounded-pill px-4"
                                               title="{{ __('crm::crm.download') }}">
                                                <i class="las la-download me-2"></i>
                                                <span>{{ __('crm::crm.download') }}</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            </div>
                        @else
                            {{-- Show placeholder when no images --}}
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="fas fa-image fa-3x text-muted opacity-50"></i>
                                </div>
                                <p class="text-muted mb-3">{{ __('No images uploaded for this return') }}</p>
                                @can('edit Returns')
                                    @if($return->status === 'pending')
                                        <a href="{{ route('returns.edit', $return) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-plus me-1"></i>
                                            {{ __('Add Images') }}
                                        </a>
                                    @endif
                                @endcan
                            </div>
                        @endif
                    </div>
                                    @if($return->status === 'pending')
                                        <a href="{{ route('returns.edit', $return) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-plus me-1"></i>
                                            {{ __('Add Images') }}
                                        </a>
                                    @endif
                                @endcan
                            </div>
                        @endif
                    </div>

                    @can('edit Returns')
                        <div class="d-flex gap-2 mt-4">
                            <a href="{{ route('returns.edit', $return) }}" class="btn btn-primary">
                                <i class="las la-edit me-1"></i> {{ __('crm::crm.edit_return') }}
                            </a>
                            @can('delete Returns')
                                <form action="{{ route('returns.destroy', $return) }}" method="POST"
                                    onsubmit="return confirm('{{ __('crm::crm.confirm_delete_return') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="las la-trash me-1"></i> {{ __('crm::crm.delete') }}
                                    </button>
                                </form>
                            @endcan
                        </div>
                    @endcan
                </div>
            </div>

            <!-- Items Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-box me-2 text-primary"></i>
                        {{ __('crm::crm.return_items') }}
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>{{ __('crm::crm.item') }}</th>
                                    <th class="text-center">{{ __('crm::crm.quantity') }}</th>
                                    <th class="text-end">{{ __('crm::crm.unit_price') }}</th>
                                    <th class="text-end">{{ __('crm::crm.total') }}</th>
                                    <th>{{ __('crm::crm.condition') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($return->items as $item)
                                    <tr>
                                        <td class="text-muted">{{ $loop->iteration }}</td>
                                        <td>
                                            <i class="fas fa-cube me-2 text-muted"></i>
                                            <strong>{{ $item->item->name ?? 'N/A' }}</strong>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ $item->quantity }}</span>
                                        </td>
                                        <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                                        <td class="text-end">
                                            <strong
                                                class="text-primary">{{ number_format($item->total_price, 2) }}</strong>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $item->item_condition ?? '-' }}</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="4" class="text-end"><strong>{{ __('crm::crm.total_amount') }}:</strong></td>
                                    <td class="text-end">
                                        <h5 class="mb-0 text-primary fw-bold">{{ number_format($return->total_amount, 2) }}
                                        </h5>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-tasks me-2 text-primary"></i>
                        {{ __('crm::crm.actions') }}
                    </h5>
                </div>
                <div class="card-body">
                    @if ($return->status === 'pending')
                        @can('edit Returns')
                            <div class="d-grid gap-2">
                                <form action="{{ route('returns.approve', $return) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="las la-check-circle me-1"></i> {{ __('crm::crm.approve_return') }}
                                    </button>
                                </form>
                                <form action="{{ route('returns.reject', $return) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="las la-times-circle me-1"></i> {{ __('crm::crm.reject_return') }}
                                    </button>
                                </form>
                            </div>
                        @endcan
                    @else
                        <div class="alert alert-{{ $return->status === 'approved' ? 'success' : 'danger' }} border-0">
                            <i class="fas fa-{{ $return->status === 'approved' ? 'check' : 'times' }}-circle me-2"></i>
                            {{ __('This return has been') }} <strong>{{ __(strtolower($return->status)) }}</strong>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-opacity-10 {
            --bs-bg-opacity: 0.1;
        }

        .hover-zoom {
            transition: transform 0.2s ease-in-out;
        }

        .hover-zoom:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        /* Fix modal backdrop */
        .modal.show {
            display: block !important;
        }
        
        /* Lightbox fade in animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        /* Smooth button hover effects */
        .btn-light:hover {
            transform: scale(1.1);
            transition: transform 0.2s ease;
        }

        /* Ring utility for active thumbnail */
        .ring {
            outline: 2px solid currentColor;
            outline-offset: 2px;
        }

        .ring-primary {
            outline-color: #0d6efd;
        }

        .ring-3 {
            outline-width: 3px;
        }
    </style>

    <script>
        function imageGallery() {
            return {
                showModal: false,
                currentImage: '',
                currentIndex: 0,
                images: [],

                init(imagesData) {
                    this.images = imagesData;
                    // Initialize but don't open modal
                    this.showModal = false;
                    this.currentIndex = 0;
                    if (this.images.length > 0) {
                        this.currentImage = this.images[0].url;
                    }
                },

                openModal(index) {
                    this.currentIndex = index;
                    this.currentImage = this.images[index].url;
                    this.showModal = true;
                    document.body.style.overflow = 'hidden';
                },

                closeModal() {
                    this.showModal = false;
                    document.body.style.overflow = 'auto';
                },

                nextImage() {
                    if (this.images.length > 1) {
                        this.currentIndex = (this.currentIndex + 1) % this.images.length;
                        this.currentImage = this.images[this.currentIndex].url;
                    }
                },

                previousImage() {
                    if (this.images.length > 1) {
                        this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
                        this.currentImage = this.images[this.currentIndex].url;
                    }
                },

                goToImage(index) {
                    this.currentIndex = index;
                    this.currentImage = this.images[index].url;
                },

                getCounterText() {
                    return `${this.currentIndex + 1} / ${this.images.length}`;
                }
            }
        }
    </script>

    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: '{{ __('Error') }}',
                    text: '{{ session('error') }}',
                    confirmButtonText: '{{ __('OK') }}'
                });
            });
        </script>
    @endif
@endsection
