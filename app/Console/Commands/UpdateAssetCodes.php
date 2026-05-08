<?php

namespace App\Console\Commands;

use App\Models\Asset;
use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateAssetCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assets:update-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all existing asset codes to BRIN-XX-YYYYYY format';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting asset code update to BRIN-XX-YYYYYY format...');
        
        $categories = Category::all();
        $totalUpdated = 0;
        
        $this->info('Updating all assets with Global Sequence system...');
        
        // Get all assets ordered by creation date for proper global sequence
        $allAssets = Asset::orderBy('created_at', 'asc')->get();
        
        $globalSequence = 1;
        foreach ($allAssets as $asset) {
            // Get category code for this asset
            $category = Category::find($asset->category_id);
            if (!$category || !$category->category_code) {
                $this->warn("Skipping asset '{$asset->name}' - no valid category with category_code");
                continue;
            }
            
            $newCode = 'BRIN-' . $category->category_code . '-' . str_pad($globalSequence, 6, '0', STR_PAD_LEFT);
            
            $asset->code = $newCode;
            $asset->save();
            
            $this->line("  Updated: {$asset->name} -> {$newCode}");
            
            $globalSequence++;
            $totalUpdated++;
        }
        
        $this->info('----------------------------------------');
        $this->info("Total assets updated: {$totalUpdated}");
        $this->info('Asset code update completed successfully!');
        
        return Command::SUCCESS;
    }
}
