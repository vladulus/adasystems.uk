<?php

namespace App\Http\Controllers;

use App\Services\DvlaVehicleService;
use App\Services\DvlaDriverService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DvlaController extends Controller
{
    /**
     * Lookup vehicle by registration number (VES API)
     */
    public function lookupVehicle(Request $request, DvlaVehicleService $dvla): JsonResponse
    {
        $request->validate([
            'registration' => 'required|string|max:15',
        ]);

        if (!$dvla->isConfigured()) {
            return response()->json([
                'success' => false,
                'error' => 'DVLA API not configured. Please add DVLA_API_KEY to .env',
            ], 503);
        }

        $result = $dvla->lookup($request->registration);

        if ($result) {
            // Remove raw data from response
            unset($result['_raw']);
            
            return response()->json([
                'success' => true,
                'data' => $result,
                'sandbox' => $dvla->isSandbox(),
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => 'Vehicle not found or DVLA API error',
        ], 404);
    }

    /**
     * Lookup driver by check code (Driver Data API)
     */
    public function lookupDriver(Request $request, DvlaDriverService $dvla): JsonResponse
    {
        $request->validate([
            'check_code' => 'required|string|size:8',
            'last_name' => 'required|string|max:100',
            'licence_number' => 'required|string|min:8',
        ]);

        $result = $dvla->lookup(
            $request->check_code,
            $request->last_name,
            $request->licence_number
        );

        if ($result) {
            // Remove raw data from response
            unset($result['_raw']);
            
            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => 'Driver not found, check code expired, or API error. Check code is valid for 21 days.',
        ], 404);
    }

    /**
     * Get test registration numbers (for sandbox testing)
     */
    public function getTestRegistrations(DvlaVehicleService $dvla): JsonResponse
    {
        if (!$dvla->isSandbox()) {
            return response()->json([
                'success' => false,
                'error' => 'Test registrations only available in sandbox mode',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'test_registrations' => [
                'AA19AAA' => 'Valid vehicle with all data',
                'AA19MOT' => 'Vehicle with valid MOT',
                'AA19TAX' => 'Vehicle with valid tax',
                'ER19BAD' => 'Returns 400 error (for testing error handling)',
                'AA19EXP' => 'Vehicle with expired MOT',
            ],
        ]);
    }
}
