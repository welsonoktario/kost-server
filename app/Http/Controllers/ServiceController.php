<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Models\Kost;
use Throwable;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $services = Service::query()
            ->where('kost_id', $request->kost)
            ->get();

        return $this->success(null, $services);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $kost = Kost::query()->find($request->kost);
        $service = $kost->services()->create([
            'name' => $request->name,
            'description' => $request->description,
            'cost' => $request->cost
        ]);

        return $this->success(null, $service);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $services = Service::where('kost_id', $id)->get();

        return $this->success(null, $services);
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
            Service::query()
                ->find($id)
                ->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'cost' => $request->cost
                ]);

            return $this->success('Service berhasil diperbarui');
        } catch (Throwable $e) {
            return $this->fail("Terjadi kesalahan sistem: {$e->getMessage()}");
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
        try {
            Service::query()
                ->find($id)
                ->delete();

            return $this->success('Service berhasil dihapus');
        } catch (Throwable $e) {
            return $this->fail("Terjadi kesalahan sistem: {$e->getMessage()}");
        }
    }
}
