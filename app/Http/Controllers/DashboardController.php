<?php

namespace App\Http\Controllers;
use App\Models\Poll;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Dashboard stats (only for current user's polls)
        $polls = Poll::with(['categories', 'nominees'])
            ->where('created_by', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        $totalPolls = $polls->count();
        $totalVotes = 0;
        $totalCategories = 0;
        $totalNominees = 0;
        foreach ($polls as $poll) {
            $totalVotes += $poll->nominees->sum(function($nominee) { return $nominee->votes->count() ?? 0; });
            $totalCategories += $poll->categories->count();
            $totalNominees += $poll->nominees->count();
        }
        // Recent polls (latest 5 for current user)
        $recentPolls = $polls->take(5);
        // Recent activity (latest 7 for current user)
        $recentActivity = $polls->take(7)->map(function($poll) {
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
