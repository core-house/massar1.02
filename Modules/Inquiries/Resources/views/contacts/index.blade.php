@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.inquiries')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('inquiries::inquiries.contacts'),
        'breadcrumb_items' => [
            ['label' => __('inquiries::inquiries.dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('inquiries::inquiries.contacts')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            @can('create Contacts')
                <a href="{{ route('contacts.create') }}" type="button" class="btn btn-main font-hold fw-bold">
                    <i class="fas fa-plus me-2"></i>
                    {{ __('inquiries::inquiries.add_new_contact') }}
                </a>
            @endcan
            <br><br>
            <div class="card">
                <div class="card-body">
                    <x-inquiries::bulk-actions model="Modules\Inquiries\Models\Contact" permission="delete Inquiries">
                        <div class="table-responsive" style="overflow-x: auto;">

                            <x-table-export-actions table-id="contacts-table" filename="contacts-table"
                                excel-label="{{ __('inquiries::inquiries.export_excel') }}"
                                pdf-label="{{ __('inquiries::inquiries.export_pdf') }}"
                                print-label="{{ __('inquiries::inquiries.print') }}" />

                            <table id="contacts-table" class="table table-striped mb-0" style="min-width: 1200px;">
                                <thead class="table-light text-center align-middle">
                                    <tr>
                                        <th>
                                            <input type="checkbox" class="form-check-input" x-model="selectAll" @change="toggleAll">
                                        </th>
                                        <th>#</th>
                                        <th>{{ __('inquiries::inquiries.name') }}</th>
                                        <th>{{ __('inquiries::inquiries.type') }}</th>
                                        <th>{{ __('inquiries::inquiries.email') }}</th>
                                        <th>{{ __('inquiries::inquiries.phone') }}</th>
                                        <th>{{ __('inquiries::inquiries.main_role') }}</th>
                                        <th>{{ __('inquiries::inquiries.parent_contact') }}</th>
                                        @canany(['edit Contacts', 'delete Contacts'])
                                            <th>{{ __('inquiries::inquiries.actions') }}</th>
                                        @endcanany
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($contacts as $contact)
                                        <tr class="text-center">
                                            <td>
                                                <input type="checkbox" class="form-check-input bulk-checkbox"
                                                    value="{{ $contact->id }}" x-model="selectedIds">
                                            </td>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $contact->name }}</td>
                                            <td>
                                                @if ($contact->type === 'person')
                                                    <span class="badge badge-info">{{ __('inquiries::inquiries.person') }}</span>
                                                @else
                                                    <span class="badge badge-success">{{ __('inquiries::inquiries.company') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $contact->email ?? '-' }}</td>
                                            <td>{{ $contact->phone_1 ?? '-' }}</td>
                                            <td>{{ $contact->role->name ?? '-' }}</td>
                                            <td>{{ $contact->parent->name ?? '-' }}</td>
                                            @canany(['edit Contacts', 'delete Contacts'])
                                                <td>
                                                    @can('edit Contacts')
                                                        <a class="btn btn-success btn-icon-square-sm"
                                                            href="{{ route('contacts.edit', $contact->id) }}">
                                                            <i class="las la-edit"></i>
                                                        </a>
                                                    @endcan

                                                    @can('delete Contacts')
                                                        <form action="{{ route('contacts.destroy', $contact->id) }}" method="POST"
                                                            style="display:inline-block;"
                                                            onsubmit="return confirm('{{ __('inquiries::inquiries.confirm_delete') }}');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-icon-square-sm">
                                                                <i class="las la-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </td>
                                            @endcanany
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center">
                                                <div class="alert alert-info py-3 mb-0"
                                                    style="font-size: 1.2rem; font-weight: 500;">
                                                    <i class="las la-info-circle me-2"></i>
                                                    {{ __('inquiries::inquiries.no_data_available') }}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </x-inquiries::bulk-actions>
                </div>
            </div>
        </div>
    </div>
@endsection
