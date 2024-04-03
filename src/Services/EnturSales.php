<?php

namespace Ragnarok\Entur\Services;

use Ragnarok\Sink\Traits\LogPrintf;
use Ragnarok\Sink\Services\SinkDisk;
use Ragnarok\Sink\Models\SinkFile;
use Ragnarok\Entur\Sinks\SinkEnturSales;
use Ragnarok\Sink\Services\ChunkArchive;

class EnturSales {
    use LogPrintf;


    protected $sinkDisk;


    public function __construct() {
        $disk = new SinkDisk(SinkEnturSales::$id);
        $this->sinkDisk = $disk->getDisk();
        $this->logPrintfInit("[EnTur CLEOS]: ");
    }

    public function download($chunkId)
    {
        $this->debug("ChunkID: %s", $chunkId);
        $archive = new ChunkArchive(SinkEnturSales::$id, $chunkId);
        $archive->addFromString("tfk-cleos.csv", $this->sinkDisk->get("tfk-cleos.csv"));
        $archive->save();
        return $archive->getFile();
        //$this->debug("%s", $this->sinkDisk->get("tfk-cleos.csv"));
        //return $this->sinkDisk->get("tfk-cleos.csv");
    }

    public function import(string $chunkId, SinkFile $file) {
        
        return 0;
    }
}