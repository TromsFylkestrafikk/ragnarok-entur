<?php

namespace Ragnarok\Entur\Sinks;

use Ragnarok\Sink\Models\SinkFile;

class SinkEnturSales extends SinkEnturBase
{

    public function import(string $chunkId, SinkFile $file): int
    {
        return 0;
    }

    /**
     * @inheritdoc
     */
    public function deleteImport(string $id, SinkFile $file): bool
    {
        
        return true;
    }
}