<?php

namespace App\Http\Controllers;

use App\Models\Kost;
use App\Models\Room;
use Illuminate\Http\Request;
use Throwable;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $rooms = Room::with('tenant.user')
            ->whereHas('roomType.kost', fn ($q) => $q->where('id', $request->kost))
            ->where('room_type_id', $request->type)
            ->get();

        return $this->success(null, $rooms);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $room = Room::with([
            'roomType',
            'tenant.user',
            'tenant.services' => fn ($q) => $q->where('status', 'diterima'),
            'tenant.additionals' => fn ($q) => $q->where('status', 'pending'),
            'tenant.dendas' => fn ($q) => $q->where('status', 'pending'),
        ])->find($id);

        $total = $room->roomType->cost;

        if (!$room) {
            return $this->fail('Data room tidak ditemukan');
        }

        $total += $room->tenant->services->sum('service.cost') + $room->tenant->additionals->sum('cost') + $room->tenant->dendas->sum('cost');
        $room['total'] = $total;

        return $this->success(null, $room);
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
        $room = Room::with('tenant')->find($id);

        if (!$room) {
            return $this->fail('Data room tidak ditemukan');
        }

        try {
            $room->update($request->all());
        } catch (Throwable $e) {
            return $this->fail($e->getMessage());
        }

        return $this->success('Data room berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $room = Room::find($id);
        if (!$room->destroy()) {
            return $this->fail('Terjadi kesalahan manghapus room');
        }

        try {
            $kost = Kost::find($room->kost_id);
            $kost->update(['room_count' => $kost->room_count - 1]);
        } catch (Throwable $e) {
            return $this->fail($e->getMessage());
        }

        return $this->success('Data room berhasil dihapus');
    }
}
