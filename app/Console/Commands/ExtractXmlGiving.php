<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ExtractXmlGiving extends Command
{
    protected $signature = 'giving:extract-xml
                            {--search-type=company : Scope — all, charity, company, list, all_lists}
                            {--criteria= : ID for charity/company/list search types}
                            {--fields=all : Fields to extract — all or basic}
                            {--background : Run in background and return immediately}';

    protected $description = 'Fetch IRS XML filings and update charity_giving with detailed financial fields';

    public function handle(): int
    {
        $searchType = $this->option('search-type');
        $criteria   = $this->option('criteria');
        $fields     = $this->option('fields');

        $requiresCriteria = in_array($searchType, ['charity', 'company', 'list']);
        if ($requiresCriteria && empty($criteria)) {
            $this->error("--search-type '{$searchType}' requires --criteria <id>");
            return 1;
        }

        $db = config('database.connections.' . config('database.default'));

        $pythonBin = base_path('venv/bin/python3');

        $cmd = implode(' ', array_filter([
            escapeshellarg($pythonBin),
            escapeshellarg(base_path('scripts/extract_xml_giving.py')),
            '--search-type', escapeshellarg($searchType),
            $criteria ? '--criteria ' . escapeshellarg($criteria) : '',
            '--fields',       escapeshellarg($fields),
            '--host',         escapeshellarg($db['host']),
            '--port',         escapeshellarg($db['port'] ?? 3306),
            '--user',         escapeshellarg($db['username']),
            '--password',     escapeshellarg($db['password']),
            '--database',     escapeshellarg($db['database']),
        ]));

        if ($this->option('background')) {
            $logFile = storage_path('logs/xml_giving_extract.log');
            exec("nohup {$cmd} > " . escapeshellarg($logFile) . " 2>&1 &");
            $this->info("XML giving extraction started in the background.");
            $this->info("Progress log: {$logFile}");
            return 0;
        }

        $process = popen("{$cmd} 2>&1", 'r');
        while (!feof($process)) {
            $line = fgets($process);
            if ($line !== false) {
                $this->line(rtrim($line));
            }
        }
        pclose($process);

        return 0;
    }
}
