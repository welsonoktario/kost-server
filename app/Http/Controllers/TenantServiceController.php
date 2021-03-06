<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TenantServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($id)
    {
        $tenantServices = TenantService::query()
            ->with(['service', 'tenant.user', 'tenant.room'])
            ->whereHas('service', fn ($q) => $q->where('kost_id', $id))
            ->whereHas('tenant', fn ($q) => $q->whereNull('deleted_at'))
            ->orderBy('created_at', 'DESC')
            ->get();

        return $this->success(null, $tenantServices);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexById($id)
    {
        $tenantServices = TenantService::where('tenant_id', $id)->get();

        return $this->success(null, $tenantServices);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        Log::debug($request->all());
        try {
            $tenant = Tenant::with('user')->find($request->tenant);
            $services = collect($request->services)->map(function ($s, $key) use ($request) {
                return [
                    'service_id' => $s,
                    'tanggal' => $request->tanggal ?: Carbon::now()->format('Y-m-d')
                ];
            });

            $serviceTenants = $tenant->services()->createMany($services);
            $notifications = collect($serviceTenants)->map(function ($st, $i) use ($tenant) {
                return [
                    'message' => "Penyewa kamar no {$tenant->room->no_kamar} mengajukan service {$st->service->name} untuk tanggal {$st->tanggal}"
                ];
            });
            $tenant->room->kost->notifications()->createMany($notifications);
        } catch (Throwable $e) {
            Log::error($e);
            return $this->fail('Terjadi kesalahan mengajukan service');
        }

        return $this->success('Berhasil mengajukan service');
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
        DB::beginTransaction();

        try {
            $aksi = $request->aksi ?: 'diterima';
            $alasan = $request->alasan ?: null;
            $tenantService = TenantService::with(['tenant', 'service'])->find($id);
            $tenantService->update([
                'status' => $aksi,
                'alasan' => $alasan
            ]);

            if ($aksi == 'ditolak') {
                $tenantService->tenant->notifications()->create([
                    'message' => "Pengajuan service {$tenantService->service->name} anda untuk tanggal {$tenantService->tanggal} $aksi. Alasan penolakan: {$alasan}"
                ]);
            } else {
                $tenantService->tenant->notifications()->create([
                    'message' => "Pengajuan service {$tenantService->service->name} anda untuk tanggal {$tenantService->tanggal} $aksi"
                ]);
            }

            DB::commit();

            return $this->success('Pengajuan service diterima');
        } catch (Throwable $e) {
            DB::rollBack();

            return $this->fail($e->getMessage());
        }
    }
}
