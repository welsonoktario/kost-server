<?php

namespace App\Http\Controllers;

use App\Models\Catatan;
use Illuminate\Http\Request;
use App\Models\Kost;
use Throwable;

class CatatanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $catatans = Catatan::query()
            ->where('kost_id', $request->kost)
            ->get();

        return $this->success(null, $catatans);
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
            $catatan = Kost::query()
                ->find($request->kost)
                ->catatans()
                ->create($request->except('kost'));

            return $this->success(null, $catatan);
        } catch (Throwable $e) {
            return $this->fail("Terjadi kesalahan sistem: {$e->getMessage()}");
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Catatan  $catatan
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Catatan $catatan)
    {
        try {
            $catatan->update($request->all());

            return $this->success('Catatan berhasil diubah');
        } catch (Throwable $e) {
            return $this->fail("Terjadi kesalahan sistem: {$e->getMessage()}");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Catatan  $catatan
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Catatan $catatan)
    {
        try {
            $catatan->delete();

            return $this->success('Catatan berhasil dihapus');
        } catch (Throwable $e) {
            return $this->fail("Terjadi kesalahan sistem: {$e->getMessage()}");
        }
    }
}
