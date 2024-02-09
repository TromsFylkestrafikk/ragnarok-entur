<?php

namespace Ragnarok\Entur\Sinks;

use Illuminate\Support\Carbon;
use Ragnarok\Entur\Services\Entur;
use Ragnarok\Sink\Models\SinkFile;
use Ragnarok\Sink\Sinks\SinkBase;

/**
 * Entur doesn't provide historic route sets or historic NSR data, so this sink
 * only provides today's sink
 */
class SinkEntur extends SinkBase
{
    public static $id = "entur";
    public static $title = "Entur";
    public $singleState = true;

    /**
     * @var Entur
     */
    protected $entur;

    // Run fetch+import daily at 05:00
    public $cron = '0 05 * * *';

    public function __construct()
    {
        $this->entur = new Entur();
    }

    /**
     * @inheritdoc
     */
    public function destinationTables(): array
    {
        return [
            'netex_active_calls' => 'Activated route data. Should not be needed for statistical purposes',
            'netex_active_journeys' => 'Activated route data. Should not be needed for statistical purposes',
            'netex_active_status' => 'Activation status for route data sets',
            'netex_calendar' => 'Says which dates given calendar ID is active',
            'netex_destination_displays' => 'List of final and interim destinations for all journeys',
            'netex_group_of_stop_places' => 'Parental stop place info.',
            'netex_imports' => 'Laravel NeTEx take on route data import status',
            'netex_journey_pattern_link' => 'Map between journeys and links, i.e. actual journey trace',
            'netex_journey_pattern_stop_point' => 'Maps stops points to journeys',
            'netex_journey_patterns' => 'Maps journeys to route',
            'netex_line_groups' => 'Line groups. Water, bus, train, etc.',
            'netex_lines' => 'List of all available lines',
            'netex_notice_assignments' => 'Map between notice and object that has reference (journey, stop, ...)',
            'netex_notices' => 'The list of actual notices',
            'netex_operators' => 'List of operators in route set',
            'netex_passing_times' => 'List arrival and departure times for journeys. No dates.',
            'netex_route_point_sequence' => 'List of stop points and their sequence for routes',
            'netex_routes' => 'List of routes and lines owning them',
            'netex_service_links' => 'All segments/links available with coordinates and distance',
            'netex_stop_assignments' => 'Maps internal stop point ids to actual NSR quays',
            'netex_stop_place' => 'NSR stop place',
            'netex_stop_place_alt_id' => 'List of old, alternative IDs used for stops',
            'netex_stop_place_group_member' => 'Maps (child) stop points to group of stop places',
            'netex_stop_points' => 'NeTEx internal list of stop places',
            'netex_stop_quay' => 'NSR stop place quay. Refers always to its stop place',
            'netex_stop_quay_alt_id' => 'Old and alternative IDs used on stop quays',
            'netex_stop_tariff_zone' => 'Maps stops to tariff zones',
            'netex_tariff_zone' => 'List of tariff zones with polygon data',
            'netex_topographic_place' => 'Topographic place names with polygon data',
            'netex_vehicle_blocks' => 'Vehicle block list',
            'netex_vehicle_journeys' => 'List of actual journeys to be executed',
            'netex_vehicle_schedules' => 'Maps list of journeys to vehicle blocks',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getFromDate(): Carbon
    {
        // Lets use already provided/stored data as initial date.
        return Carbon::today();
    }

    /**
     * @inheritdoc
     */
    public function getToDate(): Carbon
    {
        return Carbon::today();
    }

    /**
     * Get available chunk IDs.
     *
     * EnTur does not have an available route set history, so we need to build
     * this ourselves. Only previously stored files are available as chunk IDs.
     * And today's route set.
     *
     * @return array
     */
    public function getChunkIds(): array
    {
        $filenames = SinkFile::select('name')
            ->where('sink_id', self::$id)
            ->orderBy('name')
            ->get()
            ->keyBy('name')
            ->pluck('name')
            ->toArray();
        $today = today()->format('Y-m-d');
        $chunkIds = [$today => $today];
        foreach ($filenames as $filename) {
            $id = $this->filenameToChunkId(basename($filename));
            if ($id) {
                $chunkIds[$id] = $id;
            }
        }
        return $chunkIds;
    }

    /**
     * @inheritdoc
     */
    public function fetch(string $id): SinkFile|null
    {
        return $this->entur->downloadRouteset()->getFile();
    }

    /**
     * @inheritdoc
     */
    public function import(string $chunkId, SinkFile $file): int
    {
        return $this->entur->importRouteset($file);
    }

    /**
     * @inheritdoc
     */
    public function deleteImport(string $id, SinkFile $file): bool
    {
        $this->entur->delImport($file);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function filenameToChunkId(string $filename): string|null
    {
        return $this->entur->getDateFromFilename($filename);
    }
}
