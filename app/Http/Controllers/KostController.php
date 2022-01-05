<?php

namespace App\Http\Controllers;

use App\Models\Kost;
use Illuminate\Http\Request;
use Throwable;

class KostController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $kost_req = $request->kost;
        $types_req = $request->types;

        try {
            $kost = Kost::create($kost_req);
            $types = $kost->roomTypes()->create($types_req);

            foreach($types as $type) {
                $count = $type->count;
                $rooms = array_fill(0, $count, []);

                $type->rooms()->create($rooms);
            }

            return $this->success('Kost berhasil dibuat', $kost);
        } catch (Throwable $e) {
            return $this->fail($e->getMessage());
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
        $kost = Kost::with(['rooms.tenant', 'images'])->find($id);

        if (!$kost) {
            return $this->fail('Data kost tidak ditemukan');
        }

        return $this->success(null, $kost);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $kost = Kost::with(['rooms.tenant', 'images'])->find($id);

        if (!$kost) {
            return $this->fail('Data kost tidak ditemukan');
        }

        return $this->success(null, $kost);
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
        $kost = Kost::find($id);

        if (!$kost) {
            return $this->fail('Data kost tidak ditemukan');
        }

        try {
            $kost->update($request->kost);

            return $this->success('Kost berhasil dibuat');
        } catch (Throwable $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Kost::destroy($id)) {
            return $this->fail('Terjadi kesalahan menghapus kost');
        }

        return $this->success('Kost berhasil dihapus');
    }
}
