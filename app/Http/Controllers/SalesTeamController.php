<?php

namespace App\Http\Controllers;

use App\Models\SalesTeam;
use Illuminate\Http\Request;

class SalesTeamController extends Controller
{
    public function index()
    {
        $teamMembers = SalesTeam::latest()->paginate(10);
        return view('sales-teams.index', compact('teamMembers'));
    }

    public function create()
    {
        return view('sales-teams.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone_no' => 'nullable|string|max:20|unique:sales_teams',
            'email' => 'nullable|string|email|max:100|unique:sales_teams',
            'is_active' => 'required|boolean',
        ]);

        SalesTeam::create($validated);

        return redirect()->route('sales-teams.index')
            ->with('success', 'Sales team member created successfully.');
    }

    public function show(SalesTeam $salesTeam)
    {
        return view('sales-teams.show', compact('salesTeam'));
    }

    public function edit(SalesTeam $salesTeam)
    {
        return view('sales-teams.edit', compact('salesTeam'));
    }

    public function update(Request $request, SalesTeam $salesTeam)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone_no' => 'nullable|string|max:20|unique:sales_teams,phone_no,'.$salesTeam->id,
            'email' => 'nullable|string|email|max:100|unique:sales_teams,email,'.$salesTeam->id,
            'is_active' => 'required|boolean',
        ]);

        $salesTeam->update($validated);

        return redirect()->route('sales-teams.index')
            ->with('success', 'Sales team member updated successfully.');
    }

    public function destroy(SalesTeam $salesTeam)
    {
        $salesTeam->delete();

        return redirect()->route('sales-teams.index')
            ->with('success', 'Sales team member deleted successfully.');
    }
}
