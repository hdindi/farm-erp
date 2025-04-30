<?php

namespace App\Http\Controllers;

use App\Models\FeedType;
use Illuminate\Http\Request;

class FeedTypeController extends Controller
{
    public function index()
    {
        $feedTypes = FeedType::latest()->paginate(10);
        return view('feed-types.index', compact('feedTypes'));
    }

    public function create()
    {
        return view('feed-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:feed_types',
            'description' => 'nullable|string|max:1000',
        ]);

        FeedType::create($validated);

        return redirect()->route('feed-types.index')
            ->with('success', 'Feed type created successfully.');
    }

    public function show(FeedType $feedType)
    {
        return view('feed-types.show', compact('feedType'));
    }

    public function edit(FeedType $feedType)
    {
        return view('feed-types.edit', compact('feedType'));
    }

    public function update(Request $request, FeedType $feedType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:feed_types,name,'.$feedType->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $feedType->update($validated);

        return redirect()->route('feed-types.index')
            ->with('success', 'Feed type updated successfully.');
    }

    public function destroy(FeedType $feedType)
    {
        $feedType->delete();

        return redirect()->route('feed-types.index')
            ->with('success', 'Feed type deleted successfully.');
    }
}
