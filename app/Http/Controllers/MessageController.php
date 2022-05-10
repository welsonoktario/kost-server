<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatRoom;
use App\Models\Message;
use Throwable;
use App\Models\Kost;
use Illuminate\Support\Facades\Auth;
use App\Models\Tenant;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $chatRoom = ChatRoom::query()
            ->with('messages')
            ->firstOrCreate([
                'kost_id' => $request->kost,
                'tenant_id' => $request->tenant
            ]);

        return $this->success(null, $chatRoom);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            $chatRoom = ChatRoom::query()->find($request->chatRoom);

            if ($user->type == 'Owner') {
                $message = $chatRoom->messages()
                    ->create([
                        'is_owner' => true,
                        'message' => $request->message
                    ]);

                Tenant::query()
                    ->find($chatRoom->tenant_id)
                    ->notifications()
                    ->create([
                        'message' => 'Anda mendapatkan pesan baru'
                    ]);
            } elseif ($user->type == 'Tenant') {
                Kost::query()
                    ->find($chatRoom->kost_id)
                    ->notifications()
                    ->create([
                        'message' => 'Anda mendapatkan pesan baru dari ' . $user->tenant->room->no_kamar
                    ]);
            }

            return $this->success(null, $message);
        } catch (Throwable $e) {
            return $this->fail("Pesan gagal terkirim: {$e->getMessage()}");
        }
    }
}
