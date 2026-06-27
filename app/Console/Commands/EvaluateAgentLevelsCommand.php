<?php

namespace App\Console\Commands;

use App\Services\AgentLevelingService;
use Illuminate\Console\Command;

class EvaluateAgentLevelsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent:evaluate-levels';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Evaluate and update Hajj agent levels based on verified prospects';

    protected $levelingService;

    public function __construct(AgentLevelingService $levelingService)
    {
        parent::__construct();
        $this->levelingService = $levelingService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Hajj Agent Level Evaluation...');

        $results = $this->levelingService->evaluateAllAgents();

        if (empty($results)) {
            $this->warn('No active agents found for evaluation.');
            return self::SUCCESS;
        }

        $headers = ['Agent ID', 'Verified Prospects', 'Old Level', 'New Level', 'Level Changed'];
        $rows = [];

        $changedCount = 0;
        foreach ($results as $res) {
            $rows[] = [
                $res['agent_id'],
                $res['verified_count'],
                $res['old_level'],
                $res['new_level'],
                $res['level_changed'] ? 'YES' : 'NO'
            ];

            if ($res['level_changed']) {
                $changedCount++;
            }
        }

        $this->table($headers, $rows);
        $this->info("Evaluation finished. Total agents evaluated: " . count($results) . ". Levels updated: {$changedCount}.");

        return self::SUCCESS;
    }
}
