<?php
declare(strict_types=1);

namespace Modules\POS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\POS\Models\Driver;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            $data = Driver::latest()->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function($row){
                    return $row->is_available ? '<span class="badge bg-success">'.__('pos.active').'</span>' : '<span class="badge bg-secondary">'.__('pos.inactive').'</span>';
                })
                ->addColumn('action', function($row){
                    $btn = '<a href="javascript:void(0)" data-id="'.$row->id.'" class="btn btn-primary btn-sm mx-1 editDriver"><i class="las la-edit"></i></a>';
                    $btn .= '<a href="javascript:void(0)" data-id="'.$row->id.'" class="btn btn-danger btn-sm mx-1 deleteDriver"><i class="las la-trash"></i></a>';
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
            'phone' => 'nullable|string|max:20',
            'vehicle_type' => 'nullable|string|max:100',
        ]);

        $data = $request->only('name', 'phone', 'vehicle_type');
        $data['is_available'] = $request->has('is_available') ? 1 : 0;
        
        if ($request->filled('driver_id')) {
            Driver::where('id', $request->driver_id)->update($data);
        } else {
            Driver::create($data);
        }

        return response()->json(['success' => __('pos.saved_successfully')]);
    }

    public function edit($id)
    {
        $driver = Driver::find($id);
        return response()->json($driver);
    }

    public function destroy($id)
    {
        Driver::find($id)->delete();
        return response()->json(['success' => __('pos.deleted_successfully')]);
    }
}
