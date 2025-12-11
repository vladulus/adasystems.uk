<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DvlaVehicleService
{
    private string $apiKey;
    private string $baseUrl;
    private bool $sandbox;

    public function __construct()
    {
        $this->apiKey = config('services.dvla.api_key', '');
        $this->sandbox = config('services.dvla.sandbox', true);
        
        $this->baseUrl = $this->sandbox
            ? 'https://uat.driver-vehicle-licensing.api.gov.uk/vehicle-enquiry/v1/vehicles'
            : 'https://driver-vehicle-licensing.api.gov.uk/vehicle-enquiry/v1/vehicles';
    }

    /**
     * Lookup vehicle details by registration number
     */
    public function lookup(string $registrationNumber): ?array
    {
        // Normalize: uppercase, remove spaces
        $regNumber = strtoupper(str_replace(' ', '', trim($registrationNumber)));

        if (empty($regNumber)) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl, [
                'registrationNumber' => $regNumber,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $this->mapToVehicleFields($data);
            }

            // Log errors for debugging
            Log::warning('DVLA VES API error', [
                'registration' => $regNumber,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('DVLA VES API exception', [
                'registration' => $regNumber,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Map DVLA response to our database fields
     */
    private function mapToVehicleFields(array $data): array
    {
        return [
            // Direct mappings
            'registration_number' => $data['registrationNumber'] ?? null,
            'make' => $data['make'] ?? null,
            'year' => $data['yearOfManufacture'] ?? null,
            'colour' => $data['colour'] ?? null,
            'fuel_type' => $data['fuelType'] ?? null,
            'engine_capacity' => $data['engineCapacity'] ?? null,
            'co2_emissions' => $data['co2Emissions'] ?? null,
            'tax_status' => $data['taxStatus'] ?? null,
            'tax_due_date' => $data['taxDueDate'] ?? null,
            'mot_status' => $data['motStatus'] ?? null,
            'mot_expiry_date' => $data['motExpiryDate'] ?? null,
            'euro_status' => $data['euroStatus'] ?? null,
            'wheelplan' => $data['wheelplan'] ?? null,
            'revenue_weight' => $data['revenueWeight'] ?? null,
            'first_registered_at' => $this->parseMonthDate($data['monthOfFirstRegistration'] ?? null),
            
            // Raw data for reference
            '_raw' => $data,
        ];
    }

    /**
     * Parse DVLA month format (YYYY-MM) to date
     */
    private function parseMonthDate(?string $monthDate): ?string
    {
        if (!$monthDate) {
            return null;
        }

        // Format: "2004-12" -> "2004-12-01"
        if (preg_match('/^\d{4}-\d{2}$/', $monthDate)) {
            return $monthDate . '-01';
        }

        return null;
    }

    /**
     * Check if API key is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Check if running in sandbox mode
     */
    public function isSandbox(): bool
    {
        return $this->sandbox;
    }
}
