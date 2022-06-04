<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\Pengeluaran;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $invoices = Invoice::query()
            ->with('tenant.room')
            ->where('tenant_id', $request->tenant);

        return $this->success(null, $invoices);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function nota($tenant)
    {
        $invoices = Invoice::query()
            ->with(['tenant.user', 'tenant.room', 'invoiceDetails'])
            ->where('tenant_id', $tenant)
            ->orderBy('created_at', 'DESC')
            ->get();

        return $this->success(null, $invoices);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $invoice = Invoice::query()
            ->with(['details', 'services', 'dendas'])
            ->find($id);

        if (!$invoice) {
            return $this->fail('Data invoice tidak ditemukan');
        }

        return $this->success(null, $invoice);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        //
    }

    public function historyTransaksi($kost)
    {
        $invoices = Invoice::query()
            ->with(['tenant.room', 'tenant.user', 'invoiceDetails'])
            ->where('kost_id', $kost)
            ->orderBy('date', 'DESC')
            ->limit(10)
            ->get();

        $pengeluarans = Pengeluaran::query()
            ->where('kost_id', $kost)
            ->orderBy('date', 'DESC')
            ->limit(10)
            ->get();

        $invoices = $invoices->map(function ($invoice) {
            return ['invoice' => $invoice];
        });

        $pengeluarans = $pengeluarans->map(function ($pengeluaran) {
            return ['pengeluaran' => $pengeluaran];
        });

        $data = $invoices->merge($pengeluarans)->sortByDesc('date');

        return $this->success(null, $data);
    }
}
