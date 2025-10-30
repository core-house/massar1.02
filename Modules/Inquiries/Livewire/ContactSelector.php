<?php

namespace Modules\Inquiries\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Modules\Inquiries\Models\Contact;
use Modules\Inquiries\Models\ContactRole;

class ContactSelector extends Component
{
    public $inquiry;
    public $roleSlug;
    public $roleName;

    public $selectedContacts = [];
    public $primaryContactId;

    public $showAddModal = false;
    public $showSelectModal = false;

    public $searchTerm = '';
    public $contactType = 'all';

    public $newContact = [
        'type' => 'person',
        'name' => '',
        'email' => '',
        'phone' => '',
        'organization_id' => null,
        'job_title' => '',
    ];

    protected $listeners = [
        'contactAdded' => 'refreshContacts',
        'contactRemoved' => 'refreshContacts',
    ];

    public function mount($inquiry = null, $roleSlug = 'client')
    {
        $this->inquiry = $inquiry;
        $this->roleSlug = $roleSlug;

        $role = ContactRole::where('slug', $roleSlug)->first();
        $this->roleName = $role ? $role->name : ucfirst($roleSlug);

        if ($inquiry) {
            $this->loadExistingContacts();
        }
    }

    public function loadExistingContacts()
    {
        $role = ContactRole::where('slug', $this->roleSlug)->first();

        if (!$role || !$this->inquiry) return;

        $contacts = $this->inquiry->contacts()
            ->wherePivot('role_id', $role->id)
            ->get();

        $this->selectedContacts = $contacts->pluck('id')->toArray();

        $primary = $contacts->where('pivot.is_primary', true)->first();
        $this->primaryContactId = $primary ? $primary->id : null;
    }

    public function getAvailableContactsProperty()
    {
        $query = Contact::active()->with(['organizations']);

        if ($this->searchTerm) {
            $query->search($this->searchTerm);
        }

        if ($this->contactType !== 'all') {
            $query->where('type', $this->contactType);
        }

        return $query->limit(50)->get();
    }

    public function getSelectedContactsDataProperty()
    {
        if (empty($this->selectedContacts)) return collect();

        return Contact::whereIn('id', $this->selectedContacts)
            ->with(['organizations', 'roles'])
            ->get();
    }

    public function addContact($contactId)
    {
        if (!in_array($contactId, $this->selectedContacts)) {
            $this->selectedContacts[] = $contactId;

            // أول واحد يكون primary
            if (count($this->selectedContacts) === 1) {
                $this->primaryContactId = $contactId;
            }

            // إضافة الدور للـ Contact
            $contact = Contact::find($contactId);
            $role = ContactRole::where('slug', $this->roleSlug)->first();

            if ($contact && $role) {
                $contact->assignRole($role->id, false);
            }

            // حفظ في الـ Inquiry لو موجود
            if ($this->inquiry) {
                $this->inquiry->assignContact(
                    $contactId,
                    $this->roleSlug,
                    $contactId == $this->primaryContactId
                );
            }

            $this->dispatch('contactAdded', contactId: $contactId);
        }
    }

    public function removeContact($contactId)
    {
        $this->selectedContacts = array_values(
            array_filter($this->selectedContacts, fn($id) => $id != $contactId)
        );

        if ($this->primaryContactId == $contactId) {
            $this->primaryContactId = $this->selectedContacts[0] ?? null;
        }

        // حذف من الـ Inquiry
        if ($this->inquiry) {
            $role = ContactRole::where('slug', $this->roleSlug)->first();
            if ($role) {
                $this->inquiry->removeContact($contactId, $role->id);
            }
        }

        $this->dispatch('contactRemoved', contactId: $contactId);
    }

    public function setPrimary($contactId)
    {
        if (in_array($contactId, $this->selectedContacts)) {
            $this->primaryContactId = $contactId;

            // تحديث في الـ Inquiry
            if ($this->inquiry) {
                $role = ContactRole::where('slug', $this->roleSlug)->first();
                if ($role) {
                    // إزالة primary من الكل
                    $this->inquiry->contacts()
                        ->wherePivot('role_id', $role->id)
                        ->update(['inquiry_contacts.is_primary' => false]);

                    // تعيين primary للجديد
                    $this->inquiry->contacts()
                        ->wherePivot('role_id', $role->id)
                        ->wherePivot('contact_id', $contactId)
                        ->update(['inquiry_contacts.is_primary' => true]);
                }
            }
        }
    }

    public function saveNewContact()
    {
        $this->validate([
            'newContact.type' => 'required|in:person,organization',
            'newContact.name' => 'required|string|max:255',
            'newContact.email' => 'nullable|email|unique:contacts,email',
            'newContact.phone' => 'required|string|max:20',
        ]);

        // إنشاء الـ Contact
        $contact = Contact::create([
            'type' => $this->newContact['type'],
            'name' => $this->newContact['name'],
            'email' => $this->newContact['email'],
            'phone' => $this->newContact['phone'],
            'job_title' => $this->newContact['job_title'] ?? null,
            'is_active' => true,
            'created_by' => Auth::id(),
            'branch_id' => Auth::user()->branch_id ?? null,
            'tenant' => Auth::user()->tenant ?? 0,
        ]);

        // إضافة الدور
        $role = ContactRole::where('slug', $this->roleSlug)->first();
        if ($role) {
            $contact->assignRole($role->id, true);
        }

        // ربط بمؤسسة لو person
        if ($this->newContact['type'] == 'person' && !empty($this->newContact['organization_id'])) {
            $contact->attachToOrganization(
                $this->newContact['organization_id'],
                $this->newContact['job_title'],
                true
            );
        }

        // إضافة للـ Selected
        $this->addContact($contact->id);

        $this->resetNewContactForm();
        $this->showAddModal = false;

        session()->flash('message', 'Contact added successfully!');
    }

    public function resetNewContactForm()
    {
        $this->newContact = [
            'type' => 'person',
            'name' => '',
            'email' => '',
            'phone' => '',
            'organization_id' => null,
            'job_title' => '',
        ];
    }

    public function openSelectModal()
    {
        $this->showSelectModal = true;
    }

    public function openAddModal()
    {
        $this->showAddModal = true;
    }

    public function closeModals()
    {
        $this->showSelectModal = false;
        $this->showAddModal = false;
        $this->dispatchBrowserEvent('closeModal');
    }

    // حفظ الـ Contacts في الـ Inquiry عند الحفظ النهائي
    public function getContactsForSave()
    {
        $role = ContactRole::where('slug', $this->roleSlug)->first();

        if (!$role) {
            return [];
        }

        return [
            'role_id' => $role->id,
            'contacts' => $this->selectedContacts,
            'primary' => $this->primaryContactId
        ];
    }

    public function render()
    {
        return view('inquiries::livewire.contact-selector', [
            'availableContacts' => $this->availableContacts,
            'selectedContactsData' => $this->selectedContactsData,
        ]);
    }
}
