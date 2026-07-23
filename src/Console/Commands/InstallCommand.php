<?php

namespace Bohumer\AiStandards\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai-standards:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install AI standards and example project to .agents directory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Installing AI Standards...');

        $stubsPath = __DIR__ . '/../../../stubs/.agents';
        $destinationPath = base_path('.agents');

        if (!File::exists($stubsPath)) {
            $this->error('Stubs directory not found.');
            return;
        }

        if (File::exists($destinationPath)) {
            $this->warn('The .agents directory already exists. It will be overwritten/merged.');
        } else {
            File::makeDirectory($destinationPath, 0755, true);
        }

        File::copyDirectory($stubsPath, $destinationPath);

        // Merge project_rules.md into AGENTS.md if it exists
        $agentsFile = $destinationPath . '/AGENTS.md';
        $rulesFile = $destinationPath . '/project_rules.md';
        
        if (File::exists($agentsFile) && File::exists($rulesFile)) {
            $agentsContent = File::get($agentsFile);
            $rulesContent = trim(File::get($rulesFile));

            if (!empty($rulesContent) && !str_contains($agentsContent, $rulesContent)) {
                File::append($agentsFile, "\n\n" . $rulesContent . "\n");
                $this->info('Automatically appended project_rules.md to AGENTS.md.');
            }
        }

        // Update .gitignore
        $gitignorePath = base_path('.gitignore');
        if (File::exists($gitignorePath)) {
            $gitignoreContent = File::get($gitignorePath);
            $linesToAdd = [
                '/.agents/example-project',
                '/.agents/standards'
            ];
            
            $added = false;
            foreach ($linesToAdd as $line) {
                if (!str_contains($gitignoreContent, $line)) {
                    File::append($gitignorePath, "\n" . $line);
                    $added = true;
                }
            }
            if ($added) {
                $this->info('Added AI Standards directories to .gitignore.');
            }
        }

        $this->info('AI Standards successfully installed into .agents directory.');
    }
}
