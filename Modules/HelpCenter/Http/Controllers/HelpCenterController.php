<?php

declare(strict_types=1);

namespace Modules\HelpCenter\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\HelpCenter\Models\HelpArticle;
use Modules\HelpCenter\Models\HelpCategory;
use Modules\HelpCenter\Models\HelpFeedback;

class HelpCenterController extends Controller
{
    public function index(): View
    {
        $categories = HelpCategory::where('is_active', true)
            ->withCount(['activeArticles'])
            ->orderBy('sort_order')
            ->get();

        return view('helpcenter::index', compact('categories'));
    }

    public function category(string $slug): View
    {
        $category = HelpCategory::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $articles = $category->activeArticles()->get();

        return view('helpcenter::category', compact('category', 'articles'));
    }

    public function article(int $id): View
    {
        $article = HelpArticle::with('category')->published()->findOrFail($id);
        $article->incrementViews();

        $related = HelpArticle::published()
            ->where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->limit(5)
            ->get();

        return view('helpcenter::article', compact('article', 'related'));
    }

    /**
     * البحث في المقالات (AJAX)
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->string('q')->trim();

        if ($query->isEmpty() || $query->length() < 2) {
            return response()->json([]);
        }

        $articles = HelpArticle::published()
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            })
            ->with('category')
            ->limit(8)
            ->get(['id', 'title', 'category_id']);

        return response()->json($articles->map(fn($a) => [
            'id'       => $a->id,
            'title'    => $a->title,
            'category' => $a->category?->name,
            'url'      => route('helpcenter.article', $a->id),
        ]));
    }

    /**
     * مقالات مرتبطة بـ route معين (للـ Offcanvas السياقي)
     */
    public function byRoute(Request $request): JsonResponse
    {
        $routeKey = $request->string('route')->trim();

        $articles = HelpArticle::published()
            ->forRoute((string) $routeKey)
            ->limit(5)
            ->get(['id', 'title']);

        return response()->json($articles->map(fn($a) => [
            'id'    => $a->id,
            'title' => $a->title,
            'url'   => route('helpcenter.article', $a->id),
        ]));
    }

    /**
     * تقييم المقالة
     */
    public function feedback(Request $request, int $id): JsonResponse
    {
        $request->validate(['is_helpful' => ['required', 'boolean']]);

        $article = HelpArticle::published()->findOrFail($id);

        HelpFeedback::create([
            'article_id' => $article->id,
            'user_id'    => auth()->id(),
            'is_helpful' => $request->boolean('is_helpful'),
        ]);

        return response()->json(['success' => true]);
    }
}
