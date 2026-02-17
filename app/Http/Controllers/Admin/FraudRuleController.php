<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FraudRule;
use Illuminate\Http\Request;

class FraudRuleController extends Controller
{
    public function index()
    {
        $rules = FraudRule::all();
        return view('admin.fraud.index', compact('rules'));
    }

    public function create()
    {
        return view('admin.fraud.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'rule_name' => 'required|string|max:255',
            'rule_type' => 'required|in:cart_value,multiple_orders,same_ip',
            'threshold_value' => 'required|numeric',
            'weight' => 'required|integer',
            'time_window_minutes' => 'nullable|integer',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        
        FraudRule::create($data);

        return redirect()->route('fraud-rules.index')->with('success', 'Fraud rule created successfully.');
    }

    public function edit(FraudRule $fraudRule)
    {
        return view('admin.fraud.edit', compact('fraudRule'));
    }

    public function update(Request $request, FraudRule $fraudRule)
    {
        $request->validate([
            'rule_name' => 'required|string|max:255',
            'rule_type' => 'required|in:cart_value,multiple_orders,same_ip',
            'threshold_value' => 'required|numeric',
            'weight' => 'required|integer',
            'time_window_minutes' => 'nullable|integer',
        ]);

        $fraudRule->update([
            'rule_name' => $request->rule_name,
            'rule_type' => $request->rule_type,
            'threshold_value' => $request->threshold_value,
            'weight' => $request->weight,
            'time_window_minutes' => $request->time_window_minutes,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('fraud-rules.index')->with('success', 'Fraud rule updated successfully.');
    }

    public function destroy(FraudRule $fraudRule)
    {
        $fraudRule->delete();
        return redirect()->route('fraud-rules.index')->with('success', 'Fraud rule deleted successfully.');
    }
}
