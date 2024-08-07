<?php

namespace Ragnarok\Entur\Sinks;

use Exception;
use Ragnarok\Entur\Services\EnturStops;
use Ragnarok\Sink\Models\SinkFile;

/**
 * Sink handler for National stop register (NSR) from Entur
 */
class SinkEnturStops extends SinkEnturBase
{
    public static $id = "entur-stops";
    public static $title = "Entur Stops";
    public $singleState = true;
    public static $docfileName = "docs/SINK-STOPS.md";

    /**
     * Run import daily at 04:30
     *
     * @var string
     */
    public $cron = '30 04 * * *';

    /**
     * @var EnturStops
     */
    protected $enturStops;

    public function __construct()
    {
        $this->enturStops = new EnturStops();
    }

    /**
     * @inheritdoc
     */
    public function destinationTables(): array
    {
        return [
            'netex_stop_place' => 'NSR stop place',
            'netex_stop_place_alt_id' => 'List of old, alternative IDs used for stops',
            'netex_stop_place_group_member' => 'Maps (child) stop points to group of stop places',
            'netex_stop_points' => 'NeTEx internal list of stop places',
            'netex_stop_quay' => 'NSR stop place quay. Refers always to its stop place',
            'netex_stop_quay_alt_id' => 'Old and alternative IDs used on stop quays',
            'netex_stop_tariff_zone' => 'Maps stops to tariff zones',
            'netex_tariff_zone' => 'List of tariff zones with polygon data',
            'netex_topographic_place' => 'Topographic place names with polygon data',
        ];
    }

    /**
     * @inheritdoc
     */
    public function fetch(string $chunkId): SinkFile|null
    {
        $today = today()->format('Y-m-d');
        if ($chunkId !== $today) {
            throw new Exception('Entur only provides todays NSR set.');
        }
        return $this->enturStops->downloadStops($chunkId);
    }

    /**
     * @inheritdoc
     */
    public function import(string $chunkId, SinkFile $file): int
    {
        return $this->enturStops->importFromSink($file);
    }

    /**
     * @inheritdoc
     */
    public function deleteImport(string $id, SinkFile $file): bool
    {
        // laravel_netex package doesn't have prune/truncate-function. Do it
        // manually.
        collect([
            'StopPlace',
            'StopQuay',
            'GroupOfStopPlaces',
            'TariffZone',
            'TopographicPlace',
        ])->each(function ($modelName) {
            $modelClass = 'TromsFylkestrafikk\\Netex\\Models\\' . $modelName;
            $modelClass::truncate();
        });
        return true;
    }

    /**
     * @inheritdoc
     */
    public function filenameToChunkId(string $filename): string|null
    {
        $matches = [];
        $hits = preg_match('|^(?P<date>\d{4}-\d{2}-\d{2})\.zip$|', $filename, $matches);
        return $hits ? $matches['date'] : null;
    }
}
