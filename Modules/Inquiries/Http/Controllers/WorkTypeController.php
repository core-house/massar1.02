<?php

namespace Modules\Inquiries\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Inquiries\Http\Requests\WorkTypeRequest;
use Modules\Inquiries\Models\WorkType;
use RealRashid\SweetAlert\Facades\Alert;

class WorkTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view Work Types')->only(['index', 'getTreeData', 'getActiveWorkTypes']);
        $this->middleware('can:create Work Types')->only(['store']);
        $this->middleware('can:edit Work Types')->only(['update']);
        $this->middleware('can:delete Work Types')->only(['destroy']);
    }

    public function index()
    {
        try {
            $workTypes = WorkType::whereNull('parent_id')
                ->with('childrenRecursive')
                ->orderBy('name')
                ->get();

            $workTypesTree = $this->buildTreeArray($workTypes);

            return view('inquiries::work-types.index', compact('workTypes', 'workTypesTree'));
        } catch (Exception $e) {
            Alert::toast(__('inquiries::inquiries.error_loading_data'), 'error');

            return redirect()->back();
        }
    }

    public function store(WorkTypeRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $validatedData['is_active'] = $validatedData['is_active'] ?? true;

            $workType = WorkType::create($validatedData);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('inquiries::inquiries.work_type_created_successfully'),
                    'workType' => $this->formatWorkTypeForResponse($workType),
                ]);
            }

            Alert::toast(__('inquiries::inquiries.work_type_created_successfully'), 'success');

            return redirect()->route('work.types.index');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('inquiries::inquiries.error_during_work_type_save'),
                ], 500);
            }

            Alert::toast(__('inquiries::inquiries.error_during_work_type_save'), 'error');

            return redirect()->back()->withInput();
        }
    }

    public function update(WorkTypeRequest $request, $id)
    {
        try {
            $workType = WorkType::findOrFail($id);
            $validatedData = $request->validated();

            // التحقق من عدم جعل نوع العمل فرع من نفسه أو من أحد فروعه
            if (isset($validatedData['parent_id']) && $validatedData['parent_id']) {
                if ($this->isDescendant($workType, $validatedData['parent_id'])) {
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => __('inquiries::inquiries.cannot_make_work_type_child_of_itself'),
                        ], 400);
                    }

                    Alert::toast(__('inquiries::inquiries.cannot_make_work_type_child_of_itself'), 'error');

                    return redirect()->back()->withInput();
                }
            }

            $workType->update($validatedData);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('inquiries::inquiries.work_type_updated_successfully'),
                    'workType' => $this->formatWorkTypeForResponse($workType),
                ]);
            }

            Alert::toast(__('inquiries::inquiries.work_type_updated_successfully'), 'success');

            return redirect()->route('work.types.index');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('inquiries::inquiries.error_during_work_type_update'),
                ], 500);
            }

            Alert::toast(__('inquiries::inquiries.error_during_work_type_update'), 'error');

            return redirect()->back()->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $workType = WorkType::findOrFail($id);

            $childrenCount = $workType->childrenRecursive()->count();
            $this->deleteWorkTypeAndChildren($workType);

            return response()->json([
                'success' => true,
                'message' => $childrenCount > 0
                    ? __('inquiries::inquiries.work_type_and_children_deleted', ['count' => $childrenCount])
                    : __('inquiries::inquiries.work_type_deleted_successfully'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('inquiries::inquiries.error_during_work_type_delete'),
            ], 500);
        }
    }

    public function toggleStatus($id)
    {
        try {
            $workType = WorkType::findOrFail($id);
            $workType->is_active = ! $workType->is_active;
            $workType->save();

            return response()->json([
                'success' => true,
                'is_active' => $workType->is_active,
                'message' => $workType->is_active
                    ? __('inquiries::inquiries.work_type_activated_successfully')
                    : __('inquiries::inquiries.work_type_deactivated_successfully'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('inquiries::inquiries.error_during_work_type_status_change'),
            ], 500);
        }
    }

    public function getTreeData()
    {
        try {
            $workTypes = WorkType::whereNull('parent_id')
                ->with('childrenRecursive')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $this->buildTreeArray($workTypes),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('inquiries::inquiries.error_loading_work_types_data'),
                'data' => [],
            ], 500);
        }
    }

    public function getActiveWorkTypes(Request $request)
    {
        try {
            $query = WorkType::where('is_active', true);

            if ($request->has('parent_id')) {
                if ($request->parent_id === 'null' || $request->parent_id === '') {
                    $query->whereNull('parent_id');
                } else {
                    $query->where('parent_id', $request->parent_id);
                }
            }

            $workTypes = $query->orderBy('name')->get(['id', 'name', 'parent_id']);

            return response()->json([
                'success' => true,
                'data' => $workTypes,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('inquiries::inquiries.error_loading_work_types'),
                'data' => [],
            ], 500);
        }
    }

    private function buildTreeArray($workTypes)
    {
        return $workTypes->map(function ($workType) {
            return [
                'id' => $workType->id,
                'name' => $workType->name,
                'parent_id' => $workType->parent_id,
                'is_active' => $workType->is_active,
                'created_at' => $workType->created_at->format('Y-m-d H:i'),
                'updated_at' => $workType->updated_at->format('Y-m-d H:i'),
                'children' => $this->buildTreeArray($workType->childrenRecursive),
            ];
        })->toArray();
    }

    private function formatWorkTypeForResponse($workType)
    {
        $workType->load('parent', 'childrenRecursive');

        return [
            'id' => $workType->id,
            'name' => $workType->name,
            'parent_id' => $workType->parent_id,
            'parent_name' => $workType->parent ? $workType->parent->name : null,
            'is_active' => $workType->is_active,
            'has_children' => $workType->childrenRecursive->count() > 0,
            'children_count' => $workType->childrenRecursive->count(),
            'created_at' => $workType->created_at->format('Y-m-d H:i'),
            'updated_at' => $workType->updated_at->format('Y-m-d H:i'),
            'path' => $this->buildWorkTypePath($workType),
        ];
    }

    private function buildWorkTypePath($workType)
    {
        $path = [$workType];
        $current = $workType;

        while ($current->parent_id) {
            $current = $current->parent;
            if ($current) {
                array_unshift($path, $current);
            } else {
                break;
            }
        }

        return array_map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
            ];
        }, $path);
    }

    private function isDescendant($workType, $potentialParentId)
    {
        if ($workType->id == $potentialParentId) {
            return true;
        }

        $descendants = $this->getAllDescendants($workType);

        return $descendants->pluck('id')->contains($potentialParentId);
    }

    private function getAllDescendants($workType)
    {
        $descendants = collect();

        foreach ($workType->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($this->getAllDescendants($child));
        }

        return $descendants;
    }

    public function show($id)
    {
        $workType = WorkType::with(['parent', 'children'])->findOrFail($id);

        return view('inquiries::work-types.show', compact('workType'));
    }

    private function deleteWorkTypeAndChildren($workType)
    {
        foreach ($workType->children as $child) {
            $this->deleteWorkTypeAndChildren($child);
        }
        $workType->delete();
    }
}
