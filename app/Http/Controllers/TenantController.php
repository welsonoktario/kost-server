<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Throwable;

class TenantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tenants = Tenant::all();

        return $this->success(null, $tenants);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $user = User::create($request->user);
            $tenant = $user->tenant()->create($request->tenant);

            return $this->success('Tenant berhasil ditambahkan', $tenant);
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
        $tenant = Tenant::with(['user', 'room'])->find($id);

        if (!$tenant) {
            return $this->fail('Data tenant tidak ditemukan');
        }

        return $this->success(null, $tenant);
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
        $tenant = Tenant::find($id);

        if (!$tenant) {
            return $this->fail('Data tenant tidak ditemukan');
        }

        if (!$tenant->update($request->all())) {
            return $this->fail('Terjadi kesalahan mengubah data tenant');
        }

        return $this->success('Data tenant berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tenant = Tenant::destroy($id);

        if (!$tenant) {
            return $this->fail('Terjadi kesalahan menghapus data tenant');
        }

        return $this->success('Data tenant berhasil dihapus');
    }
}
