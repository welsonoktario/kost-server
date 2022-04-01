<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\Support\Carbon;

class TenantServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tenantServices = TenantService::all();

        return $this->success(null, $tenantServices);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Log::debug($request->all());
        try {
            $tenant = Tenant::find($request->tenant);
            $services = collect($request->services)->map(function ($s, $key) use ($request) {
                return [
                    'service_id' => $s,
                    'tanggal' => $request->tanggal ?: Carbon::now()->format('Y-m-d')
                ];
            });

            $tenant->services()->createMany($services);
        } catch (Throwable $e) {
            Log::error($e);
            return $this->fail('Terjadi kesalahan mengajukan service');
        }

        return $this->success('Berhasil mengajukan service');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
