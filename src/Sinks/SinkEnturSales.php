<?php

namespace Ragnarok\Entur\Sinks;

use Ragnarok\Entur\Services\EnturSales;
use Ragnarok\Sink\Models\SinkFile;

class SinkEnturSales extends SinkEnturBase
{
    public static $id = "entur-sales";
    public static $title = "Entur Salesdata";
    public $cron = '30 04 * * *';
    

    //protected $service;

    public function __construct(protected $service = null)
    {
        if($service == null) {
            $this->service = new EnturSales();
        }
    }

    public function import(string $chunkId, SinkFile $file): int
    {
        return $this->service->import($chunkId, $file);
    }

    /**
     * @inheritdoc
     */
    public function fetch(string $id): SinkFile|null
    {
        return $this->service->download($id);
    }

    /**
     * @inheritdoc
     */
    public function deleteImport(string $id, SinkFile $file): bool
    {
        return true;
    }

    public function destinationTables(): array
    {
        return $this->service->destinationTables();
    }
}