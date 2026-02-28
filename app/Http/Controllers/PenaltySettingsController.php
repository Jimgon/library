<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PenaltySetting;

class PenaltySettingsController extends Controller
{
    // Show current penalty settings
    public function index()
    {
        // Get settings (create defaults if none exist)
        $settings = PenaltySetting::first();

        if (!$settings) {
            $settings = PenaltySetting::create([
                'penalty_per_day' => 5,
                'borrow_days_allowed' => 7,
            ]);
        }

        return view('admin.penalties.settings', compact('settings'));
    }

    // Update penalty settings
    public function update(Request $request)
    {
        $request->validate([
            'penalty_per_day' => 'required|numeric|min:1',
            'borrow_days_allowed' => 'required|numeric|min:1',
        ]);

        $settings = PenaltySetting::first();

        if ($settings) {
            // Update existing settings
            $settings->update([
                'penalty_per_day' => $request->penalty_per_day,
                'borrow_days_allowed' => $request->borrow_days_allowed,
            ]);
        } else {
            // Create new settings
            $settings = PenaltySetting::create([
                'penalty_per_day' => $request->penalty_per_day,
                'borrow_days_allowed' => $request->borrow_days_allowed,
            ]);
        }

        return back()->with('success', 'Penalty settings updated successfully!');
    }
}
