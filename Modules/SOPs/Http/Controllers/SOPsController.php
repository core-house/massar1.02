declare(strict_types=1);

namespace Modules\SOPs\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\SOPs\Models\SOP;
use Modules\SOPs\Models\SOPCategory;
use Modules\HR\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SOPsController extends Controller
{
    public function index()
    {
        $sops = SOP::with(['category', 'department', 'creator'])->latest()->get();
        return view('sops::index', compact('sops'));
    }

    public function create()
    {
        $categories = SOPCategory::where('is_active', true)->get();
        $departments = Department::all();
        return view('sops::create', compact('categories', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:sop_categories,id',
            'department_id' => 'nullable|exists:departments,id',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'attachment' => 'nullable|file|max:10240', // 10MB
            'version' => 'nullable|string',
            'status' => 'required|in:draft,active,archived',
        ]);

        $data = $request->all();
        $data['created_by'] = Auth::id();

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('sops', 'public');
        }

        SOP::create($data);

        return redirect()->route('sops.index')->with('success', __('sops::sops.saved_successfully'));
    }

    public function show($id)
    {
        $sop = SOP::with(['category', 'department', 'creator', 'updater'])->findOrFail($id);
        return view('sops::show', compact('sop'));
    }

    public function edit($id)
    {
        $sop = SOP::findOrFail($id);
        $categories = SOPCategory::where('is_active', true)->get();
        $departments = Department::all();
        return view('sops::edit', compact('sop', 'categories', 'departments'));
    }

    public function update(Request $request, $id)
    {
        $sop = SOP::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:sop_categories,id',
            'department_id' => 'nullable|exists:departments,id',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'attachment' => 'nullable|file|max:10240',
            'version' => 'nullable|string',
            'status' => 'required|in:draft,active,archived',
        ]);

        $data = $request->all();
        $data['updated_by'] = Auth::id();

        if ($request->hasFile('attachment')) {
            if ($sop->attachment) {
                Storage::disk('public')->delete($sop->attachment);
            }
            $data['attachment'] = $request->file('attachment')->store('sops', 'public');
        }

        $sop->update($data);

        return redirect()->route('sops.index')->with('success', __('sops::sops.updated_successfully'));
    }

    public function destroy($id)
    {
        $sop = SOP::findOrFail($id);
        if ($sop->attachment) {
            Storage::disk('public')->delete($sop->attachment);
        }
        $sop->delete();

        return redirect()->route('sops.index')->with('success', __('sops::sops.deleted_successfully'));
    }
}
