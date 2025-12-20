<?php

namespace App\Http\Controllers;
use App\Models\Poll;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Dashboard stats
        $polls = Poll::with(['categories', 'nominees'])->orderBy('created_at', 'desc')->get();
        $totalPolls = \App\Models\Poll::count();
        $totalVotes = \App\Models\Vote::count();
        $totalCategories = \App\Models\Category::count();
        $totalNominees = \App\Models\Nominee::count();

        // Recent polls (latest 5)
        $recentPolls = \App\Models\Poll::orderBy('created_at', 'desc')->take(5)->get();

        // Recent activity (latest 7, fallback: show recent poll creations)
        $recentActivity = \App\Models\Poll::orderBy('created_at', 'desc')->take(7)->get()->map(function($poll) {
            return (object) [
                'created_at' => $poll->created_at,
                'description' => "Poll created: {$poll->name}"
            ];
        });

        return view('dashboard.index', compact(
            'totalPolls',
            'totalVotes',
            'totalCategories',
            'totalNominees',
            'recentPolls',
            'recentActivity',
            'polls'
        ));
    }

    public function results()
    {
        $polls = Poll::with(['categories', 'nominees'])->orderBy('created_at', 'desc')->get();
        return view('dashboard.sidebar_items.results.index', compact('polls'));
    }



    
}
