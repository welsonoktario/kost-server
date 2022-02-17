<?php

namespace App\Http\Controllers;

use App\Models\Kost;
use App\Models\User;
use Illuminate\Http\Request;
use Throwable;

class KostController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($username)
    {
        $kost = Kost::with(['roomTypes.rooms.tenant.user'])->firstWhere('user_username', $username);

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
