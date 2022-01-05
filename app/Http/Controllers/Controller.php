<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function success(String $msg = null, $data = null)
    {
        if (!$msg && $data) {
            return response()->json([
                'status' => 'OK',
                'data' => $data
            ], 200);
        }

        if ($msg && !$data) {
            return response()->json([
                'status' => 'OK',
                'msg' => $msg
            ], 200);
        }

        return response()->json([
            'status' => 'OK',
            'msg' => $msg,
            'data' => $data
        ], 200);
    }

    public function fail(String $msg)
    {
        return response()->json([
            'status' => 'FAIL',
            'msg' => $msg
        ], 200);
    }
}
