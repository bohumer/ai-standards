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
    protected $description = 'Install AI standards, localization rules, and example project into .agents directory';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Installing AI Standards...');

        $stubsRoot = __DIR__ . '/../../../stubs';
        $stubsAgentsPath = $stubsRoot . '/.agents';
        $destinationPath = base_path('.agents');

        if (! File::exists($stubsAgentsPath)) {
            $this->error('Stubs directory not found.');

            return self::FAILURE;
        }

        if (File::exists($destinationPath)) {
            $this->warn('The .agents directory already exists. Syncing global standards and example project...');
            File::deleteDirectory($destinationPath . '/standards');
            File::deleteDirectory($destinationPath . '/example-project');
        } else {
            File::makeDirectory($destinationPath, 0755, true);
        }

        File::copyDirectory($stubsAgentsPath, $destinationPath);

        // Migrate legacy .agents/project_rules.md to root AGENTS.md or initialize root AGENTS.md if missing
        $legacyRulesFile = $destinationPath . '/project_rules.md';
        $rootAgentsFile = base_path('AGENTS.md');
        $rootAgentsStub = $stubsRoot . '/AGENTS.md';

        if (File::exists($legacyRulesFile)) {
            $legacyContent = trim((string) File::get($legacyRulesFile));

            if (! File::exists($rootAgentsFile)) {
                $header = "# Pravidlá projektu (Project Rules)\n\n"
                    . "> **Globálne architektonické štandardy:** Pred akoukoľvek úpravou kódu povinne dodržiavaj hlavné architektonické štandardy definované v [`.agents/AGENTS.md`](./.agents/AGENTS.md), podrobné pravidlá v priečinku [`.agents/standards/`](./.agents/standards/) (`1_routing.md` až `8_localization.md`) a vzorové implementácie v [`.agents/example-project/`](./.agents/example-project/). Nižšie uvedené pravidlá platia špecificky pre tento projekt.\n\n";

                File::put($rootAgentsFile, $header . $legacyContent . "\n");
                $this->info('Migrated legacy .agents/project_rules.md to root AGENTS.md.');
            }

            File::delete($legacyRulesFile);
        } elseif (! File::exists($rootAgentsFile) && File::exists($rootAgentsStub)) {
            File::copy($rootAgentsStub, $rootAgentsFile);
            $this->info('Created starter AGENTS.md in project root.');
        }

        // Update .gitignore
        $gitignorePath = base_path('.gitignore');
        if (File::exists($gitignorePath)) {
            $gitignoreContent = (string) File::get($gitignorePath);
            $linesToAdd = [
                '/.agents/example-project',
                '/.agents/standards',
            ];

            $added = false;
            foreach ($linesToAdd as $line) {
                if (! str_contains($gitignoreContent, $line)) {
                    File::append($gitignorePath, "\n" . $line);
                    $added = true;
                }
            }
            if ($added) {
                $this->info('Added AI Standards directories to .gitignore.');
            }
        }

        $this->info('AI Standards successfully installed into .agents directory.');

        return self::SUCCESS;
    }
}
