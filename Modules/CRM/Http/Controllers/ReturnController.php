<?php

namespace Modules\CRM\Http\Controllers;

use App\Models\{Client, Item};
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\{DB, Auth};
use Modules\CRM\Http\Requests\StoreReturnRequest;
use Modules\CRM\Models\{ReturnItem, ReturnOrder};

class ReturnController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view Returns')->only(['index', 'show']);
        $this->middleware('can:create Returns')->only(['create', 'store']);
        $this->middleware('can:edit Returns')->only(['edit', 'update', 'approve', 'reject']);
        $this->middleware('can:delete Returns')->only(['destroy']);
    }

    public function index()
    {
        $returns = ReturnOrder::with(['client', 'createdBy', 'approvedBy', 'items'])
            ->latest()
            ->get();

        return view('crm::returns.index', compact('returns'));
    }

    public function create()
    {
        $branches = userBranches();
        $clients = Client::all();
        $items = Item::all();

        return view('crm::returns.create', compact('clients', 'items', 'branches'));
    }

    public function store(StoreReturnRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $itemsData = $validated['items'];
            unset($validated['items']);

            // Remove attachment from validated data (will be handled by Media Library)
            unset($validated['attachment']);

            $validated['created_by'] = Auth::id();
            $validated['status'] = 'pending';

            $returnOrder = ReturnOrder::create($validated);

            // Handle attachment upload using Media Library
            if ($request->hasFile('attachment')) {
                $returnOrder->addMediaFromRequest('attachment')
                    ->toMediaCollection('return-attachments');
            }

            // Handle multiple images if provided
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $returnOrder->addMedia($image)
                        ->toMediaCollection('return-images');
                }
            }

            foreach ($itemsData as $item) {
                ReturnItem::create([
                    'return_id'      => $returnOrder->id,
                    'item_id'        => $item['item_id'],
                    'quantity'       => $item['quantity'],
                    'unit_price'     => $item['unit_price'],
                    'item_condition' => $item['item_condition'] ?? null,
                    'notes'          => $item['notes'] ?? null,
                ]);
            }

            $returnOrder->calculateTotal();
            $returnOrder->update(['refund_amount' => $returnOrder->total_amount]);

            DB::commit();

            return redirect()->route('returns.index')->with('message', __('crm::crm.return_created_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors(['error' => __('Error: ') . $e->getMessage()]);
        }
    }


    public function show(ReturnOrder $return)
    {
        $return->load(['client', 'createdBy', 'approvedBy', 'items.item']);
        return view('crm::returns.show', compact('return'));
    }

    public function edit(ReturnOrder $return)
    {
        if ($return->status != 'pending') {
            return redirect()->back()->with('error', __('crm::crm.cannot_edit_non_pending_returns'));
        }

        $clients = Client::all();
        $items = Item::all();
        $return->load('items.item');

        return view('crm::returns.edit', compact('return', 'clients', 'items'));
    }

    public function update(StoreReturnRequest $request, ReturnOrder $return)
    {
        $validated = $request->validated();

        if ($return->status != 'pending') {
            return redirect()->back()->with('error', __('crm::crm.cannot_edit_processed_returns'));
        }

        DB::beginTransaction();
        try {
            $itemsData = $validated['items'];
            unset($validated['items']);
            unset($validated['attachment']); // Will be handled by Media Library

            $updateData = [
                'client_id'               => $validated['client_id'],
                'return_date'             => $validated['return_date'],
                'return_type'             => $validated['return_type'],
                'original_invoice_number' => $validated['original_invoice_number'],
                'original_invoice_date'   => $validated['original_invoice_date'],
                'branch_id'               => $validated['branch_id'],
                'reason'                  => $validated['reason'],
                'notes'                   => $validated['notes'],
            ];

            // Handle attachment replacement using Media Library
            if ($request->hasFile('attachment')) {
                // Clear old attachments
                $return->clearMediaCollection('return-attachments');
                
                // Add new attachment
                $return->addMediaFromRequest('attachment')
                    ->toMediaCollection('return-attachments');
            }

            // Handle multiple images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $return->addMedia($image)
                        ->toMediaCollection('return-images');
                }
            }

            $return->update($updateData);

            $return->items()->delete();

            foreach ($itemsData as $item) {
                ReturnItem::create([
                    'return_id'      => $return->id,
                    'item_id'        => $item['item_id'],
                    'quantity'       => $item['quantity'],
                    'unit_price'     => $item['unit_price'],
                    'item_condition' => $item['item_condition'] ?? null,
                    'notes'          => $item['notes'] ?? null,
                ]);
            }

            $return->calculateTotal();
            $return->update(['refund_amount' => $return->total_amount]);

            DB::commit();

            return redirect()->route('returns.show', $return->id)->with('message', __('crm::crm.return_updated_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors(['error' => __('Error: ') . $e->getMessage()]);
        }
    }

    public function approve(ReturnOrder $return)
    {
        $return->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
        ]);

        return redirect()->route('returns.show', $return)->with('message', __('crm::crm.return_approved_successfully'));
    }

    public function reject(ReturnOrder $return)
    {
        $return->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
        ]);

        return redirect()->route('returns.show', $return)->with('message', __('crm::crm.return_rejected_successfully'));
    }

    public function destroy(ReturnOrder $return)
    {
        if ($return->status != 'pending') {
            return redirect()->back()->with('error', __('crm::crm.cannot_delete_processed_returns'));
        }

        $return->delete();
        return redirect()->route('returns.index')->with('message', __('crm::crm.return_deleted_successfully'));
    }

    public function downloadAttachment(ReturnOrder $return, $mediaId = null)
    {
        try {
            if ($mediaId) {
                // Download specific media item
                $media = $return->getMedia('return-attachments')->where('id', $mediaId)->first()
                    ?? $return->getMedia('return-images')->where('id', $mediaId)->first();
                
                if (!$media) {
                    return redirect()->back()->with('error', __('crm::crm.attachment_not_found'));
                }
                
                return response()->download($media->getPath(), $media->file_name);
            }
            
            // Download first attachment if no specific ID
            $media = $return->getFirstMedia('return-attachments');
            
            if (!$media) {
                return redirect()->back()->with('error', __('crm::crm.no_attachment_found'));
            }
            
            return response()->download($media->getPath(), $media->file_name);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('crm::crm.error_downloading_file'));
        }
    }

    public function deleteAttachment(ReturnOrder $return, $mediaId)
    {
        try {
            $media = $return->getMedia('return-attachments')->where('id', $mediaId)->first()
                ?? $return->getMedia('return-images')->where('id', $mediaId)->first();
            
            if (!$media) {
                return redirect()->back()->with('error', __('crm::crm.attachment_not_found'));
            }
            
            $media->delete();
            
            return redirect()->back()->with('message', __('crm::crm.attachment_deleted_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('crm::crm.error_deleting_attachment'));
        }
    }
}
