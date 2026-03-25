<?php
declare(strict_types=1);

namespace Modules\POS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\POS\Models\RestaurantTable;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class RestaurantTableController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = RestaurantTable::latest()->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('seats', fn($row) => $row->capacity)
                ->addColumn('action', function($row){
                    $btn = '<a href="javascript:void(0)" data-id="'.$row->id.'" class="btn btn-primary btn-sm mx-1 editTable"><i class="las la-edit"></i></a>';
                    $btn .= '<a href="javascript:void(0)" data-id="'.$row->id.'" class="btn btn-danger btn-sm mx-1 deleteTable"><i class="las la-trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'seats' => 'nullable|integer|min:1', // maps to capacity column
        ]);

        $data = [
            'name' => $request->name,
            'capacity' => $request->seats,
        ];

        if ($request->filled('table_id')) {
            RestaurantTable::where('id', $request->table_id)->update($data);
        } else {
            RestaurantTable::create($data);
        }

        return response()->json(['success' => __('pos.saved_successfully')]);
    }

    public function edit($id)
    {
        $table = RestaurantTable::find($id);
        return response()->json($table);
    }

    public function destroy($id)
    {
        RestaurantTable::find($id)->delete();
        return response()->json(['success' => __('pos.deleted_successfully')]);
    }
}
