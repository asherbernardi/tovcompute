<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Company;
use App\Models\QualitativeFrameworkScore;
use Carbon\Carbon;

class ImportResearchScores extends Command
{
    protected $signature = 'scores:import
                            {--csv=        : Path to the research CSV file}
                            {--notes-dir=  : Directory containing the MD evidence files}
                            {--dry-run     : Preview what would be imported without writing to DB}';

    protected $description = 'Import qualitative framework scores from CSV and MD evidence files';

    private array $categories = [
        'leadership_character'           => 1,
        'culture_strength'               => 2,
        'employee_outcomes'              => 3,
        'customer_trust'                 => 4,
        'incentive_pay_equity_alignment' => 5,
        'operational_excellence'         => 6,
        'governance_reputation'          => 7,
    ];

    public function handle(): int
    {
        $csvPath  = $this->option('csv')       ?? base_path('data_sources/research_scores.csv');
        $notesDir = $this->option('notes-dir') ?? base_path('data_sources/research_notes');
        $dryRun   = $this->option('dry-run');

        if (!file_exists($csvPath)) {
            $this->error("CSV not found: {$csvPath}");
            return 1;
        }

        if ($dryRun) {
            $this->warn('DRY RUN — nothing will be written to the database.');
        }

        $handle  = fopen($csvPath, 'r');
        $headers = array_map('trim', fgetcsv($handle));

        $imported = 0;
        $skipped  = 0;
        $row      = 0;

        while (($data = fgetcsv($handle)) !== false) {
            $row++;
            if (count($data) !== count($headers)) {
                $this->warn("Row {$row}: column count mismatch — skipping");
                $skipped++;
                continue;
            }

            $record = array_combine($headers, $data);
            $ticker = strtoupper(trim($record['ticker']));

            $company = Company::where('ticker', $ticker)->first();
            if (!$company) {
                $altTicker = str_contains($ticker, '.') ? str_replace('.', '-', $ticker) : str_replace('-', '.', $ticker);
                $company   = Company::where('ticker', $altTicker)->first();
            }
            if (!$company) {
                $this->warn("Row {$row}: no company found for ticker '{$ticker}' — skipping");
                $skipped++;
                continue;
            }

            $createdAt = Carbon::parse($record['research_date'])->timestamp;

            // Skip if already imported (same company + timestamp)
            if (QualitativeFrameworkScore::where('company_id', $company->id)
                    ->where('created_at', $createdAt)->exists()) {
                $this->line("Row {$row}: {$ticker} already imported — skipping");
                $skipped++;
                continue;
            }

            $mdContent = $this->loadMdFile($notesDir, $ticker, $record['research_date']);
            $summary   = $mdContent ? $this->parseSummary($mdContent)              : 'Imported from legacy CSV.';
            $redFlags  = $this->parseRedFlags($record['red_flags'], $mdContent);

            $this->line("Row {$row}: {$ticker} — {$company->name}" . ($mdContent ? '' : ' (no MD file)'));

            if (!$dryRun) {
                $score = QualitativeFrameworkScore::create([
                    'company_id'        => $company->id,
                    'created_at'        => $createdAt,
                    'source'            => trim($record['model']),
                    'summary'           => $summary,
                    'red_flags'         => $redFlags,
                    'research_duration' => (int) $record['research_duration_seconds'],
                    'input_tokens'      => (int) $record['total_input_tokens'],
                    'output_tokens'     => (int) $record['total_output_tokens'],
                    'notes'             => $mdContent,
                ]);

                foreach ($this->categories as $column => $categoryNumber) {
                    $score->featureScores()->create([
                        'category'   => $column,
                        'score'      => (float) $record[$column],
                        'confidence' => strtoupper(trim($record["{$column}_confidence"])),
                        'evidence'   => $mdContent ? $this->parseEvidence($mdContent, $categoryNumber) : [],
                    ]);
                }
            }

            $imported++;
        }

        fclose($handle);

        $this->info("Done. Imported: {$imported}, Skipped: {$skipped}");
        return 0;
    }

    private function loadMdFile(string $dir, string $ticker, string $researchDate): ?string
    {
        $dt       = Carbon::parse($researchDate);
        $filename = "{$ticker}_{$dt->format('Ymd')}_{$dt->format('His')}.md";
        $path     = rtrim($dir, '/') . '/' . $filename;

        if (file_exists($path)) {
            return file_get_contents($path);
        }

        // Fallback: match any file for this ticker on the same date
        $pattern = rtrim($dir, '/') . "/{$ticker}_{$dt->format('Ymd')}*.md";
        $matches = glob($pattern);
        if (!empty($matches)) {
            $this->warn("  Exact MD file not found; using {$matches[0]}");
            return file_get_contents($matches[0]);
        }

        $this->warn("  No MD file found for {$filename}");
        return null;
    }

    private function parseSummary(string $md): string
    {
        // Extract the paragraph following "**Key Mosaic Insight:**"
        if (preg_match('/\*\*Key Mosaic Insight:\*\*\s*(.+?)(?=\n\n---|\n\n##|\z)/su', $md, $m)) {
            return trim($m[1]);
        }
        return 'Imported from legacy CSV.';
    }

    private function parseRedFlags(string $csvValue, ?string $md): ?array
    {
        if (strtoupper(trim($csvValue)) !== 'TRUE') {
            return null;
        }

        if ($md) {
            // Extract titles from "### 🚩 Red Flag N: Title" lines
            preg_match_all('/###\s*🚩\s*Red Flag \d+:\s*(.+)/u', $md, $m);
            if (!empty($m[1])) {
                return array_map('trim', $m[1]);
            }
        }

        return ['Red flags present — see notes'];
    }

    private function parseEvidence(string $md, int $categoryNumber): array
    {
        // Isolate the CATEGORY N section
        $pattern = '/### CATEGORY ' . $categoryNumber . ':.*?\n(.*?)(?=\n---|\n### CATEGORY|\n## \d|\z)/su';
        if (!preg_match($pattern, $md, $sectionMatch)) {
            return [];
        }

        // Isolate the **Evidence:** block within that section
        if (!preg_match('/\*\*Evidence:\*\*\s*\n(.*?)(?=\n\*\*[A-Z]|\z)/su', $sectionMatch[1], $evidenceMatch)) {
            return [];
        }

        // Extract bullet lines, stripping markdown bold markers
        $evidence = [];
        foreach (explode("\n", $evidenceMatch[1]) as $line) {
            $line = trim($line);
            if (str_starts_with($line, '- ')) {
                $evidence[] = trim(preg_replace('/\*\*(.+?)\*\*/u', '$1', substr($line, 2)));
            }
        }

        return $evidence;
    }
}
