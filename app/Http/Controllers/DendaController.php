<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kost;
use Throwable;
use App\Models\Tenant;
use Illuminate\Support\Carbon;

class DendaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $tenants = Tenant::query()
            ->with(['user'])
            ->whereHas('room.roomType', function ($q) use ($request) {
                return $q->where('kost_id', $request->kost);
            })
            ->whereDate('due_date', '<', Carbon::now()->addDays(3)->format('Y-m-d'))
            ->get();

        return $this->success(null, $tenants);
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
        $kost = Kost::find($id);

        return $this->success(null, $kost);
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
        try {
            Kost::query()
                ->find($id)
                ->update([
                    'nominal_denda' => $request->nominal,
                    'interval_denda' => $request->interval
                ]);

            return $this->success("Peraturan denda berhasil diubah");
        } catch (Throwable $e) {
            return $this->fail("Terjadi kesalahan mengubah peraturan denda: {$e->getMessage()}");
        }
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
}
