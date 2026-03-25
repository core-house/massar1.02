declare(strict_types=1);

namespace Modules\SOPs\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\SOPs\Models\SOPCategory;

class SOPCategoryController extends Controller
{
    public function index()
    {
        $categories = SOPCategory::latest()->get();
        return view('sops::categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        SOPCategory::create($request->all());

        return redirect()->back()->with('success', __('sops::sops.category_saved_successfully'));
    }

    public function update(Request $request, $id)
    {
        $category = SOPCategory::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        $category->update($request->all());

        return redirect()->back()->with('success', __('sops::sops.category_updated_successfully'));
    }

    public function destroy($id)
    {
        $category = SOPCategory::findOrFail($id);
        if ($category->sops()->count() > 0) {
            return redirect()->back()->with('error', __('sops::sops.category_has_sops'));
        }
        $category->delete();

        return redirect()->back()->with('success', __('sops::sops.category_deleted_successfully'));
    }
}
