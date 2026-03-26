<?php

namespace Modules\Quality\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Quality\Models\QualityCertificate;
use Modules\Quality\Http\Requests\CertificateRequest;

class QualityCertificateController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view certificates')->only(['index', 'show']);
        $this->middleware('can:create certificates')->only(['create', 'store']);
        $this->middleware('can:edit certificates')->only(['edit', 'update']);
        $this->middleware('can:delete certificates')->only(['destroy']);
    }

    public function index()
    {
        $certificates = QualityCertificate::orderBy('expiry_date', 'asc')->paginate(20);

        $stats = [
            'total'         => QualityCertificate::count(),
            'active'        => QualityCertificate::where('status', 'active')->count(),
            'expiring_soon' => QualityCertificate::expiringSoon()->count(),
            'expired'       => QualityCertificate::expired()->count(),
        ];

        return view('quality::certificates.index', compact('certificates', 'stats'));
    }

    public function create()
    {
        return view('quality::certificates.create');
    }

    public function store(CertificateRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['branch_id']          = auth()->user()->branches()->where('is_active', 1)->first()->id ?? 1;
            $validated['status']             = 'active';
            $validated['notify_before_expiry'] = true;
            $validated['created_by']         = auth()->id();

            $certificate = QualityCertificate::create($validated);

            return redirect()->route('quality.certificates.show', $certificate)
                ->with('success', __('quality::quality.certificate details') . ' ' . __('quality::quality.created'));
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', __('quality::quality.error') . ': ' . $e->getMessage());
        }
    }

    public function show(QualityCertificate $certificate)
    {
        return view('quality::certificates.show', compact('certificate'));
    }

    public function edit(QualityCertificate $certificate)
    {
        return view('quality::certificates.edit', compact('certificate'));
    }

    public function update(CertificateRequest $request, QualityCertificate $certificate)
    {
        try {
            $validated = $request->validated();
            $validated['updated_by'] = auth()->id();
            $certificate->update($validated);

            return redirect()->route('quality.certificates.show', $certificate)
                ->with('success', __('quality::quality.certificate details') . ' ' . __('quality::quality.save changes'));
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', __('quality::quality.error') . ': ' . $e->getMessage());
        }
    }

    public function destroy(QualityCertificate $certificate)
    {
        try {
            $certificate->delete();

            return redirect()->route('quality.certificates.index')
                ->with('success', __('quality::quality.delete') . ' ' . __('quality::quality.success'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('quality::quality.error') . ': ' . $e->getMessage());
        }
    }
}
