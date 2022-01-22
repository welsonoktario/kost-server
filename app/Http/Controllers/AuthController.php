<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomType;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Throwable;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $attempt = Auth::attempt($request->all());

        if (!$attempt) {
            return $this->fail('Username atau password salah');
        }

        $user = User::find($request->username);
        $token = $user->createToken($request->username);

        return $this->success(null, [
            'user' => $user,
            'token' => $token->plainTextToken
        ]);
    }

    public function register(Request $request)
    {
        $kost_req = json_decode($request->kost, true);
        $types_req = array_reverse(json_decode($request->types, true));
        $services_req = array_reverse(json_decode($request->services, true));
        $user_req = $kost_req['user'];
        $user_req['password'] = Hash::make($request->password);

        try {
            $user = User::create($user_req);
            $kost = $user->kost()->create($kost_req);

            foreach ($types_req as $type_req) {
                $type = $kost->roomTypes()->create($type_req);
                $rooms = array_fill(0, $type->room_count, [
                    'room_type_id' => $type->id,
                    'created_at' => now('Asia/Jakarta'),
                    'updated_at' => now('Asia/Jakarta'),
                ]);

                Room::insert($rooms);
            }

            foreach ($services_req as $service_req) {
                $kost->services()->create($service_req);
            }
            $token = $user->createToken($user->username);

            return $this->success('Kost berhasil dibuat', [
                'user' => $user,
                'kost' => $kost,
                'token' => $token->plainTextToken
            ]);
        } catch (Throwable $e) {
            return $this->fail($e->getMessage());
        }
    }
}
