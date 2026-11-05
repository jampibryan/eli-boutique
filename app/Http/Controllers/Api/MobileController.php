<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MobileAccess;
use App\Services\MobileAuthService;
use App\Services\MobileReadService;
use Illuminate\Http\Request;

class MobileController extends Controller
{
    public function login(Request $request, MobileAuthService $auth)
    {
        $input = $request->validate(['email' => 'required|email|max:254',
            'password' => 'required|string|max:1024', 'device_id' => 'required|string|min:16|max:128',
            'two_factor_code' => 'nullable|digits:6']);
        return response()->json(['success' => true, 'data' => $auth->login($input)])
            ->header('Cache-Control', 'no-store');
    }

    public function me(Request $request, MobileAccess $access)
    {
        return response()->json(['success' => true, 'data' => $access->profile($request->user())]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['success' => true]);
    }

    public function dashboard(Request $request, MobileReadService $service)
    {
        app(MobileAccess::class)->authorize($request->user(), 'dashboard');
        return response()->json(['success' => true, 'data' => $service->dashboard($request->user())]);
    }

    public function charts(Request $request, MobileReadService $service)
    {
        app(MobileAccess::class)->authorize($request->user(), 'graficos');
        $input = $request->validate(['from' => 'nullable|date_format:Y-m-d',
            'to' => 'nullable|date_format:Y-m-d|after_or_equal:from']);
        return response()->json(['success' => true, 'data' => $service->charts($input)]);
    }

    public function index(Request $request, string $module, MobileReadService $service)
    {
        app(MobileAccess::class)->authorize($request->user(), $module);
        $input = $request->validate(['q' => 'nullable|string|max:100', 'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:50', 'state' => 'nullable|string|max:40',
            'from' => 'nullable|date_format:Y-m-d', 'to' => 'nullable|date_format:Y-m-d|after_or_equal:from']);
        return response()->json($service->listing($module, $input));
    }

    public function show(Request $request, string $module, int $id, MobileReadService $service)
    {
        app(MobileAccess::class)->authorize($request->user(), $module);
        return response()->json(['success' => true, 'data' => $service->detail($module, $id)]);
    }
}
