<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $attempt = Auth::attempt($request->all());

        if (!$attempt) {
            return $this->fail('Username atau password salah');
        }

        $user = User::with(['tenant', 'kost'])->find($request->username);
        $token = $user->createToken($request->username);

        return $this->success(null, [
            'user' => $user,
            'token' => $token->plainTextToken
        ]);
    }

    public function register(Request $request)
    {
        $kost_req = $request->kost;
        $types_req = array_reverse($request->types);
        $services_req = array_reverse($request->services);
        $user_req = $kost_req['user'];
        $user_req['password'] = Hash::make($request->password);

        DB::beginTransaction();

        try {
            $user = User::create($user_req);
            $kost = $user->kost()->create($kost_req);

            foreach ($types_req as $type_req) {
                $type = $kost->roomTypes()->create($type_req);
                $no_kamar = collect(range(1, $type->room_count));

                $rooms = $no_kamar->map(function ($no) use ($type) {
                    return [
                        'room_type_id' => $type->id,
                        'no_kamar' => "{$type->name} $no"
                    ];
                });
                $type->rooms()->createMany($rooms);
            }

            foreach ($services_req as $service_req) {
                $kost->services()->create($service_req);
            }
            $user = $user->load(['tenant', 'kost']);
            $token = $user->createToken($user->username);

            DB::commit();

            return $this->success('Kost berhasil dibuat', [
                'user' => $user,
                'token' => $token->plainTextToken
            ]);
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->fail($e->getMessage());
        }
    }
}
