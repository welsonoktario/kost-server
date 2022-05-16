<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Kost;
use App\Models\Tenant;
use Throwable;

class DendaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $kost = Kost::find($request->kost);
        $tenants = Tenant::query()
            ->with(['user', 'room'])
            ->whereHas('room.roomType', function ($q) use ($kost) {
                return $q->where('kost_id', $kost->id);
            })
            ->whereDate('due_date', '<', Carbon::now()->addDays($kost->denda_berlaku)->format('Y-m-d'))
            ->get();

        return $this->success(null, $tenants);
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
                    'interval_denda' => $request->interval,
                    'denda_berlaku' => $request->berlaku
                ]);

            return $this->success("Peraturan denda berhasil diubah");
        } catch (Throwable $e) {
            return $this->fail("Terjadi kesalahan mengubah peraturan denda: {$e->getMessage()}");
        }
    }
}
