<?php
declare(strict_types=1);

namespace Modules\POS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\POS\Models\DeliveryArea;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class DeliveryAreaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DeliveryArea::latest()->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('fee', function($row) {
                    return number_format((float)$row->delivery_fee, 2);
                })
                ->addColumn('status', function($row){
                    return $row->is_active ? '<span class="badge bg-success">'.__('pos.active').'</span>' : '<span class="badge bg-secondary">'.__('pos.inactive').'</span>';
                })
                ->addColumn('action', function($row){
                    $btn = '<a href="javascript:void(0)" data-id="'.$row->id.'" class="btn btn-primary btn-sm mx-1 editArea"><i class="las la-edit"></i></a>';
                    $btn .= '<a href="javascript:void(0)" data-id="'.$row->id.'" class="btn btn-danger btn-sm mx-1 deleteArea"><i class="las la-trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'delivery_fee' => 'required|numeric|min:0',
        ]);

        $data = $request->only('name', 'delivery_fee');
        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        
        if ($request->filled('area_id')) {
            DeliveryArea::where('id', $request->area_id)->update($data);
        } else {
            DeliveryArea::create($data);
        }

        return response()->json(['success' => __('pos.saved_successfully')]);
    }

    public function edit($id)
    {
        $area = DeliveryArea::find($id);
        return response()->json($area);
    }

    public function destroy($id)
    {
        DeliveryArea::find($id)->delete();
        return response()->json(['success' => __('pos.deleted_successfully')]);
    }
}
