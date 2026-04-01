@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.inquiries')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('inquiries::inquiries.add_inquiry_source'),
        'breadcrumb_items' => [
            ['label' => __('inquiries::inquiries.home'), 'url' => route('admin.dashboard')],
            ['label' => __('inquiries::inquiries.inquiry_sources'), 'url' => route('inquiry.sources.index')],
            ['label' => __('Add')],
        ],
    ])

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f8f9fa;
        }

        .tree-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            height: fit-content;
        }

        .tree-item {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 15px;
            margin: 8px 0;
            background: #fff;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .tree-item:hover {
            border-color: #007bff;
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.2);
        }

        .tree-item.active {
            border-color: #007bff;
            background: #e7f3ff;
        }

        .tree-item.selected {
            border-color: #28a745;
            background: #d4edda;
        }

        .tree-children {
            margin-right: 25px;
            margin-top: 10px;
            border-right: 2px dashed #dee2e6;
            padding-right: 15px;
        }

        .add-child-btn {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: #28a745;
            border: none;
            color: white;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .tree-item:hover .add-child-btn {
            opacity: 1;
        }

        .inline-form {
            background: #f8f9fa;
            border: 2px dashed #007bff;
            border-radius: 8px;
            padding: 15px;
            margin: 8px 0;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .table-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .form-check-input {
            cursor: pointer;
            width: 2.3rem;
            height: 1.2rem;
            margin: 0 !important;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .breadcrumb-item {
            background: #e9ecef;
            padding: 5px 10px;
            border-radius: 15px;
            margin: 2px;
            font-size: 12px;
        }

        .edit-form {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 8px;
            padding: 15px;
            margin: 8px 0;
            animation: fadeIn 0.3s ease;
        }
    </style>
    <div class="container-fluid mt-4">

        <div class="mb-3" id="pathDisplay" style="display: none;">
            <div class="d-flex align-items-center flex-wrap">
                <strong class="me-2">{{ __('inquiries::inquiries.current_path') }}</strong>
                <div id="pathBreadcrumb"></div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="tree-container">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="fas fa-sitemap me-2"></i>{{ __('inquiries::inquiries.source_tree') }}
                        </h5>
                        <button class="btn btn-primary btn-sm" onclick="addRootSource()">
                            <i class="fas fa-plus me-1"></i>{{ __('inquiries::inquiries.add_main_source') }}
                        </button>
                    </div>

                    <div id="treeContainer">
                        <!-- Tree will be generated here -->
                    </div>
                </div>
            </div>

            <!-- Table View -->
            <div class="col-lg-6">
                <x-inquiries::bulk-actions model="Modules\Inquiries\Models\InquirySource"
                    permission="delete Inquiries Source">
                    <div class="table-container position-relative" style="margin-top: 25px;">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>
                                            <input type="checkbox" class="form-check-input" x-model="selectAll"
                                                @change="toggleAll">
                                        </th>
                                        <th class="text-center">#</th>
                                        <th>{{ __('inquiries::inquiries.name') }}</th>
                                        <th>{{ __('inquiries::inquiries.level') }}</th>
                                        <th>{{ __('inquiries::inquiries.status') }}</th>
                                        <th>{{ __('inquiries::inquiries.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody" class="text-center">
                                    <!-- Table content will be generated here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
            </div>
        </div>
    </div>

    <script>
        let sourcesData = @json($sourcesTree);
        let selectedSource = null;
        let editingSource = null;

        function renderTree() {
            const container = document.getElementById('treeContainer');
            container.innerHTML = '';
            sourcesData.forEach(source => {
                container.appendChild(createTreeItem(source, 0));
            });
            renderTable();
        }

        function createTreeItem(source, level) {
            const item = document.createElement('div');
            item.className = 'tree-item';
            item.dataset.id = source.id;

            if (selectedSource && selectedSource.id === source.id) {
                item.classList.add('selected');
            }

            item.innerHTML = `
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <i class="fas fa-${source.children.length > 0 ? 'folder' : 'file'} me-2"></i>
                <span class="source-name">${source.name}</span>
                ${source.is_active ?
                    '<span class="status-badge status-active ms-2">{{ __('inquiries::inquiries.active') }}</span>' :
                    '<span class="status-badge status-inactive ms-2">{{ __('inquiries::inquiries.inactive') }}</span>'
                }
            </div>
            <div class="action-buttons">
                @can('create Inquiries Source')
                    <button class="add-child-btn" onclick="addChild(${source.id})" title="{{ __('inquiries::inquiries.add_branch') }}">
                        <i class="fas fa-plus"></i>
                    </button>
                @endcan
            </div>
        </div>
        `;

            item.addEventListener('click', (e) => {
                if (!e.target.closest('.add-child-btn')) {
                    selectSource(source);
                    updatePath(source);
                }
            });

            const container = document.createElement('div');
            container.appendChild(item);

            if (source.children && source.children.length > 0) {
                const childrenContainer = document.createElement('div');
                childrenContainer.className = 'tree-children';

                source.children.forEach(child => {
                    childrenContainer.appendChild(createTreeItem(child, level + 1));
                });

                container.appendChild(childrenContainer);
            }

            return container;
        }

        function selectSource(source) {
            selectedSource = source;
            document.querySelectorAll('.tree-item.selected').forEach(item => item.classList.remove('selected'));
            document.querySelector(`[data-id="${source.id}"]`).classList.add('selected');
        }

        function updatePath(source) {
            const path = getSourcePath(source);
            const pathDisplay = document.getElementById('pathDisplay');
            const pathBreadcrumb = document.getElementById('pathBreadcrumb');

            if (path.length > 1) {
                pathDisplay.style.display = 'block';
                pathBreadcrumb.innerHTML = path.map((item) => {
                    return `<span class="breadcrumb-item">${item.name}</span>`;
                }).join('<i class="fas fa-chevron-left mx-2"></i>');
            } else {
                pathDisplay.style.display = 'none';
            }
        }

        function getSourcePath(source) {
            const path = [source];
            let current = source;
            while (current.parent_id) {
                const parent = findSourceById(current.parent_id);
                if (parent) {
                    path.unshift(parent);
                    current = parent;
                } else break;
            }
            return path;
        }

        function findSourceById(id, sources = sourcesData) {
            for (const source of sources) {
                if (source.id === id) return source;
                if (source.children) {
                    const found = findSourceById(id, source.children);
                    if (found) return found;
                }
            }
            return null;
        }

        function addRootSource() {
            showInlineForm(null);
        }

        function addChild(parentId) {
            event.stopPropagation();
            const parent = findSourceById(parentId);
            showInlineForm(parent);
        }

        function showInlineForm(parent) {
            document.querySelectorAll('.inline-form, .edit-form').forEach(form => form.remove());

            const form = document.createElement('div');
            form.className = 'inline-form';
            form.innerHTML = `
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">{{ __('inquiries::inquiries.source_name') }}</label>
                <input type="text" class="form-control" id="newSourceName" placeholder="{{ __('inquiries::inquiries.enter_source_name') }}">
            </div>
            <div class="col-md-3 d-flex justify-content-center align-items-center">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="newSourceStatus" checked>
                    <label class="form-check-label ms-2" for="newSourceStatus">{{ __('inquiries::inquiries.status') }}</label>
                </div>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-success btn-sm me-2" onclick="saveNewSource(${parent ? parent.id : null})">
                    <i class="fas fa-check me-1"></i>{{ __('inquiries::inquiries.save') }}
                </button>
                <button class="btn btn-danger btn-sm" onclick="cancelForm()">
                    <i class="fas fa-times me-1"></i>{{ __('inquiries::inquiries.cancel') }}
                </button>
            </div>
        </div>
        ${parent ? `<small class="text-muted">{{ __('inquiries::inquiries.will_be_added_as_branch_of') }}: ${parent.name}</small>` : ''}
        `;

            if (parent) {
                const parentElement = document.querySelector(`[data-id="${parent.id}"]`).closest('div');
                const childrenContainer = parentElement.querySelector('.tree-children') ||
                    (() => {
                        const container = document.createElement('div');
                        container.className = 'tree-children';
                        parentElement.appendChild(container);
                        return container;
                    })();
                childrenContainer.appendChild(form);
            } else {
                document.getElementById('treeContainer').insertBefore(form, document.getElementById('treeContainer')
                    .firstChild);
            }

            document.getElementById('newSourceName').focus();
        }

        async function saveNewSource(parentId) {
            const name = document.getElementById('newSourceName').value.trim();
            const isActive = document.getElementById('newSourceStatus').checked;
            if (!name) {
                alert('{{ __('inquiries::inquiries.enter_source_name') }}');
                return;
            }

            const formData = {
                name: name,
                parent_id: parentId,
                is_active: isActive,
                _token: '{{ csrf_token() }}'
            };

            try {
                const response = await fetch('{{ route('inquiry.sources.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(formData)
                });
                const result = await response.json();
                if (result.success) {
                    showToast(result.message, 'success');
                    await fetchSources();
                    renderTree();
                } else {
                    showToast(result.message, 'error');
                }
            } catch (error) {
                showToast('{{ __('inquiries::inquiries.unexpected_error') }}', 'error');
            }

            cancelForm();
        }

        async function fetchSources() {
            try {
                const response = await fetch('/inquiry-sources/tree');
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const result = await response.json();
                if (result.success) sourcesData = result.data;
                else throw new Error(result.message || '{{ __('inquiries::inquiries.no_data_available') }}');
            } catch (error) {
                showToast('{{ __('inquiries::inquiries.no_data_available') }}: ' + error.message, 'error');
            }
        }

        function cancelForm() {
            document.querySelectorAll('.inline-form, .edit-form').forEach(form => form.remove());
        }

        function editSource(sourceId) {
            const source = findSourceById(sourceId);
            if (!source) return;
            editingSource = source;
            document.querySelectorAll('.inline-form, .edit-form').forEach(form => form.remove());

            const sourceElement = document.querySelector(`[data-id="${sourceId}"]`);
            const form = document.createElement('div');
            form.className = 'edit-form';
            form.innerHTML = `
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">{{ __('inquiries::inquiries.source_name') }}</label>
                <input type="text" class="form-control" id="editSourceName" value="${source.name}">
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('inquiries::inquiries.status') }}</label>
                <select class="form-select" id="editSourceStatus">
                    <option value="true" ${source.is_active ? 'selected' : ''}>{{ __('inquiries::inquiries.active') }}</option>
                    <option value="false" ${!source.is_active ? 'selected' : ''}>{{ __('inquiries::inquiries.inactive') }}</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-warning btn-sm me-2" onclick="updateSource()">
                    <i class="fas fa-save me-1"></i>{{ __('inquiries::inquiries.update') }}
                </button>
                <button class="btn btn-danger btn-sm" onclick="cancelForm()">
                    <i class="fas fa-times me-1"></i>{{ __('inquiries::inquiries.cancel') }}
                </button>
            </div>
        </div>
        `;
            sourceElement.parentNode.insertBefore(form, sourceElement.nextSibling);
            document.getElementById('editSourceName').focus();
        }

        async function updateSource() {
            if (!editingSource) return;
            const name = document.getElementById('editSourceName').value.trim();
            const isActive = document.getElementById('editSourceStatus').value === 'true';
            if (!name) {
                alert('{{ __('inquiries::inquiries.enter_source_name') }}');
                return;
            }
            const formData = {
                name,
                is_active: isActive,
                _token: '{{ csrf_token() }}'
            };
            const url = '{{ route('inquiry.sources.update', ['inquiry_source' => ':id']) }}'.replace(':id',
                editingSource.id);

            try {
                const response = await fetch(url, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(formData)
                });
                const result = await response.json();
                if (result.success) {
                    showToast(result.message, 'success');
                    await fetchSources();
                    renderTree();
                } else {
                    showToast(result.message || '{{ __('inquiries::inquiries.failed_to_update') }}', 'error');
                }
            } catch (error) {
                showToast('{{ __('inquiries::inquiries.unexpected_error') }}', 'error');
            }
            cancelForm();
            editingSource = null;
        }

        async function deleteSource(sourceId) {
            if (!confirm('{{ __('inquiries::inquiries.confirm_delete_source') }}'))
                return;
            const url = '{{ route('inquiry.sources.destroy', ['inquiry_source' => ':id']) }}'.replace(':id', sourceId);

            try {
                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const result = await response.json();
                if (result.success) {
                    showToast(result.message, 'success');
                    await fetchSources();
                    renderTree();
                } else {
                    showToast(result.message || '{{ __('inquiries::inquiries.failed_to_delete') }}', 'error');
                }
            } catch (error) {
                showToast('{{ __('inquiries::inquiries.unexpected_error') }}', 'error');
            }
        }

        function renderTable() {
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = '';
            let counter = 1;

            function addToTable(sources, level = 0) {
                sources.forEach(source => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                <td class="text-center">
                    <input type="checkbox" class="form-check-input bulk-checkbox" 
                           value="${source.id}" x-model="selectedIds">
                </td>
                <td class="text-center">${counter++}</td>
                <td>
                    ${'—'.repeat(level)}
                    <i class="fas fa-${source.children.length > 0 ? 'folder' : 'file'} me-1"></i>
                    ${source.name}
                </td>
                <td>
                    <span class="badge bg-info">{{ __('inquiries::inquiries.level') }} ${level + 1}</span>
                </td>
                <td class="text-center align-middle">
                    <div class="d-flex justify-content-center align-items-center" style="height: 100%;">
                        <div class="form-check form-switch m-0">
                            @can('edit Inquiries Source')
                                <input class="form-check-input toggle-status" type="checkbox"
                                    data-id="${source.id}" ${source.is_active ? 'checked' : ''}>
                            @else
                                    <span class="status-badge ${source.is_active ? 'status-active' : 'status-inactive'}">
                                        ${source.is_active ? '{{ __('inquiries::inquiries.active') }}' : '{{ __('inquiries::inquiries.inactive') }}'}
                                    </span>
                            @endcan
                        </div>
                    </div>
                </td>
                <td>
                    @can('edit Inquiries Source')
                        <button class="btn btn-success btn-sm me-1" onclick="editSource(${source.id})" title="{{ __('inquiries::inquiries.edit') }}">
                            <i class="fas fa-edit"></i>
                        </button>
                    @endcan

                    @can('delete Inquiries Source')
                        <button class="btn btn-danger btn-sm" onclick="deleteSource(${source.id})" title="{{ __('inquiries::inquiries.delete') }}">
                            <i class="fas fa-trash"></i>
                        </button>
                    @endcan
                </td>
                `;
                    tbody.appendChild(row);
                    if (source.children.length > 0) addToTable(source.children, level + 1);
                });
            }

            addToTable(sourcesData);
            window.dispatchEvent(new CustomEvent('content-changed'));
        }

        function showToast(message, type) {
            const toast = document.createElement('div');
            toast.className =
                `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} position-fixed`;
            toast.style.cssText = 'top: 20px; left: 20px; z-index: 9999; min-width: 300px;';
            toast.innerHTML =
                `<i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>${message}`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        document.addEventListener('DOMContentLoaded', () => renderTree());

        document.addEventListener('change', async (e) => {
            if (e.target.classList.contains('toggle-status')) {
                const sourceId = e.target.dataset.id;
                try {
                    const response = await fetch(`/inquiry-sources/${sourceId}/toggle-status`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const result = await response.json();
                    if (result.success) {
                        showToast('{{ __('inquiries::inquiries.status_changed_successfully') }}', 'success');
                        await fetchSources();
                        renderTree();
                    } else {
                        showToast('{{ __('inquiries::inquiries.failed_to_update_status') }}', 'error');
                    }
                } catch (error) {
                    showToast('{{ __('inquiries::inquiries.error_while_updating_status') }}', 'error');
                }
            }
        });
    </script>
    </x-inquiries::bulk-actions>
@endsection
