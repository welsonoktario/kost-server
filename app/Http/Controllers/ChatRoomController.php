<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatRoom;

class ChatRoomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $chatRooms = ChatRoom::query()
            ->with('tenant')
            ->where('kost_id', $request->kost)
            ->get();

        return $this->success(null, $chatRooms);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        ChatRoom::query()
            ->find($id)
            ->delete();

        return $this->success('Chat berhasil dihapus');
    }
}
