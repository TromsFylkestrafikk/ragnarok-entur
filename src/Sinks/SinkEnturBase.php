<?php

namespace Ragnarok\Entur\Sinks;

use Illuminate\Support\Carbon;
use Ragnarok\Sink\Models\SinkFile;
use Ragnarok\Sink\Sinks\SinkBase;

/**
 * Shared stuff between entur sinks.
 */
abstract class SinkEnturBase extends SinkBase
{
    public $singleState = true;

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
     * Use existing entries in the SinkFile table as base.
     *
     * @return array
     */
    public function getChunkIds(): array
    {
        $filenames = SinkFile::select('name')
            ->where('sink_id', static::$id)
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
