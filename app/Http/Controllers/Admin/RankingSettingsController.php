<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ProductRankingService;

class RankingSettingsController extends Controller
{
    public function index()
    {
        $settings = DB::table('ranking_settings')->first();
        return view('admin.ranking.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'sales_weight' => 'required|numeric|min:0',
            'rating_weight' => 'required|numeric|min:0',
            'return_weight' => 'required|numeric|min:0',
            'min_sales_for_best_seller' => 'required|integer|min:0',
            'min_rating_for_best_seller' => 'required|numeric|min:0|max:5',
            'allow_early_reviews' => 'nullable|boolean',
        ]);

        DB::table('ranking_settings')->updateOrInsert(
            ['id' => 1], 
            [
                'sales_weight' => $request->sales_weight,
                'rating_weight' => $request->rating_weight,
                'return_weight' => $request->return_weight,
                'min_sales_for_best_seller' => $request->min_sales_for_best_seller,
                'min_rating_for_best_seller' => $request->min_rating_for_best_seller,
                'allow_early_reviews' => $request->has('allow_early_reviews'),
                'updated_at' => now()
            ]
        );

        // Optionally trigger recalculation immediately
        // Artisan::call('products:recalculate-ranking'); 
        // Or let the user do it manually/scheduled. 
        // For better UX, let's trigger it but maybe in background if queue is set up.
        // For now, synchronous is safer for immediate feedback in this demo.
        
        try {
            $service = new ProductRankingService();
            $service->recalculateAll();
            $message = 'Settings updated and rankings recalculated successfully.';
        } catch (\Exception $e) {
            $message = 'Settings updated but recalculation failed: ' . $e->getMessage();
        }

        return redirect()->back()->with('success', $message);
    }
}
