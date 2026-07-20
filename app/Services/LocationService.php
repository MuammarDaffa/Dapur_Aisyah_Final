<?php

namespace App\Services;

use App\Models\Kecamatan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LocationService
{
    /**
     * Validasi apakah koordinat atau informasi wilayah berada di dalam Kota Pontianak.
     *
     * @param float|string|null $latitude
     * @param float|string|null $longitude
     * @param int|string|null $districtId
     * @param string|null $districtName
     * @param string|null $address
     * @return array{is_in_pontianak: bool, message: string, kecamatan_id: int|null, district_name: string|null}
     */
    public static function validateLocation($latitude = null, $longitude = null, $districtId = null, $districtName = null, $address = null): array
    {
        $lat = is_numeric($latitude) ? (float) $latitude : null;
        $lng = is_numeric($longitude) ? (float) $longitude : null;

        // 1. Cek bounding box kasar Kalimantan Barat / sekitar Pontianak terlebih dahulu
        if ($lat !== null && $lng !== null) {
            if ($lat < -0.25 || $lat > 0.15 || $lng < 109.15 || $lng > 109.55) {
                return [
                    'is_in_pontianak' => false,
                    'message' => 'Lokasi berada di luar wilayah Pontianak.',
                    'kecamatan_id' => null,
                    'district_name' => $districtName
                ];
            }
        }

        // 2. Cek jika kecamatan_id valid di database (seluruh kecamatan di tabel kecamatan adalah kecamatan Kota Pontianak)
        if (!empty($districtId)) {
            $kecamatan = Kecamatan::find($districtId);
            if ($kecamatan && self::isDistrictInPontianak($kecamatan->name)) {
                return [
                    'is_in_pontianak' => true,
                    'message' => 'Lokasi berada di wilayah Pontianak.',
                    'kecamatan_id' => $kecamatan->id,
                    'district_name' => $kecamatan->name
                ];
            }
        }

        // 3. Cek berdasarkan teks districtName / address yang dikirimkan dari Nominatim (Frontend)
        if (!empty($districtName) || !empty($address)) {
            $checkText = strtolower(trim(($districtName ?? '') . ' ' . ($address ?? '')));

            // Kata kunci wilayah di luar Kota Pontianak (tetangga atau kabupaten lain)
            $outsideKeywords = [
                'kubu raya', 'sungai raya', 'kakap', 'sungai kakap', 'sungai ambawang', 'ambawang',
                'rasau jaya', 'kuala mandor', 'mempawah', 'jongkat', 'siantan hilir kab', 'landak',
                'sanggau', 'singkawang', 'ketapang', 'sambas', 'bengkayang', 'sintang', 'kabupaten pontianak'
            ];

            foreach ($outsideKeywords as $keyword) {
                if (str_contains($checkText, $keyword)) {
                    return [
                        'is_in_pontianak' => false,
                        'message' => 'Lokasi berada di luar wilayah Pontianak.',
                        'kecamatan_id' => null,
                        'district_name' => $districtName
                    ];
                }
            }

            // Cek apakah match dengan salah satu dari 6 kecamatan Kota Pontianak di database
            $pontianakDistricts = Kecamatan::all();
            foreach ($pontianakDistricts as $dist) {
                if (str_contains($checkText, strtolower($dist->name))) {
                    return [
                        'is_in_pontianak' => true,
                        'message' => 'Lokasi berada di wilayah Pontianak.',
                        'kecamatan_id' => $dist->id,
                        'district_name' => $dist->name
                    ];
                }
            }

            // Cek indikasi kuat Kota Pontianak
            if (str_contains($checkText, 'pontianak') && !str_contains($checkText, 'kabupaten')) {
                return [
                    'is_in_pontianak' => true,
                    'message' => 'Lokasi berada di wilayah Pontianak.',
                    'kecamatan_id' => null,
                    'district_name' => $districtName
                ];
            }

            // Jika dari address/districtName sudah ada teks tetapi tidak ada kata pontianak sama sekali (misal kota lain)
            if (!empty($districtName)) {
                return [
                    'is_in_pontianak' => false,
                    'message' => 'Lokasi berada di luar wilayah Pontianak.',
                    'kecamatan_id' => null,
                    'district_name' => $districtName
                ];
            }
        }

        // 4. Jika hanya tersedia koordinat tanpa informasi teks dari frontend, periksa koordinat (via OSM backend / bounding box)
        if ($lat !== null && $lng !== null) {
            $geoResult = self::checkCoordinatesViaOSM($lat, $lng);
            if ($geoResult !== null) {
                return $geoResult;
            }

            // Fallback bounding box ketat Kota Pontianak jika geocode backend tidak tersedia/timeout
            $inTightBounds = ($lat >= -0.09 && $lat <= 0.06 && $lng >= 109.28 && $lng <= 109.39);
            return [
                'is_in_pontianak' => $inTightBounds,
                'message' => $inTightBounds ? 'Lokasi berada di wilayah Pontianak.' : 'Lokasi berada di luar wilayah Pontianak.',
                'kecamatan_id' => null,
                'district_name' => null
            ];
        }

        return [
            'is_in_pontianak' => false,
            'message' => 'Lokasi berada di luar wilayah Pontianak.',
            'kecamatan_id' => null,
            'district_name' => null
        ];
    }

    /**
     * Memeriksa apakah nama kecamatan termasuk di Kota Pontianak.
     */
    public static function isDistrictInPontianak(string $districtName): bool
    {
        $name = strtolower(trim($districtName));
        return str_contains($name, 'pontianak') && !str_contains($name, 'kabupaten');
    }

    /**
     * Pengecekan koordinat via reverse geocoding Nominatim dari server backend.
     */
    protected static function checkCoordinatesViaOSM(float $lat, float $lng): ?array
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'DapurAisyah-Catering/1.0'
            ])->timeout(3)->get("https://nominatim.openstreetmap.org/reverse", [
                'format' => 'json',
                'lat' => $lat,
                'lon' => $lng,
                'zoom' => 14,
                'addressdetails' => 1
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['address'])) {
                    $addr = $data['address'];
                    $city = $addr['city'] ?? $addr['town'] ?? $addr['county'] ?? $addr['state_district'] ?? '';
                    $cityDistrict = $addr['city_district'] ?? $addr['suburb'] ?? '';

                    $fullText = strtolower($city . ' ' . $cityDistrict . ' ' . ($data['display_name'] ?? ''));

                    // Cek non-pontianak
                    if (str_contains($fullText, 'kubu raya') || str_contains($fullText, 'mempawah') || str_contains($fullText, 'landak')) {
                        return [
                            'is_in_pontianak' => false,
                            'message' => 'Lokasi berada di luar wilayah Pontianak.',
                            'kecamatan_id' => null,
                            'district_name' => $cityDistrict ?: $city
                        ];
                    }

                    // Match dengan DB kecamatan
                    $pontianakDistricts = Kecamatan::all();
                    foreach ($pontianakDistricts as $dist) {
                        if (str_contains($fullText, strtolower($dist->name))) {
                            return [
                                'is_in_pontianak' => true,
                                'message' => 'Lokasi berada di wilayah Pontianak.',
                                'kecamatan_id' => $dist->id,
                                'district_name' => $dist->name
                            ];
                        }
                    }

                    if (str_contains($fullText, 'pontianak') && !str_contains($fullText, 'kabupaten')) {
                        return [
                            'is_in_pontianak' => true,
                            'message' => 'Lokasi berada di wilayah Pontianak.',
                            'kecamatan_id' => null,
                            'district_name' => $cityDistrict ?: $city
                        ];
                    }

                    return [
                        'is_in_pontianak' => false,
                        'message' => 'Lokasi berada di luar wilayah Pontianak.',
                        'kecamatan_id' => null,
                        'district_name' => $cityDistrict ?: $city
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning('OSM reverse geocode check failed inside LocationService: ' . $e->getMessage());
        }

        return null;
    }
}
