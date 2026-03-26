@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.settings')
@endsection
@section('content')
    @push('styles')
        <style>
            .download-card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 20px;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
                padding: 40px;
                max-width: 500px;
                margin: 0 auto;
                text-align: center;
                position: relative;
                overflow: hidden;
            }

            .download-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 5px;
                background: linear-gradient(90deg, #ff6b6b, #4ecdc4, #45b7d1, #96ceb4, #feca57);
                background-size: 300% 100%;
                animation: rainbow 3s ease infinite;
            }

            @keyframes rainbow {

                0%,
                100% {
                    background-position: 0% 50%;
                }

                50% {
                    background-position: 100% 50%;
                }
            }

            .download-icon {
                font-size: 4rem;
                color: #34d3a3;
                margin-bottom: 30px;
                animation: bounce 2s infinite;
            }

            @keyframes bounce {

                0%,
                20%,
                50%,
                80%,
                100% {
                    transform: translateY(0);
                }

                40% {
                    transform: translateY(-10px);
                }

                60% {
                    transform: translateY(-5px);
                }
            }

            .download-btn {
                background: linear-gradient(45deg, #34d3a3, #34d3a3);
                border: none;
                padding: 15px 40px;
                border-radius: 50px;
                color: white;
                font-size: 1.1rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
                position: relative;
                overflow: hidden;
            }

            .download-btn:hover {
                transform: translateY(-3px);
                box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
            }

            .download-btn:active {
                transform: translateY(-1px);
            }

            .download-btn::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
                transition: left 0.5s;
            }

            .download-btn:hover::before {
                left: 100%;
            }

            .spinner {
                display: none;
                margin: 20px auto;
            }

            .progress-container {
                display: none;
                margin-top: 20px;
            }

            .progress-bar {
                height: 10px;
                border-radius: 5px;
                background: linear-gradient(45deg, #667eea, #764ba2);
                animation: progress-animation 2s ease-in-out;
            }

            @keyframes progress-animation {
                0% {
                    width: 0%;
                }

                100% {
                    width: 100%;
                }
            }

            .success-message {
                display: none;
                color: #28a745;
                margin-top: 20px;
                font-weight: 600;
            }

            .error-message {
                display: none;
                color: #dc3545;
                margin-top: 20px;
                font-weight: 600;
            }

            .info-text {
                color: #666;
                margin-bottom: 30px;
                line-height: 1.6;
            }

            .stats {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
                gap: 20px;
                margin-top: 30px;
                padding-top: 30px;
                border-top: 1px solid #eee;
            }

            .stat-item {
                text-align: center;
            }

            .stat-number {
                font-size: 2rem;
                font-weight: bold;
                color: #34d3a3;
            }

            .stat-label {
                font-size: 0.9rem;
                color: #666;
            }

            .download-options {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 15px;
                margin-top: 30px;
            }

            .option-btn {
                padding: 12px 20px;
                border: 2px solid #34d3a3;
                background: transparent;
                color: #34d3a3;
                border-radius: 10px;
                cursor: pointer;
                transition: all 0.3s ease;
                font-weight: 500;
            }

            .option-btn:hover {
                background: #34d3a3;
                color: white;
                transform: translateY(-2px);
            }

            .option-btn.active {
                background: #34d3a3;
                color: white;
            }
        </style>
    @endpush
    {{-- Check permission --}}
    @can('view Export Data')
        <div class="container">
            <div class="download-card">
                <div class="download-icon">
                    <i class="fas fa-cloud-download-alt"></i>
                </div>

                <h2 style="color: #333; margin-bottom: 20px;">{{ __('Download System Data') }}</h2>

                <p class="info-text">
                    {{ __('You can download all ERP system data to save it locally on your device. The data will be protected and compressed in a single file.') }}
                </p>

                {{-- Download Options --}}
                @can('edit Export Data')
                    <div class="download-options">
                        <button class="option-btn active" data-type="json" onclick="selectOption('json', this)">
                            <i class="fas fa-file-code"></i> JSON/CSV
                        </button>
                        <button class="option-btn" data-type="sql" onclick="selectOption('sql', this)">
                            <i class="fas fa-database"></i> SQL Database
                        </button>
                    </div>
                    <br>

                    {{-- Main Download Button --}}
                    <button class="download-btn" id="downloadBtn" onclick="startDownload()">
                        <i class="fas fa-download"></i>
                        <span id="btnText">{{ __('Download Data') }}</span>
                    </button>

                    {{-- Loading Spinner --}}
                    <div class="spinner" id="loadingSpinner">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">{{ __('Loading...') }}</span>
                        </div>
                        <p class="mt-2">{{ __('Preparing data for download...') }}</p>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="progress-container" id="progressContainer">
                        <div class="progress">
                            <div class="progress-bar" id="progressBar" style="width: 0%"></div>
                        </div>
                        <small class="text-muted mt-2 d-block">{{ __('Compressing files...') }}</small>
                    </div>

                    {{-- Success and Error Messages --}}
                    <div class="success-message" id="successMessage">
                        <i class="fas fa-check-circle"></i>
                        {{ __('Data downloaded successfully!') }}
                    </div>

                    <div class="error-message" id="errorMessage">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span id="errorText">{{ __('An error occurred during download') }}</span>
                    </div>
                @else
                    <div class="alert alert-warning mt-4">
                        <i class="fas fa-lock"></i>
                        {{ __('You do not have permission to export data') }}
                    </div>
                @endcan

                {{-- Statistics --}}
                <div class="stats">
                    <div class="stat-item">
                        <div class="stat-number" id="recordsCount">-</div>
                        <div class="stat-label">{{ __('Records') }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" id="tablesCount">-</div>
                        <div class="stat-label">{{ __('Tables') }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" id="dbSize">-</div>
                        <div class="stat-label">{{ __('Megabytes') }}</div>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- No Permission Page --}}
        <div class="container">
            <div class="alert alert-danger text-center py-5">
                <i class="fas fa-ban fa-3x mb-3"></i>
                <h3>{{ __('Access Denied') }}</h3>
                <p>{{ __('You do not have permission to access this page') }}</p>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-home"></i> {{ __('Back to Dashboard') }}
                </a>
            </div>
        </div>
    @endcan

    @push('scripts')
        <script>
            let selectedType = 'json';

            function selectOption(type, button) {
                // Remove active from all buttons
                document.querySelectorAll('.option-btn').forEach(btn => {
                    btn.classList.remove('active');
                });

                // Add active to selected button
                button.classList.add('active');
                selectedType = type;

                // Update button text
                const btnText = document.getElementById('btnText');
                if (type === 'json') {
                    btnText.innerHTML = '<i class="fas fa-download"></i> {{ __('Download JSON/CSV') }}';
                } else {
                    btnText.innerHTML = '<i class="fas fa-download"></i> {{ __('Download SQL Database') }}';
                }
            }

            function startDownload() {
                const downloadBtn = document.getElementById('downloadBtn');
                const spinner = document.getElementById('loadingSpinner');
                const progressContainer = document.getElementById('progressContainer');
                const successMessage = document.getElementById('successMessage');
                const errorMessage = document.getElementById('errorMessage');

                // Hide previous messages
                successMessage.style.display = 'none';
                errorMessage.style.display = 'none';

                // Disable button and show loading
                downloadBtn.disabled = true;
                spinner.style.display = 'block';

                // Simulate preparation
                setTimeout(() => {
                    spinner.style.display = 'none';
                    progressContainer.style.display = 'block';

                    // Start progress bar
                    const progressBar = document.getElementById('progressBar');
                    let width = 0;
                    const interval = setInterval(() => {
                        width += 10;
                        progressBar.style.width = width + '%';

                        if (width >= 100) {
                            clearInterval(interval);

                            // Simulate actual download
                            setTimeout(() => {
                                downloadFile();
                            }, 500);
                        }
                    }, 200);
                }, 1000);
            }

            function downloadFile() {
                const progressContainer = document.getElementById('progressContainer');
                const successMessage = document.getElementById('successMessage');
                const errorMessage = document.getElementById('errorMessage');
                const downloadBtn = document.getElementById('downloadBtn');

                // Determine URL based on selected type
                let downloadUrl;
                if (selectedType === 'json') {
                    downloadUrl = '/settings/export-data';
                } else {
                    downloadUrl = '/settings/export-sql';
                }

                // Attempt actual download
                fetch(downloadUrl, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('{{ __('Failed to download data') }}');
                        }
                        return response.blob();
                    })
                    .then(blob => {
                        // Create download link
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.style.display = 'none';
                        a.href = url;
                        a.download =
                            `erp_data_${selectedType}_${new Date().toISOString().slice(0,19).replace(/:/g, '-')}.${selectedType === 'json' ? 'zip' : 'sql'}`;
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);

                        // Show success message
                        progressContainer.style.display = 'none';
                        successMessage.style.display = 'block';

                        // Re-enable button
                        setTimeout(() => {
                            downloadBtn.disabled = false;
                            successMessage.style.display = 'none';
                        }, 3000);
                    })
                    .catch(error => {
                        console.error('Error:', error);

                        // Show error message
                        progressContainer.style.display = 'none';
                        errorMessage.style.display = 'block';
                        document.getElementById('errorText').textContent = error.message;

                        // Re-enable button
                        setTimeout(() => {
                            downloadBtn.disabled = false;
                            errorMessage.style.display = 'none';
                        }, 3000);
                    });
            }

            // Update statistics
            function updateStats() {
                fetch('/api/export-stats')
                    .then(response => response.json())
                    .then(data => {
                        if (data.records) {
                            document.getElementById('recordsCount').textContent = data.records.toLocaleString();
                        }
                        if (data.tables) {
                            document.getElementById('tablesCount').textContent = data.tables;
                        }
                        if (data.size) {
                            document.getElementById('dbSize').textContent = data.size;
                        }
                    })
                    .catch(error => console.log('Stats update failed:', error));
            }

            // Update statistics on page load
            document.addEventListener('DOMContentLoaded', function() {
                updateStats();
            });
        </script>
    @endpush
@endsection
