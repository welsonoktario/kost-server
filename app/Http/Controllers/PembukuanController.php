<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Pengeluaran;
use Illuminate\Database\Eloquent\Builder;

class PembukuanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $invoices = Invoice::query()
            ->with(['tenant', 'invoiceDetails'])
            ->where('kost_id', $request->kost)
            ->when(
                $startDate && $endDate,
                function (Builder $q, $startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate]);
                }
            )
            ->get();

        $pengeluarans = Pengeluaran::query()
            ->where('kost_id', $request->kost)
            ->when(
                $startDate && $endDate,
                function (Builder $q, $startDate, $endDate) {
                    $q->whereBetween('date', [$startDate, $endDate]);
                }
            )
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
