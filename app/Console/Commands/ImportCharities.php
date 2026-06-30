<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportCharities extends Command
{
    protected $signature = 'charities:import
                            {--csv= : Path to the 990 CSV file (defaults to data_sources/ directory)}
                            {--background : Run in background and return immediately}';

    protected $description = 'Import charities from a 990 Combined DataMart CSV file';

    public function handle(): int
    {
        $csvPath = $this->option('csv')
            ?? base_path('data_sources/2025_07_10_All_Years_990_990ez_990pf_990n_Combined_DataMart.csv');

        if (!file_exists($csvPath)) {
            $this->error("CSV file not found: {$csvPath}");
            return 1;
        }

        $db = config('database.connections.' . config('database.default'));

        $pythonBin = base_path('venv/bin/python3');

        $cmd = implode(' ', [
            escapeshellarg($pythonBin),
            escapeshellarg(base_path('scripts/import_charity.py')),
            '--csv',      escapeshellarg($csvPath),
            '--host',     escapeshellarg($db['host']),
            '--port',     escapeshellarg($db['port'] ?? 3306),
            '--user',     escapeshellarg($db['username']),
            '--password', escapeshellarg($db['password']),
            '--database', escapeshellarg($db['database']),
        ]);

        if ($this->option('background')) {
            $logFile = storage_path('logs/charity_import.log');
            exec("nohup {$cmd} > " . escapeshellarg($logFile) . " 2>&1 &");
            $this->info("Charity import started in the background.");
            $this->info("Progress log: {$logFile}");
            return 0;
        }

        // Foreground: stream Python output line by line
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
