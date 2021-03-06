<?php

namespace App\Http\Controllers;

use App\Models\Kost;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class TenantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $room_req = $request->room;
        $user_req = $request->user;
        $user_req['password'] = Hash::make($user_req['username'] . substr($user_req['phone'], -4));
        $entry_date = Carbon::parse($request->entry_date, 'Asia/Jakarta');
        $due_date = Carbon::parse($request->entry_date, 'Asia/Jakarta')->addMonth();
        $leave_date = Carbon::parse($request->entry_date, 'Asia/Jakarta')->addMonths($request->durasi);

        DB::beginTransaction();

        try {
            // cek udah terdaftar apa belum
            $oldUser = User::query()
                ->with('tenant')
                ->where('username', $user_req['username'])
                ->orWhere('phone', $user_req['phone'])
                ->first();

            if ($oldUser) {
                if ($oldUser->tenant && !$oldUser->tenant->deleted_at) {
                    return $this->fail('Username atau no hp telah digunakan');
                }
            }

            // bikin user dan tenant
            $user = User::query()->updateOrCreate(
                ['username' => $user_req['username']],
                [...$user_req,'deleted_at' => null]
            );
            $tenant = $user->tenant()->updateOrCreate(
                ['user_username' => $user->username],
                [
                    'entry_date' => $entry_date,
                    'due_date' => $due_date,
                    'leave_date' => $leave_date,
                    'status' => true,
                    'deleted_at' => null
                ]
            );

            // upload ktp tenant
            $ktp = base64_decode($request->ktp);
            $filename = "tenant_{$tenant->id}.jpeg";
            Storage::disk('public')->put($filename, $ktp);
            Tenant::find($tenant->id)->update(['ktp' => $filename]);

            // room diisi tenant
            Room::find($room_req)->update(['tenant_id' => $tenant->id]);
            $room_updated = Room::with('tenant.user', 'tenant.services')->find($room_req);

            // bikin invoice tagihan
            $invoice = $tenant->invoices()->create([
                'date' => $entry_date,
                'kost_id' => $room_updated->roomType->kost_id,
                'total' => $room_updated->roomType->cost,
                'nama' => $tenant->user->name,
                'no_kamar' => $room_updated->no_kamar,
                'tanggal_tagihan' => $entry_date->copy()->format('m-Y'),
                'description' => "Tagihan awal penyewa baru untuk bulan " . $entry_date->copy()->format('m-Y')
            ]);

            $invoice->invoiceDetails()->create([
                'description' => "Tagihan awal penyewa baru untuk bulan " . $entry_date->format('m-Y'),
                'cost' => $room_updated->roomType->cost
            ]);

            DB::commit();

            return $this->success('Data tenant berhasil ditambahkan', $room_updated);
        } catch (Throwable $err) {
            DB::rollBack();
            return $this->fail($err->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $tenant = Tenant::with([
            'services' => fn ($q) => $q->where('status', 'diterima'),
            'additionals' => fn ($q) => $q->where('status', 'pending'),
            'room.roomType',
            'room.kost'
        ])->find($id);

        $total = $tenant->room->roomType->cost;

        $total += $tenant->services->sum('service.cost') + $tenant->additionals->sum('cost') + $tenant->dendas->sum('cost');

        if (!$tenant) {
            return $this->fail('Data tenant tidak ditemukan');
        }

        return $this->success(null, [
            'tenant' => $tenant,
            'total' =>  $total
        ]);
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
        $tenant = Tenant::find($id);

        if (!$tenant) {
            return $this->fail('Data tenant tidak ditemukan');
        }

        DB::beginTransaction();

        try {
            $data = $request->all();
            $data['password'] = Hash::make($data['username'] . substr($data['phone'], -4));
            $tenant->user()->update($data);

            DB::commit();

            return $this->success('Data tenant berhasil diubah');
        } catch (QueryException $e) {
            DB::rollBack();

            switch ($e->getCode()) {
                case 23000:
                    return $this->fail("Username atau nomor HP telah digunakan");
                default:
                    return $this->fail("Terjadi kesalahan mengubah biodata tenant: {$e->getMessage()}");
            }
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
        $tenant = Tenant::find($id);
        $tenant->room()->update([
            'tenant_id' => null
        ]);

        if (!$tenant->delete()) {
            return $this->fail('Terjadi kesalahan menghapus data tenant');
        }

        Storage::delete("tenant_$id.jpeg");

        return $this->success('Data tenant berhasil dihapus');
    }

    public function addTagihan(Request $request, $id)
    {
        try {
            $tenant = Tenant::find($id);
            $additional = $tenant->additionals()->create([
                'cost' => $request->cost,
                'description' => $request->description,
            ]);

            return $this->success('Tagihan berhasil ditambahkan', $additional);
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            return $this->fail('Terjadi kesalahan menambah tagihan');
        }
    }

    public function konfirmasiPembayaran(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $tenant = Tenant::with([
                'room.roomType',
                'services' => fn ($q) => $q->where('status', 'diterima'),
                'dendas' => fn ($q) => $q->where('status', 'pending'),
                'additionals' => fn ($q) => $q->where('status', 'pending')
            ])->find($id);

            if (!$tenant) {
                return $this->fail('Data tenant tidak ditemukan');
            }

            $kost = $tenant->room->roomType->kost;
            $dueDate = clone $tenant;
            $dueDate = Carbon::parse($dueDate->due_date);
            $dueDateBaru = clone $tenant;
            $dueDateBaru = Carbon::parse($dueDateBaru->due_date)->addMonths(1)->format('Y-m-d');

            $invoice = $tenant->invoices()->create([
                'kost_id' => $tenant->room->kost->id,
                'total' => $request->total,
                'date' => $request->date,
                'nama' => $tenant->user->name,
                'no_kamar' => $tenant->room->no_kamar,
                'tanggal_tagihan' => Carbon::parse($tenant->due_date)->copy()->format('m-Y'),
                'description' => "Tagihan tenant untuk bulan " . Carbon::parse($tenant->due_date)->copy()->format('m-Y')
            ]);

            $tenant->update([
                'due_date' => $dueDateBaru
            ]);

            $tagihan = [
                'cost' => $tenant->room->roomType->cost,
                'description' => "Tagihan kamar jenis {$tenant->room->roomType->name}"
            ];

            $services = $tenant->services->map(function ($ts, $key) {
                $tanggal = Carbon::parse($ts->service->created_at)->format('d-m-Y');
                $ts->update(['status' => 'selesai']);

                return [
                    'description' => "Service {$ts->service->name} pada {$tanggal}",
                    'cost' => $ts->service->cost
                ];
            });

            $additionals = $tenant->additionals->map(function ($additional, $key) {
                $additional->update(['status' => 'dibayar']);

                return [
                    'description' => $additional->description,
                    'cost' => $additional->cost
                ];
            });

            $now = Carbon::now();
            $mulaiDenda = $dueDate->copy()->addDays($kost->denda_berlaku);
            $dendaHari = $mulaiDenda->diffInDays($now, false);

            if ($dendaHari > 0) {
                $denda = $tenant->dendas()->create([
                    'title' => 'Denda',
                    'description' => "Denda keterlambatan selama {$dendaHari} hari",
                    'cost' => (int) ceil($dendaHari / $kost->interval_denda) * $kost->nominal_denda,
                    'status' => 'dibayar'
                ]);

                $dendas = [
                    'description' => "Denda keterlambatan selama {$dendaHari} hari",
                    'cost' => $denda->cost
                ];

                $invoice->invoiceDetails()->createMany([
                    $tagihan,
                    ...$services,
                    ...$additionals,
                    $dendas
                ]);
            } else {
                $invoice->invoiceDetails()->createMany([
                    $tagihan,
                    ...$services,
                    ...$additionals
                ]);
            }

            foreach ($tenant->dendas as $denda) {
                $denda->update(['status' => 'dibayar']);
            }

            $tenant->notifications()->create([
                'message' => "Konfirmasi pembayaran tagihan anda untuk tanggal {$dueDate->copy()->format('m-Y')} telah dikonfirmasi"
            ]);

            DB::commit();

            return $this->success('Konfirmasi pembayaran sukses');
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            DB::rollback();

            return $this->fail('Terjadi kesalahan mengonfirmasi pembayaran');
        }
    }

    public function perpanjang(Request $request, $id)
    {
        try {
            $tenant = Tenant::find($id);
            $leaveDate = Carbon::parse($tenant->leave_date)->copy()->addMonths($request->durasi);
            $tenant->update([
                'leave_date' => $leaveDate->format('Y-m-d')
            ]);

            $tenant->notifications()->create([
                'message' => "Masa sewa anda telah diperpanjang {$request->durasi} bulan hingga {$leaveDate->format('Y-m-d')}"
            ]);

            return $this->success('Perpanjangan lama menyewa berhasil');
        } catch (Throwable $e) {
            return $this->fail('Terjadi kesalahan perpanjangan');
        }
    }

    public function gantiPassword(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $tenant = Tenant::find($id);
            $tenant->user()->update([
                'password' => Hash::make($request->pass)
            ]);

            DB::commit();
            return $this->success('Password berhasil diubah');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return $this->fail('Terjadi kesalahan sistem');
        }
    }

    public function jatuhTempo(Request $request)
    {
        $kost = Kost::find($request->kost);
        $tenants = Tenant::query()
            ->with(['user', 'room'])
            ->whereNull('deleted_at')
            ->whereHas('room.roomType', function ($q) use ($kost) {
                return $q->where('kost_id', $kost->id);
            })
            ->whereRaw('DATEDIFF(due_date, current_date) >= 0 AND DATEDIFF(due_date, current_date) <= 14')
            ->get();

        return $this->success(null, $tenants);
    }
}
