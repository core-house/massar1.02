@extends('admin.dashboard')
@section('sidebar')
    @include('components.sidebar.crm')
@endsection
@section('content')
    @include('components.breadcrumb', [
        'title' => __('crm::crm.client_contacts'),
        'items' => [
            ['label' => __('crm::crm.dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('crm::crm.client_contacts')],
        ],
    ])
    <div class="row">
        <div class="col-lg-12">
            @can('create Client Contacts')
                <a href="{{ route('client-contacts.create') }}" type="button" class="btn btn-main font-hold fw-bold">
                    <i class="fas fa-plus me-2"></i>
                    {{ __('crm::crm.add_new') }}
                </a>
            @endcan
            <br>
            <br>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive" style="overflow-x: auto;">

                        <x-table-export-actions table-id="client-contact-table" filename="client-contact-table"
                            :excel-label="__('crm::crm.export_excel')" :pdf-label="__('crm::crm.export_pdf')" :print-label="__('crm::crm.print')" />

                        <table id="client-contact-table" class="table table-striped mb-0" style="min-width: 1200px;">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('crm::crm.company') }}</th>
                                    <th>{{ __('crm::crm.name') }}</th>
                                    <th>{{ __('crm::crm.email') }}</th>
                                    <th>{{ __('crm::crm.phone') }}</th>
                                    <th>{{ __('crm::crm.position') }}</th>
                                    <th>{{ __('crm::crm.preferred_contact_method') }}</th>
                                    @canany(['edit Client Contacts', 'delete Client Contacts'])
                                        <th>{{ __('crm::crm.actions') }}</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($clientContacts as $contact)
                                    <tr class="text-center">
                                        <td> {{ $loop->iteration }} </td>
                                        <td>{{ $contact->client->cname }}</td>
                                        <td>{{ $contact->name }}</td>
                                        <td>{{ $contact->email }}</td>
                                        <td>{{ $contact->phone }}</td>
                                        <td>{{ $contact->position }}</td>
                                        <td>
                                            @if($contact->preferred_contact_method == 'phone')
                                                <span class="badge bg-primary"><i class="las la-phone"></i> {{ __('crm::crm.phone') }}</span>
                                            @elseif($contact->preferred_contact_method == 'whatsapp')
                                                <span class="badge bg-success"><i class="lab la-whatsapp"></i> {{ __('crm::crm.whatsapp') }}</span>
                                            @elseif($contact->preferred_contact_method == 'email')
                                                <span class="badge bg-info"><i class="las la-envelope"></i> {{ __('crm::crm.email') }}</span>
                                            @endif
                                        </td>
                                        @canany(['edit Client Contacts', 'delete Client Contacts'])
                                            <td>
                                                @can('edit Client Contacts')
                                                    <a class="btn btn-success btn-icon-square-sm"
                                                        href="{{ route('client-contacts.edit', $contact->id) }}">
                                                        <i class="las la-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('delete Client Contacts')
                                                    <form action="{{ route('client-contacts.destroy', $contact->id) }}"
                                                        method="POST" style="display:inline-block;"
                                                        onsubmit="return confirm('{{ __('crm::crm.confirm_delete_contact') }}');">
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
                                        <td colspan="13" class="text-center">
                                            <div class="alert alert-info py-3 mb-0"
                                                style="font-size: 1.2rem; font-weight: 500;">
                                                <i class="las la-info-circle me-2"></i>
                                                {{ __('crm::crm.no_data_available') }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
