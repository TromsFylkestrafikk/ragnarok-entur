<?php

namespace Ragnarok\Entur\Sinks;

use Illuminate\Support\Facades\DB;

use Ragnarok\Entur\Services\EnturSales;
use Ragnarok\Sink\Models\SinkFile;

class SinkEnturSales extends SinkEnturBase
{
    public static $id = "entur-sales";
    public static $title = "Entur Salesdata";
    public $cron = '30 04 * * *';
    //public $cron = '0 21 12 * ?';
    

    protected $service;

    public function __construct()
    {
        //if($this->service == null) {
            $this->service = new EnturSales();
        //}
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
        return $this->service->download($id); //->getFile();
    }

    /**
     * @inheritdoc
     */
    public function deleteImport(string $id, SinkFile $file): bool
    {
        $tables = $this->destinationTables();
        
        foreach($tables as $table) {
            DB::table($table)->where('chunk_id', $id)->delete();
        }
        
        return true;
    }

    public function destinationTables(): array
    {
        return $this->service->destinationTables();
    }
}