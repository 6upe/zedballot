<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\Voter;
use App\Models\Vote;
use App\Models\EligibleVoter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VotingController extends Controller
{
    /**
     * Display the voting form for a poll.
     */
    public function showVote(Poll $poll)
    {
        // Optionally, you can eager load categories and nominees if needed
        $poll->load(['categories', 'nominees']);
        return view('polls.vote', compact('poll'));
    }
    public function submitVote(Request $request, Poll $poll)
    {
        // Determine required voter info fields based on poll's voting methods
        $methods = array_map('trim', explode(',', $poll->voting_methods));
        $rules = [];
        if (in_array('name', $methods) || $poll->is_public) {
            $rules['name'] = 'required|string|max:255';
        }
        if (in_array('email', $methods)) {
            $rules['email'] = 'required|email|max:255';
        }
        if (in_array('phone', $methods)) {
            $rules['phone'] = 'required|string|max:32';
        }
        if (in_array('nrc', $methods)) {
            $rules['nrc'] = 'required|string|max:64';
        }
        if (in_array('passport', $methods)) {
            $rules['passport'] = 'required|string|max:64';
        }

        // Identifier type/value for uniqueness
        if (count($methods) === 1) {
            $identifierType = $methods[0];
            $identifierValue = $request->input($identifierType);
        } else {
            $rules['identifier_type'] = 'required|in:' . implode(',', $methods);
            $rules['identifier_value'] = 'required|string|max:255';
            $identifierType = $request->input('identifier_type');
            $identifierValue = $request->input('identifier_value');
        }

        $rules['votes'] = 'required|array';
        $rules['votes.*'] = 'required|integer|exists:nominees,id';

        $validated = $request->validate($rules);

        // Prevent double voting
        $existingVoter = Voter::where('poll_id', $poll->id)
            ->where('identifier_type', $identifierType)
            ->where('identifier_value', $identifierValue)
            ->first();
        if ($existingVoter) {
            return view('polls.vote_already_submitted', compact('poll'));
        }

        // Create voter
        $voter = new Voter();
        $voter->poll_id = $poll->id;
        $voter->identifier_type = $identifierType;
        $voter->identifier_value = $identifierValue;
        $voter->name = $request->input('name');
        $voter->email = $request->input('email');
        $voter->phone = $request->input('phone');
        $voter->nrc = $request->input('nrc');
        $voter->passport = $request->input('passport');
        $voter->save();

        // Save votes
        foreach ($validated['votes'] as $categoryId => $nomineeId) {
            $vote = new Vote();
            $vote->poll_id = $poll->id;
            $vote->category_id = $categoryId;
            $vote->nominee_id = $nomineeId;
            $vote->voter_id = $voter->id;
            $vote->save();
        }

        return view('polls.vote_success', compact('poll'));
    }
}
