<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ProductRankingService;
use App\Models\Product;

class RecalculateProductRanking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:recalculate-ranking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate ranking scores for all products based on sales, ratings, and returns';

    /**
     * Execute the console command.
     *
     * @param  \App\Services\ProductRankingService  $service
     * @return int
     */
    public function handle(ProductRankingService $service)
    {
        $this->info('Starting product ranking recalculation...');
        $this->info('');

        $totalProducts = Product::count();
        $this->info("Total products to process: {$totalProducts}");

        try {
            $updated = $service->recalculateAll();

            $this->info('');
            $this->info("✔ Successfully recalculated ranking for {$updated} products.");
            $this->info('');

            // Show summary table
            $products = Product::orderByDesc('ranking_score')->get();
            $rows = [];
            foreach ($products as $i => $p) {
                $rows[] = [
                    '#' . ($i + 1),
                    $p->name,
                    number_format((float) $p->total_sales),
                    number_format((float) ($p->average_rating ?? $p->avg_rating ?? 0), 1),
                    number_format((float) $p->return_rate, 1) . '%',
                    number_format((float) $p->ranking_score, 1),
                    $p->is_best_seller ? '⭐ Yes' : '—',
                ];
            }

            $this->table(
                ['Rank', 'Product', 'Sales', 'Rating', 'Return Rate', 'Score', 'Best Seller'],
                $rows
            );

            $this->info('');
            $this->info('✔ No data deleted. No schema changes. No formula changes.');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error occurred: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
