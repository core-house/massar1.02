@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.inquiries')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('Contacts'),
        'items' => [['label' => __('Dashboard'), 'url' => route('admin.dashboard')], ['label' => __('Contacts')]],
    ])

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Contacts') }}</h3>
            @can('create Contacts')
                <div class="card-tools">
                    <a href="{{ route('contacts.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> {{ __('Add New') }}
                    </a>
                </div>
            @endcan
        </div>

        <div class="card-body">
            @if ($contacts->count() > 0)
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th>{{ __('Main Role') }}</th>
                            <th>{{ __('Parent Contact') }}</th>
                            @canany(['edit Contacts', 'delete Contacts'])
                                <th>{{ __('Actions') }}</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($contacts as $contact)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $contact->name }}</td>
                                <td>
                                    @if ($contact->type === 'person')
                                        <span class="badge badge-info">{{ __('Person') }}</span>
                                    @else
                                        <span class="badge badge-success">{{ __('Company') }}</span>
                                    @endif
                                </td>
                                <td>{{ $contact->email ?? '-' }}</td>
                                <td>{{ $contact->phone_1 ?? '-' }}</td>
                                <td>{{ $contact->role->name ?? '-' }}</td>
                                <td>{{ $contact->parent->name ?? '-' }}</td>
                                @canany(['edit Contacts', 'delete Contacts'])
                                    <td>
                                        @can('edit Contacts')
                                            <a href="{{ route('contacts.edit', $contact->id) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan

                                        @can('delete Contacts')
                                            <form action="{{ route('contacts.destroy', $contact->id) }}" method="POST"
                                                style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('{{ __('Are you sure you want to delete?') }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                @endcanany
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert alert-info text-center">
                    {{ __('No data available') }}
                </div>
            @endif
        </div>
    </div>
@endsection
