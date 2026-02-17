<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BundleRule;
use Illuminate\Http\Request;

class BundleRuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rules = BundleRule::orderBy('type')->orderBy('min_items')->get();
        return view('admin.bundle-rules.index', compact('rules'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.bundle-rules.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:item_count,category_variety',
            'min_items' => 'required|integer|min:1',
            'max_items' => 'nullable|integer|gte:min_items',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        // Default max_items for category rules if not provided (open ended)
        if ($validated['type'] === 'category_variety' && empty($validated['max_items'])) {
            $validated['max_items'] = 100; // Arbitrary high number or leave null if column allows
        }

        // Checkbox handling
        $validated['is_active'] = $request->has('is_active');
        $validated['discount_percentage'] = $validated['discount_type'] === 'percentage' ? $validated['discount_value'] : 0; // Legacy support

        BundleRule::create($validated);

        return redirect()->route('bundle-rules.index')->with('success', 'Bundle Rule created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $rule = BundleRule::findOrFail($id);
        return view('admin.bundle-rules.edit', compact('rule'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rule = BundleRule::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:item_count,category_variety',
            'min_items' => 'required|integer|min:1',
            'max_items' => 'nullable|integer|gte:min_items',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        if ($validated['type'] === 'category_variety' && empty($validated['max_items'])) {
            $validated['max_items'] = 100;
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['discount_percentage'] = $validated['discount_type'] === 'percentage' ? $validated['discount_value'] : 0;

        $rule->update($validated);

        return redirect()->route('bundle-rules.index')->with('success', 'Bundle Rule updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $rule = BundleRule::findOrFail($id);
        $rule->delete();

        return redirect()->route('bundle-rules.index')->with('success', 'Bundle Rule deleted successfully.');
    }
}
