<?php

namespace Ragnarok\Entur\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Ragnarok\Entur\Facades\EnturApi;
use Ragnarok\Entur\Sinks\SinkEnturRoutes;
use Ragnarok\Sink\Models\SinkFile;
use Ragnarok\Sink\Services\LocalFile;
use Ragnarok\Sink\Services\ChunkExtractor;
use Ragnarok\Sink\Traits\LogPrintf;
use TromsFylkestrafikk\Netex\Console\NeTEx\NetexDatabase;
use TromsFylkestrafikk\Netex\Models\Import;

/**
 * Services surrounding EnTur API
 */
class Entur
{
    use LogPrintf;

    /**
     * @var string
     */
    protected $tmpZipPath;

    public function __construct()
    {
        $this->logPrintfInit("[EnTur]: ");
    }

    /**
     * Get full URL to NeTEx route set
     *
     * @return string
     */
    public function getNetexRoutedataUrl(): string
    {
        return sprintf("%s/%s", EnturApi::getApiUrl(), config('ragnarok_entur.api.api_path'));
    }

    /**
     * Download and extract NeTEx route set from EnTur.
     *
     * The extracted route set will be put in a subfolder on the configured
     * 'netex' disk, in the form netex_<ENV>_TIMESTAMP/
     *
     * @return LocalFile Path to extracted directory within netex disk.
     */
    public function downloadRouteset(): LocalFile
    {
        $this->debug('Downloading route set ...');
        $lFile = LocalFile::createFromFilename(SinkEnturRoutes::$id, $this->makeTargetFilename())->assertDir();
        Http::withOptions(['sink' => $lFile->getPath()])
            ->withHeaders(['authorization' => 'Bearer ' . EnturApi::getApiToken()])
            ->get($this->getNetexRoutedataUrl());
        $this->debug('dest file = %s', $lFile->getFile()->name);
        return $lFile->save();
    }

    public function importRouteset(SinkFile $file): int
    {
        // Remove any existing entries in import table.
        Import::truncate();
        $extractor = new ChunkExtractor(SinkEnturRoutes::$id, $file);
        $extractor->setDestDir(basename($file->name, '.zip'))->extract();
        $this->debug('extracting route set to %s', $extractor->getDestDir());
        Artisan::call('netex:routedata-import', [
            '--force' => true,
            'path' => $extractor->getDestDir(),
            'main' => config('ragnarok_entur.routedata.main_xml'),
        ]);
        return 0;
    }

    /**
     * Delete imported
     */
    public function delRouteImport(SinkFile $file): bool
    {
        // Rebuild extraction dir for comparison with netex import table
        $extractor = new ChunkExtractor(SinkEnturRoutes::$id, $file);
        $extractor->setDestDir(basename($file->name, '.zip'));
        $netexImport = Import::where('path', $extractor->getDestDir())->first();
        if (!$netexImport) {
            $this->debug('Netex import not found. Not touching');
            return false;
        }
        $this->debug('Netex import found. Removing …');
        new NetexDatabase();
        Artisan::call('netex:routedata-remove', ['--id' => $netexImport->id]);
        return true;
    }

    public function makeTargetFilename(): string
    {
        return sprintf('netex_%s_%s.zip', config('ragnarok_entur.api.env'), today()->format('Y-m-d'));
    }

    /**
     * Extract date portion of target filename
     */
    public function getDateFromFilename(string $filename): string|null
    {
        $matches = [];
        $hits = preg_match('|^netex_[a-z]+_(?P<date>\d{4}-\d{2}-\d{2})\.zip$|', $filename, $matches);
        return $hits ? $matches['date'] : null;
    }
}
