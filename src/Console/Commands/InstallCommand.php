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
