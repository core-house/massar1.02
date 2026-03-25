<?php

declare(strict_types=1);

namespace Modules\HelpCenter\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\HelpCenter\Models\HelpArticle;
use Modules\HelpCenter\Models\HelpCategory;

class HelpAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage helpcenter');
    }

    // ── Categories ──────────────────────────────────────────────

    public function categories(): View
    {
        $categories = HelpCategory::withCount('articles')->orderBy('sort_order')->get();
        return view('helpcenter::admin.categories', compact('categories'));
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'name_en'    => ['nullable', 'string', 'max:255'],
            'icon'       => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $data['slug'] = \Illuminate\Support\Str::slug($data['name_en'] ?? $data['name']);
        $data['is_active'] = true;

        HelpCategory::create($data);

        return back()->with('success', __('helpcenter::helpcenter.category_created'));
    }

    public function updateCategory(Request $request, HelpCategory $category): RedirectResponse
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'name_en'    => ['nullable', 'string', 'max:255'],
            'icon'       => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer'],
            'is_active'  => ['boolean'],
        ]);

        $category->update($data);

        return back()->with('success', __('helpcenter::helpcenter.category_updated'));
    }

    public function destroyCategory(HelpCategory $category): RedirectResponse
    {
        $category->delete();
        return back()->with('success', __('helpcenter::helpcenter.category_deleted'));
    }

    // ── Articles ─────────────────────────────────────────────────

    public function articles(): View
    {
        $articles = HelpArticle::with('category')->orderBy('category_id')->orderBy('sort_order')->paginate(20);
        $categories = HelpCategory::where('is_active', true)->orderBy('sort_order')->get();
        return view('helpcenter::admin.articles', compact('articles', 'categories'));
    }

    public function createArticle(): View
    {
        $categories = HelpCategory::where('is_active', true)->orderBy('sort_order')->get();
        return view('helpcenter::admin.article-form', compact('categories'));
    }

    public function storeArticle(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:help_categories,id'],
            'title'       => ['required', 'string', 'max:255'],
            'title_en'    => ['nullable', 'string', 'max:255'],
            'content'     => ['required', 'string'],
            'content_en'  => ['nullable', 'string'],
            'route_key'   => ['nullable', 'string', 'max:255'],
            'status'      => ['required', 'in:draft,published'],
            'sort_order'  => ['nullable', 'integer'],
        ]);

        HelpArticle::create($data);

        return redirect()->route('helpcenter.admin.articles')->with('success', __('helpcenter::helpcenter.article_created'));
    }

    public function editArticle(HelpArticle $article): View
    {
        $categories = HelpCategory::where('is_active', true)->orderBy('sort_order')->get();
        return view('helpcenter::admin.article-form', compact('article', 'categories'));
    }

    public function updateArticle(Request $request, HelpArticle $article): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:help_categories,id'],
            'title'       => ['required', 'string', 'max:255'],
            'title_en'    => ['nullable', 'string', 'max:255'],
            'content'     => ['required', 'string'],
            'content_en'  => ['nullable', 'string'],
            'route_key'   => ['nullable', 'string', 'max:255'],
            'status'      => ['required', 'in:draft,published'],
            'sort_order'  => ['nullable', 'integer'],
        ]);

        $article->update($data);

        return redirect()->route('helpcenter.admin.articles')->with('success', __('helpcenter::helpcenter.article_updated'));
    }

    public function destroyArticle(HelpArticle $article): RedirectResponse
    {
        $article->delete();
        return back()->with('success', __('helpcenter::helpcenter.article_deleted'));
    }
}
