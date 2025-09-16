<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WpLogoutController extends Controller
{
    public function logout(Request $request)
    {
        // Remove the WordPress user session
        session()->forget('wp_user_id');
        session()->flush();

        if (!session('wp_user_id')) {
            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out',
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to logged out',
        ], 400);

    }
}
