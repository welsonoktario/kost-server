<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Kost;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $notifications = Notification::query()
            ->whereHasMorph(
                'notificationable',
                [Kost::class, Tenant::class],
                function (Builder $query) use ($request) {
                    $query->where('user_username', $request->user);
                }
            )
            ->orderBy('created_at', 'DESC')
            ->get();

        if (!$notifications && !count($notifications)) {
            return $this->fail('Terjadi kesalahan memuat data notifikasi');
        }

        return $this->success(null, $notifications);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $notification = Notification::find($id);

        if (!$notification) {
            return $this->fail('Data notifikasi tidak ditemukan');
        }

        return $this->success(null, $notification);
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
        Log::debug("ID notif: $id");
        $notification = Notification::find($id);
        $notification->update([
            'is_read' => true
        ]);

        return $this->success();
    }
}
