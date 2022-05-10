<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complain;
use App\Models\Tenant;

class ComplainController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $complains = Complain::query()
            ->with(['tenant.user', 'tenant.room'])
            ->when(
                $request->kost,
                function ($q) use ($request) {
                    $q->whereHas('tenant.room.roomType', function ($query) use ($request) {
                        return $query->where('kost_id', $request->kost);
                    });
                },
                function ($q) use ($request) {
                    $q->where('tenant_id', $request->tenant);
                }
            )
            ->orderBy('created_at', 'DESC')
            ->get();

        return $this->success(null, $complains);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $tenant = Tenant::find($request->tenant);
        $complain = $tenant->complains()->create([
            'description' => $request->description
        ]);
        $complain = $complain->load('tenant.user');
        $kost = $tenant->room->kost->notifications()->create([
            'message' => "Ruangan {$tenant->room->no_kamar} mengajukan komplain baru"
        ]);

        return $this->success('Komplain berhasil diajukan', $complain);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $complain = Complain::with('tenant')->find($id);

        return $this->success(null, $complain);
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
        $complain = Complain::with('tenant')->find($id);
        $complain->update(['status' => $request->aksi]);

        switch ($request->aksi) {
            case 'diproses':
                $complain->tenant->notifications()->create([
                    'message' => "Komplain anda pada {$complain->created_at} telah diproses"
                ]);
                break;
            case 'ditolak':
                $complain->tenant->notifications()->create([
                    'message' => "Komplain anda pada {$complain->created_at} ditolak"
                ]);
                break;
            default:
                break;
        }

        return $this->success("Komplain berhasil {$request->aksi}");
    }
}
