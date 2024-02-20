<?php

namespace Ragnarok\Entur\Sinks;

use Illuminate\Support\Carbon;
use Ragnarok\Entur\Services\Entur;
use Ragnarok\Sink\Models\SinkFile;
use Ragnarok\Sink\Sinks\SinkBase;

/**
 * Sink handler for National stop register (NSR) from Entur
 */
class SinkEnturStops extends SinkEnturBase
{
    public static $id = "entur-stops";
    public static $title = "Entur Stops";
    public $singleState = true;

    /**
     * @var Entur
     */
    protected $entur;

    // Run fetch+import daily at 05:00
    public $cron = '30 04 * * *';

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
        $this->entur->delRouteImport($file);
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
