<style>
    /* :root {
        --primary: #4e73df;
        --secondary: #858796;
        --success: #1cc88a;
        --info: #36b9cc;
        --warning: #f6c23e;
        --danger: #e74a3b;
        --light: #f8f9fc;
        --dark: #5a5c69;
    } */

    .project-dashboard {
        background-color: var(--bg-body, #f3f4f7);
        min-height: 100vh;
        font-family: 'Nunito', sans-serif;
    }

    .dashboard-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    .dashboard-header {
        background: var(--card-header-green, #28c76f) !important;
        background-color: var(--card-header-green, #28c76f) !important;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 4px 15px rgba(40, 199, 111, 0.4); /* Green shadow */
    }

    .stat-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-radius: 10px;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    
    .status-badge {
        padding: 0.5em 1em;
        font-size: 0.9em;
        border-radius: 50rem;
    }

    /* Avatar Group */
    .avatar-group {
        display: flex;
        align-items: center;
    }
    .avatar-group img {
        margin-left: -10px;
        transition: transform 0.2s;
        background-color: #fff;
    }
    .avatar-group img:first-child {
        margin-left: 0;
    }
    .avatar-group img:hover {
        transform: translateY(-3px);
        z-index: 2;
    }

    /* Custom Scrollbar for tables */
    .table-responsive::-webkit-scrollbar {
        height: 6px;
    }
    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    .table-responsive::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 3px;
    }
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #aaa;
    }

    .gradient-text {
        background: linear-gradient(45deg, var(--primary), var(--info));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    /* Timeline Dots */
    .timeline-item:last-child {
        border-start-end-radius: 0;
        border-image-width: 0;
        border-left: 2px solid transparent !important;
    }
</style>
