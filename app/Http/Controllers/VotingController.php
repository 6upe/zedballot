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

        $poll->syncStatus();
        $poll->refresh(); // make sure we have updated value

        if ($poll->status !== 'active') {
            return view('polls.vote_closed', compact('poll'));
        }

        // Determine required voter info fields based on poll's voting methods
        $methods = array_map('trim', explode(',', $poll->voting_methods));
        // dd($methods);
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


        // Determine identifier type and value
        if (count($methods) === 1) {
            $identifierType = $methods[0];
            $identifierValue = $request->input($identifierType);
        } else {
            $rules['identifier_type'] = 'required|in:' . implode(',', $methods);
            $rules['identifier_value'] = 'required|string|max:255';
            $identifierType = $request->input('identifier_type');
            $identifierValue = $request->input('identifier_value');
        }

        // PUBLIC POLL: create eligible voter and voter immediately
        if ($poll->is_public) {
            // Create eligible voter if not exists, and ensure email matches submitted email
            $eligibleVoter = EligibleVoter::where('poll_id', $poll->id)
                ->where('identifier_type', $identifierType)
                ->where('identifier_value', $identifierValue)
                ->first();

            dd($eligibleVoter->email);
            
            if ($eligibleVoter) {
                // Update email if different
                if ($eligibleVoter->email !== $request->input('email')) {
                    $eligibleVoter->email = $request->input('email');
                    $eligibleVoter->save();
                }
            } else {
                $eligibleVoter = EligibleVoter::create([
                    'poll_id' => $poll->id,
                    'identifier_type' => $identifierType,
                    'identifier_value' => $identifierValue,
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'phone' => $request->input('phone'),
                    'registered_at' => now(),
                ]);
            }
        } else {
            // PRIVATE POLL: check restrictions
            if (in_array('email', $methods) && $poll->email_domain) {
                $email = $request->input('email');
                $domain = substr(strrchr($email, '@'), 1);
                if (strtolower($domain) !== strtolower($poll->email_domain)) {
                    return back()->withErrors(['Your email domain is not allowed for this poll.']);
                }
            }
            // Check if eligible voter exists for this poll and identifier
            $eligibleVoter = EligibleVoter::where('poll_id', $poll->id)
                ->where('identifier_type', $identifierType)
                ->where('identifier_value', $identifierValue)
                ->first();

                // dd($eligibleVoter->email);
            
            if (!$eligibleVoter) {
                return back()->withErrors(['You are not an eligible voter for this poll.']);
            }
        }

        // Use eligible voter info for voter creation

        $rules['votes'] = 'required|array';
        $rules['votes.*'] = 'required|integer|exists:nominees,id';

        $validated = $request->validate($rules);

        // Prevent double voting only if already verified
        $existingVoter = Voter::where('poll_id', $poll->id)
            ->where('identifier_type', $identifierType)
            ->where('identifier_value', $identifierValue)
            ->whereNotNull('verified_at')
            ->first();
        if ($existingVoter) {
            return view('polls.vote_already_submitted', compact('poll'));
        }


        // Find or create voter (not verified yet), using eligible voter info
        $voter = Voter::where('poll_id', $poll->id)
            ->where('identifier_type', $eligibleVoter->identifier_type)
            ->where('identifier_value', $eligibleVoter->identifier_value)
            ->whereNull('verified_at')
            ->first();
        if (!$voter) {
            $voter = new Voter();
            $voter->poll_id = $poll->id;
            $voter->identifier_type = $eligibleVoter->identifier_type;
            $voter->identifier_value = $eligibleVoter->identifier_value;
            $voter->verified_at = null;
            $voter->save();
        }

        // Remove previous votes for this poll and voter (to avoid unique constraint violation)
        Vote::where('poll_id', $poll->id)
            ->where('voter_id', $voter->id)
            ->delete();

        // Save votes as pending (not counted until verified)
        foreach ($validated['votes'] as $categoryId => $nomineeId) {
            $vote = new Vote();
            $vote->poll_id = $poll->id;
            $vote->category_id = $categoryId;
            $vote->nominee_id = $nomineeId;
            $vote->voter_id = $voter->id;
            $vote->save();
        }

        // If poll uses email voting, send confirmation email
        if (in_array('email', $methods)) {
            // Generate a signed URL for confirmation
            $confirmationUrl = url()->temporarySignedRoute(
                'polls.vote.confirm',
                now()->addMinutes(60),
                [
                    'poll' => $poll->uuid,
                    'voter' => $voter->id,
                ]
            );

            // dd('Reached email section');
            // Send notification to eligible voter's registered email
            \Notification::route('mail', $eligibleVoter->email)
                ->notify(new \App\Notifications\VoteConfirmationNotification(
                    $poll,
                    $voter,
                    $eligibleVoter,
                    $confirmationUrl
                ));

            session()->flash('email_confirmation_sent', true);
            return view('polls.vote_success', [
                'poll' => $poll,
                'email_confirmation' => true,
                'confirmation_email' => $eligibleVoter->email,
            ]);
        }

        // Otherwise, mark voter as verified immediately
        $voter->verified_at = now();
        $voter->save();

        return view('polls.vote_success', compact('poll'));
    }

    public function confirmVote(Request $request, Poll $poll, $voterId)
    {
        $voter = Voter::where('id', $voterId)
            ->where('poll_id', $poll->id)
            ->firstOrFail();

        if ($voter->isVerified()) {
            return view('polls.vote_already_submitted', compact('poll'));
        }

        $voter->verified_at = now();
        $voter->save();

        // Optionally, you can show a special confirmation view
        return view('polls.vote_confirmed', compact('poll'));
    }
}