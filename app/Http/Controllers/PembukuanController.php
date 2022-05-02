<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Pengeluaran;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Kost;
use Throwable;

class PembukuanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $startDate = $request->start ?: null;
        $endDate = $request->end ?: null;
        $invoices = Invoice::query()
            ->with(['tenant.user', 'invoiceDetails'])
            ->where('kost_id', $request->kost)
            ->when(
                $startDate && $endDate,
                function (Builder $q) use ($startDate, $endDate) {
                    $q->whereBetween('date', [$startDate, $endDate]);
                }
            )
            ->orderBy('date', 'DESC')
            ->get();

        $pengeluarans = Pengeluaran::query()
            ->where('kost_id', $request->kost)
            ->when(
                $startDate && $endDate,
                function (Builder $q) use ($startDate, $endDate) {
                    $q->whereBetween('date', [$startDate, $endDate]);
                }
            )
            ->orderBy('date', 'DESC')
            ->get();

        return $this->success(null, compact(['invoices', 'pengeluarans']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $pengeluaran = Kost::query()
            ->find($request->kost)
            ->pengeluarans()
            ->create([
                'nominal' => (int) $request->nominal,
                'description' => $request->description,
                'date' => $request->date
            ]);

            return $this->success("Pengeluaran berhasil ditambahkan", $pengeluaran);
        } catch (Throwable $e) {
            return $this->fail("Terjadi kesalahan server: {$e->getMessage()}");
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
