<?php

namespace Modules\Inquiries\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestGoogleMapsCommand extends Command
{
    protected $signature = 'google:test-api';
    protected $description = 'اختبار Google Maps API';

    public function handle()
    {
        $apiKey = config('services.google_maps.api_key');

        $this->info('🔍 فحص API Key...');
        $this->line("API Key: " . ($apiKey ? substr($apiKey, 0, 10) . '...' : '❌ غير موجود'));

        if (!$apiKey) {
            $this->error('❌ API Key غير موجود في ملف .env');
            $this->line('أضف السطر التالي في ملف .env:');
            $this->line('GOOGLE_MAPS_API_KEY=your_api_key_here');
            return 1;
        }

        // اختبار 1: Geocoding API
        $this->info("\n📍 اختبار Geocoding API...");
        try {
            $response = Http::timeout(10)->get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => 'Dubai Marina, UAE',
                'key' => $apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if ($data['status'] === 'OK') {
                    $this->info('✅ Geocoding API يعمل بنجاح');
                    $location = $data['results'][0]['geometry']['location'];
                    $this->line("📍 الموقع: Lat {$location['lat']}, Lng {$location['lng']}");
                } else {
                    $this->error("❌ Geocoding API خطأ: " . $data['status']);
                    if (isset($data['error_message'])) {
                        $this->error("الرسالة: " . $data['error_message']);
                    }
                }
            } else {
                $this->error('❌ فشل الاتصال بـ Geocoding API');
                $this->line('Response: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->error('❌ خطأ: ' . $e->getMessage());
        }

        // اختبار 2: Routes API مع FieldMask ✅
        $this->info("\n🛣️ اختبار Routes API...");
        try {
            $body = [
                'origin' => [
                    'location' => [
                        'latLng' => [
                            'latitude' => 24.45388,
                            'longitude' => 54.37734
                        ]
                    ]
                ],
                'destination' => [
                    'location' => [
                        'latLng' => [
                            'latitude' => 25.20485,
                            'longitude' => 55.27078
                        ]
                    ]
                ],
                'travelMode' => 'DRIVE',
                'routingPreference' => 'TRAFFIC_AWARE',
                'computeAlternativeRoutes' => false,
                'units' => 'METRIC'
            ];

            // ✅ الإصلاح: إضافة X-Goog-FieldMask
            $response = Http::timeout(10)
                ->withHeaders([
                    'X-Goog-Api-Key' => $apiKey,
                    'X-Goog-FieldMask' => 'routes.distanceMeters,routes.duration' // ✅ مهم جداً
                ])
                ->post('https://routes.googleapis.com/directions/v2:computeRoutes', $body);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['routes'][0]['distanceMeters'])) {
                    $distanceKm = round($data['routes'][0]['distanceMeters'] / 1000, 2);
                    $this->info("✅ Routes API يعمل بنجاح");
                    $this->line("📏 المسافة من أبوظبي إلى دبي: {$distanceKm} كم");

                    if (isset($data['routes'][0]['duration'])) {
                        $durationMinutes = round((int)rtrim($data['routes'][0]['duration'], 's') / 60);
                        $this->line("⏱️ الوقت المتوقع: {$durationMinutes} دقيقة");
                    }
                } else {
                    $this->error('❌ Routes API: لم يتم العثور على مسار');
                    $this->line('Response: ' . json_encode($data, JSON_UNESCAPED_UNICODE));
                }
            } else {
                $this->error('❌ فشل الاتصال بـ Routes API');
                $this->line('Status: ' . $response->status());
                $this->line('Response: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->error('❌ خطأ: ' . $e->getMessage());
        }

        $this->info("\n✅ انتهى الاختبار");
        return 0;
    }
}
