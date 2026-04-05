<?php

declare(strict_types=1);

return [
    // General
    'home' => 'Home',
    'departments' => 'Departments',

    // Departments
    'add_new_department' => 'Add New Department',
    'search_by_title' => 'Search by Title',
    'department_name' => 'Department Name',
    'parent' => 'Parent',
    'director' => 'Director',
    'deputy_director' => 'Deputy Director',
    'description' => 'Description',
    'max_leave_percentage' => 'Max Leave Percentage',
    'actions' => 'Actions',
    'view_hierarchy' => 'View Hierarchy',
    'edit' => 'Edit',
    'delete' => 'Delete',

    // Modal Titles
    'add_department' => 'Add Department',
    'edit_department' => 'Edit Department',
    'hierarchy' => 'Hierarchy',

    // Hierarchy
    'parents' => 'Parent Departments',
    'current_department' => 'Current Department',
    'children_departments' => 'Child Departments',
    'no_child_departments' => 'No child departments',
    'no_department_selected' => 'No department selected',

    // Form Labels
    'title' => 'Title',
    'select_parent' => 'Select Parent',
    'select_director' => 'Select Director',
    'select_deputy_director' => 'Select Deputy Director',
    'no_employees_found' => 'No employees found in this department',

    // Form
    'max_leave_percentage_placeholder' => 'Enter percentage (optional)',
    'max_leave_percentage_help' => 'Maximum percentage of employees allowed on leave at the same time',
    'company_percentage_info' => 'Current company percentage: :percentage%',
    'company_percentage_not_set_warning' => 'Warning: Company percentage not set in settings',

    // Buttons
    'cancel' => 'Cancel',
    'save' => 'Save',
    'update' => 'Update',
    'close' => 'Close',

    // Messages
    'no_departments_found' => 'No departments found',
    'confirm_delete_department' => 'Are you sure you want to delete this department?',
    'department_created_successfully' => 'Department created successfully',
    'department_updated_successfully' => 'Department updated successfully',
    'department_deleted_successfully' => 'Department deleted successfully',

    // Validation
    'department_percentage_requires_company_percentage' => 'Company percentage must be set first in HR settings before setting department percentage',
    'department_percentage_exceeds_company' => 'Department percentage (:department_percentage%) cannot exceed company percentage (:company_percentage%)',
];
