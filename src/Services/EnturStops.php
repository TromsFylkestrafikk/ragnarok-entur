<?php

namespace Ragnarok\Entur\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Http;
use Ragnarok\Entur\Sinks\SinkEnturStops;
use Ragnarok\Sink\Models\SinkFile;
use Ragnarok\Sink\Services\SinkDisk;
use Ragnarok\Sink\Services\ChunkArchive;
use Ragnarok\Sink\Services\ChunkExtractor;
use ZipArchive;

class EnturStops
{
    use \Ragnarok\Sink\Traits\LogPrintf;

    /**
     * @var Filesystem
     */
    protected $sinkDisk;

    /**
     * List of extracted directories that should be removed when destructed.
     *
     * @var string[]
     */
    protected $dirty;

    public function __construct()
    {
        $disk = new SinkDisk(SinkEnturStops::$id);
        $this->sinkDisk = $disk->getDisk();
        $this->dirty = [];
        $this->logPrintfInit('[Entur NSR]: ');
    }

    public function __destruct()
    {
        foreach ($this->dirty as $dir) {
            $this->sinkDisk->deleteDirectory($dir);
        }
    }

    /**
     * Download all stop sets from config and re-pack them to a single file
     */
    public function downloadStops($chunkId): SinkFile|null
    {
        $archive = new ChunkArchive(SinkEnturStops::$id, $chunkId);
        foreach (config('ragnarok_entur.import_stop_archives') as $number => $url) {
            $urlParts = parse_url($url);
            $filename = basename($urlParts['path']);
            $response = Http::get($url);
            $archive->addFromString($filename, $response->body());
        }
        return $archive->save()->getFile();
    }

    public function importFromSink(SinkFile $file): int
    {
        $extractor = new ChunkExtractor(SinkEnturStops::$id, $file);
        $count = 0;
        foreach ($extractor->getFiles() as $index => $path) {
            $count += $this->importFromZip($path, $index === 0);
        }
        return $count;
    }

    protected function importFromZip(string $path, $first = false): int
    {
        $extractDir = uniqid('stops-');
        $this->sinkDisk->makeDirectory($extractDir);
        $archive = new ZipArchive();
        $archive->open($path);
        $this->debug('Extracting %s to %s', $path, $this->sinkDisk->path($extractDir));
        $archive->extractTo($this->sinkDisk->path($extractDir));
        $xml = $this->sinkDisk->files($extractDir)[0];
        $this->debug('Using xml %s', $xml);
        $xmlPath = $this->sinkDisk->path($xml);
        Artisan::call('netex:importstops', ['xml' => $xmlPath, '--keep' => !$first]);
        return 0;
    }

    protected function createTargetFilename(): string
    {
        return sprintf('stops_%s.zip', today()->format('Y-m-d'));
    }
}
