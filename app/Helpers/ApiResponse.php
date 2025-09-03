<<<<<<< HEAD
<?php

namespace App\Helpers;

class ApiResponse
{
    static function sendResponse($code = '201', $msg = null, $data = null)
    {
        $response = [
            'status' => $code,
            'msg' => $msg,
            'data' => $data,
        ];
        return response()->json($response, $code);
    }
}
=======
<?php

namespace App\Helpers;

class ApiResponse
{
    static function sendResponse($code = '201', $msg = null, $data = null)
    {
        $response = [
            'status' => $code,
            'msg' => $msg,
            'data' => $data,
        ];
        return response()->json($response, $code);
    }
}
>>>>>>> e3fb70c (First commit)
