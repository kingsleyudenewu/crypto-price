<?php

namespace App\Http\Controllers;

use App\Models\CryptoPair;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CryptoPairController extends Controller
{
    public function __invoke(): \Illuminate\Http\JsonResponse
    {
        $pairs = Cache::remember('crypto_pairs', 60, function () {
            return CryptoPair::all();
        });

        return response()->json(['message' => 'success', 'data' => $pairs]);
    }
}
