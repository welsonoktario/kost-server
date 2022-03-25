<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
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
        $user_req['password'] = Hash::make($user_req['username'].substr($user_req['phone'], -4));
        $entry_date = Carbon::parse($request->entry_date, 'Asia/Jakarta') ?: Carbon::now("Asia/Jakarta");
        $due_date = Carbon::parse($request->entry_date, 'Asia/Jakarta') ?: Carbon::now("Asia/Jakarta");

        try {
            // bikin user dan tenant
            $user = User::create($user_req);
            $tenant = $user->tenant()->create([
                'entry_date' => $entry_date,
                'due_date' => $due_date->addMonths(2),
                'status' => true
            ]);

            // upload ktp tenant
            $ktp = base64_decode($request->ktp);
            $filename = "tenant_{$tenant->id}.jpeg";
            Storage::disk('public')->put($filename, $ktp);
            Tenant::find($tenant->id)->update(['ktp' => $filename]);

            // tambah services user
            $tenant->services()->sync($services_req);

            // room diisi tenant
            Room::find($room_req)->update(['tenant_id' => $tenant->id]);
            $room_updated = Room::with('tenant.user', 'tenant.services')->find($room_req);

            // bikin invoice pertama
            $this->createInvoice($tenant, $request->durasi);

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
        $tenant = Tenant::with(['services', 'room.roomType'])->find($id);

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
        $tenant = Tenant::find($id)->user()->destroy();

        if (!$tenant) {
            return $this->fail('Terjadi kesalahan menghapus data tenant');
        }

        return $this->success('Data tenant berhasil dihapus');
    }

    public function addTagihan(Request $request, $id)
    {
        try {
            $invoice = Invoice::where([
                ['tenant_id', $id],
                ['type', 'Tagihan'],
                ['status', 'Aktif']
            ])
                ->latest()
                ->first();

            if (!$invoice) {
                return $this->fail('Data tagihan tidak ditemukan');
            }

            $invoice->details()->create([
                'description' => $request->description,
                'cost' => $request->cost
            ]);

            return $this->success('Tagihan berhasil ditambahkan');
        } catch (Throwable $e) {
            return $this->fail('Terjadi kesalahan menambah tagihan');
        }
    }

    public function konfirmasiPembayaran($id)
    {
        try {
            $services = Tenant::find($id)->service;
            $invoice = Invoice::where([
                ['tenant_id', $id],
                ['type', 'Tagihan'],
                ['status', 'Aktif']
            ])
                ->latest()
                ->first();

            if (!$invoice) {
                return $this->fail('Data tagihan tidak ditemukan');
            }

            return $this->success('Konfirmasi pembayaran sukses');
        } catch (Throwable $e) {
            return $this->fail('Terjadi kesalahan mengonfirmasi pembayaran');
        }
    }

    public function perpanjang($id)
    {
        try {
            $tenant = Tenant::find($id);
            $tenant->update([
                'due_date' => Carbon::parse($tenant->due_date)->addMonth()->format('Y-m-d')
            ]);

            $this->createInvoice($tenant, 1);

            return $this->success('Perpanjangan berhasil');
        } catch (Throwable $e) {
            return $this->fail('Terjadi kesalahan perpanjangan');
        }
    }

    private function createInvoice(Tenant $tenant, int $durasi = 1)
    {
        $services = $tenant->services->map(
            fn ($service) => [
                'description' => "Tagihan service {$service->name} untuk {$durasi} bulan",
                'cost' => $service->cost * $durasi
            ]
        );

        // bikin invoice yang udah lunas
        $invoice = $tenant->invoices()->create([
            'total' => 0,
            'type' => 'Tagihan'
        ]);

        // bikin detail tagihan kamar
        $details = $invoice->details()->createMany([
            [
                'description' => "Tagihan kamar {$tenant->room->id} selama {$durasi} bulan",
                'cost' => $tenant->room->roomType->cost * $durasi
            ],
            ...$services
        ]);

        $invoice->update(['total' => $details->sum('cost')]);
    }
}
