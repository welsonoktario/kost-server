<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
        $room_req = $request->room;
        $user_req = $request->user;
        $services_req = $request->services;
        $user_req['password'] = Hash::make($user_req['password']);
        $entry_date = Carbon::parse($request->entry_date, 'Asia/Jakarta') ?: Carbon::now("Asia/Jakarta");
        $due_date = Carbon::parse($request->entry_date, 'Asia/Jakarta') ?: Carbon::now("Asia/Jakarta");

        try {
            // bikin user dan tenant
            $user = User::create($user_req);
            $tenant = $user->tenant()->create(
                [
                    'entry_date' => $entry_date,
                    'due_date' => $due_date->addMonths(2),
                    'status' => true
                ]
            );

            // upload ktp tenant
            $ktp = base64_decode($request->ktp);
            $filename = "tenant_{$tenant->id}.jpeg";
            Storage::disk('public')->put($filename, $ktp);
            Tenant::find($tenant->id)->update(['ktp' => $filename]);

            // tambah services user
            $tenant->services()->sync($services_req);

            // room diisi tenant
            Room::find($room_req)->update(['tenant_id' => $tenant->id]);
            $room_updated = Room::with('tenant.user')->find($room_req);

            return $this->success('Data tenant berhasil ditambahkan', $room_updated);
        } catch (Throwable $err) {
            return $this->fail($err->getMessage());
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
