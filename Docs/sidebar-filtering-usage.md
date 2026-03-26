# Sidebar Filtering Usage Guide

## Overview
The sidebar filtering system allows you to show only relevant menu sections based on user selection from the main dashboard, with automatic persistence across navigation.

## How It Works

### 1. Automatic Filtering
- When users click on an app card in `main-dashboard.blade.php`, the system automatically filters the sidebar to show only related menu items
- The selection is persisted in the session, so it remains active when navigating to create/edit/delete pages
- Default behavior shows all menu items when no filter is applied

### 2. Manual Override for Special Pages
For pages that need custom sidebar content, you can override the automatic filtering using `@section('sidebar-filter')`.

## Usage Examples

### Basic Override
To show only accounts-related menu items on a specific page:

```php
@section('sidebar-filter')
    @include('components.sidebar.accounts')
@endsection
```

### Multiple Sections
To show multiple related sections:

```php
@section('sidebar-filter')
    @include('components.sidebar.accounts')
    @include('components.sidebar.permissions')
@endsection
```

### Custom Menu Items
You can also include custom menu items that don't exist in the standard components:

```php
@section('sidebar-filter')
    @include('components.sidebar.accounts')
    
    {{-- Custom menu item --}}
    <li class="nav-item">
        <a href="{{ route('custom.route') }}" class="nav-link">
            <i data-feather="star" class="menu-icon"></i>
            Custom Item
        </a>
    </li>
@endsection
```

## Available Sidebar Components

The following components are available for use in `@section('sidebar-filter')`:

- `components.sidebar.accounts` - Basic data management
- `components.sidebar.items` - Items management
- `components.sidebar.discounts` - Discounts management
- `components.sidebar.manufacturing` - Manufacturing
- `components.sidebar.permissions` - User permissions
- `components.sidebar.crm` - Customer relationship management
- `components.sidebar.sales-invoices` - Sales invoices
- `components.sidebar.purchases-invoices` - Purchase invoices
- `components.sidebar.inventory-invoices` - Inventory invoices
- `components.sidebar.vouchers` - Financial vouchers
- `components.sidebar.transfers` - Cash transfers
- `components.sidebar.merit-vouchers` - Merit vouchers
- `components.sidebar.contract-journals` - Contract journals
- `components.sidebar.multi-vouchers` - Multi vouchers
- `components.sidebar.journals` - General journals
- `components.sidebar.projects` - Projects management
- `components.sidebar.departments` - Human resources
- `components.sidebar.settings` - System settings
- `components.sidebar.rentals` - Rental management
- `components.sidebar.service` - Maintenance
- `components.sidebar.shipping` - Shipping management
- `components.sidebar.POS` - Point of sale
- `components.sidebar.daily_progress` - Daily progress
- `components.sidebar.inquiries` - Inquiries

## Dashboard Mapping

The main dashboard uses the following mapping for automatic filtering:

| App Name (Arabic) | Sidebar Key | Components Shown |
|------------------|-------------|-----------------|
| الرئيسيه | all | All components |
| البيانات الاساسيه | accounts | accounts |
| الاصناف | items | items |
| الخصومات | discounts | discounts |
| التصنيع | manufacturing | manufacturing |
| الصلاحيات | permissions | permissions |
| CRM | crm | crm |
| المبيعات | sales-invoices | sales-invoices |
| المشتريات | purchases-invoices | purchases-invoices |
| ادارة المخزون | inventory-invoices | inventory-invoices |
| السندات الماليه | vouchers | vouchers |
| التحويلات النقديه | transfers | transfers |
| رواتب الموظفين | multi-vouchers | multi-vouchers, merit-vouchers |
| الاستحقاقات | contract-journals | contract-journals |
| عمليات الاصول | depreciation-journals | journals |
| أدارة الحسابات | basic_journal-journals | journals |
| المشاريع | projects | projects |
| الموارد البشريه | departments | departments |
| الاعدادات | settings | settings |
| ادارة المستأجرات | rentals | rentals |
| الصيانه | service | service |
| أدارة الشحن | shipping | shipping |
| نقطة البيع | POS | POS |
| التقدم اليومي | daily_progress | daily_progress |
| Inquiries | inquiries | inquiries |

## Best Practices

1. **Use automatic filtering when possible** - Let the system handle filtering based on dashboard selection
2. **Override only when necessary** - Use `@section('sidebar-filter')` only for pages that need custom sidebar content
3. **Keep overrides minimal** - Only include the components that are actually relevant to the current page
4. **Test navigation flow** - Ensure that the sidebar remains consistent when navigating between related pages

## Troubleshooting

### Sidebar shows all items when it should be filtered
- Check that the `PersistSidebarSelection` middleware is registered in `bootstrap/app.php`
- Verify that the `?sidebar=` parameter is being passed correctly from the dashboard

### Override not working
- Ensure that `@section('sidebar-filter')` is defined before the sidebar is rendered
- Check that the section is properly closed with `@endsection`

### Session not persisting
- Verify that sessions are properly configured in your Laravel application
- Check that the middleware is running on the correct routes
