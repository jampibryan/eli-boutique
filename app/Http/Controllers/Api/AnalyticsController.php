<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsSnapshotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function snapshot(Request $request, AnalyticsSnapshotService $service): JsonResponse
    {
        abort_unless($request->user()->tokenCan('read'), 403);

        return response()->json($service->snapshot())
            ->header('Cache-Control', 'private, no-store');
    }
}
