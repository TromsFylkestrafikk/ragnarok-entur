<?php

namespace Ragnarok\Entur\Sinks;

use Illuminate\Support\Carbon;
use Ragnarok\Entur\Services\Entur;
use Ragnarok\Sink\Models\SinkFile;
use Ragnarok\Sink\Sinks\SinkBase;

/**
 * Shared stuff between entur sinks.
 */
abstract class SinkEnturBase extends SinkBase
{
    public $singleState = true;

    /**
     * @var Entur
     */
    protected $entur;

    public function __construct()
    {
        $this->entur = new Entur();
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
}
