<?php

namespace App\Http\Controllers;

use App\Events\SensorMeasurementsCreated;
use App\Events\SensorUpdated;
use App\Http\Resources\SensorMeasurementResource;
use App\Http\Resources\SensorResource;
use App\Http\Responses\ApiResponse;
use App\Models\Sensor;
use App\Models\SensorMeasurement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SensorsController extends Controller
{
    public function fetchs(Request $request)
    {
        $sensors = Sensor::with(['measurements' => function($query) {
            $query->orderBy('measured_at')
                  ->limit(100);
        }])->get();
        
        return ApiResponse::success(
            message: 'Sensors récupérés avec succès',
            data: SensorResource::Collection($sensors)
        );
    }

    public function fetchMeasurements(Request $request)
    {
        $sensorMeasurements = Sensor::with(['measurements' => function($query) {
            $query->limit(100);
        }])->get();

        return ApiResponse::success(
            message: 'Sensor measurements récupérés avec succès',
            data: SensorMeasurementResource::Collection($sensorMeasurements)
        );
    }

    public function storeMeasurement(Request $request)
    {
        $validated = $request->validate([
            'measures' => 'required|array',
            'measures.*.mac' => 'required|string|max:17',
            'measures.*.sequence' => 'required|integer',
            'measures.*.temperature' => 'required|numeric',
            'measures.*.battery_mv' => 'required|integer',
            'measures.*.measured_at' => 'required|date',
        ]);

        $measures = collect($validated['measures']);
        $this->store($measures);

        return ApiResponse::noContent();
    }

    private function store(Collection $measures): void
    {
        $measuresByMac = $measures->groupBy('mac');
        
        $knownSensors = Sensor::whereIn('mac', $measuresByMac->keys())
            ->get()
            ->keyBy('mac');

        if ($knownSensors->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($measuresByMac, $knownSensors) {
            // Préparer les données pour upsert des mesures
            $measurementsToUpsert = [];
            $sensorUpdates = [];
            $createdMeasurements = []; // Pour l'événement

            foreach ($measuresByMac as $mac => $macMeasures) {
                if (!$knownSensors->has($mac)) {
                    continue;
                }

                $sensor = $knownSensors[$mac];
                $lastMeasure = collect($macMeasures)->last();

                // Préparer les mesures pour upsert
                foreach ($macMeasures as $measure) {
                    $measurementData = [
                        'sensor_id' => $sensor->id,
                        'sequence' => $measure['sequence'],
                        'temperature' => $measure['temperature'],
                        'measured_at' => Carbon::parse($measure['measured_at']),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    
                    $measurementsToUpsert[] = $measurementData;
                    
                    // Garder une copie pour l'événement (avec les infos du capteur)
                    $createdMeasurements[] = array_merge($measurementData, [
                        'sensor_mac' => $mac,
                        'sensor_name' => $sensor->name ?? null,
                    ]);
                }

                // Préparer les mises à jour des capteurs
                $sensorUpdates[] = [
                    'id' => $sensor->id,
                    'battery_mv' => $lastMeasure['battery_mv'],
                    'last_temp' => $lastMeasure['temperature'],
                    'updated_at' => now(),
                ];
            }

            // Upsert des mesures (évite les duplicatas)
            if (!empty($measurementsToUpsert)) {
                SensorMeasurement::upsert(
                    $measurementsToUpsert,
                    ['sensor_id', 'sequence'], // Clés uniques
                    ['temperature', 'measured_at', 'updated_at'] // Colonnes à mettre à jour
                );
            }

            // Mettre à jour les capteurs (pas d'upsert car ils existent déjà)
            if (!empty($sensorUpdates)) {
                $sensorUpsertData = [];
                foreach ($sensorUpdates as $update) {
                    $sensor = $knownSensors->firstWhere('id', $update['id']);
                    $sensorUpsertData[] = [
                        'id' => $update['id'],
                        'mac' => $sensor->mac,
                        'name' => $sensor->name,
                        'battery_mv' => $update['battery_mv'],
                        'last_temp' => $update['last_temp'],
                        'created_at' => $sensor->created_at,
                        'updated_at' => $update['updated_at'],
                    ];
                }

                Sensor::upsert(
                    $sensorUpsertData,
                    ['id'],
                    ['battery_mv', 'last_temp', 'updated_at']
                );

                $updatedSensors = Sensor::whereIn('id', collect($sensorUpdates)->pluck('id'))->get();
                SensorUpdated::dispatch($updatedSensors);
            }

            // Envoyer un seul événement avec les vraies mesures depuis la DB
            //if (!empty($createdMeasurements)) {
                // Récupérer les vraies mesures qui viennent d'être créées
                // $sensorIds = collect($createdMeasurements)->pluck('sensor_id')->unique();
                // $sequences = collect($createdMeasurements)->pluck('sequence');
                
                // $realMeasurements = SensorMeasurement::whereIn('sensor_id', $sensorIds)
                //     ->whereIn('sequence', $sequences)
                //     ->with('sensor')
                //     ->get();

                // SensorMeasurementsCreated::dispatch($realMeasurements);
            //}
        });
    }
}
