<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\EligibleVoter;
use App\Models\Nominee;
use App\Models\Poll;
use App\Notifications\NomineeNominatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PollController extends Controller
{
    /* =======================
     | Poll lifecycle
     ======================= */

        public function index()
        {
        $polls = Poll::with(['categories', 'nominees'])->orderBy('created_at', 'desc')->get();

        return view('dashboard.sidebar_items.polls.index', compact('polls'));
        }

        public function create()
        {
        return view('dashboard.sidebar_items.polls.create');
        }

    /**
     * Step 1: Create initial poll draft with basic details
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'start_at' => ['nullable', 'date'],
                'end_at' => ['nullable', 'date', 'after:start_at'],
                'cover_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'], // 5MB
                'banner_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:10240'], // 10MB
                'video' => ['nullable', 'mimetypes:video/mp4,video/avi,video/quicktime,video/x-msvideo', 'max:51200'], // 50MB
            ]);

            // Handle file uploads
            $coverPath = null;
            if ($request->hasFile('cover_image')) {
                try {
                    $coverPath = $request->file('cover_image')->store('polls/covers', 'public');
                } catch (\Exception $e) {
                    \Log::error('Error uploading cover image: ' . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'Error uploading cover image: ' . $e->getMessage(),
                    ], 422);
                }
            }

            $bannerPath = null;
            if ($request->hasFile('banner_image')) {
                try {
                    $bannerPath = $request->file('banner_image')->store('polls/banners', 'public');
                } catch (\Exception $e) {
                    \Log::error('Error uploading banner image: ' . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'Error uploading banner image: ' . $e->getMessage(),
                    ], 422);
                }
            }

            $videoPath = null;
            if ($request->hasFile('video')) {
                try {
                    $videoPath = $request->file('video')->store('polls/videos', 'public');
                } catch (\Exception $e) {
                    \Log::error('Error uploading video: ' . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'Error uploading video: ' . $e->getMessage(),
                    ], 422);
                }
            }

            // Parse datetimes: if ISO-8601 format (from client conversion), parse as UTC.
            // Otherwise parse in server timezone (for manual API calls).
            $startAt = null;
            if (isset($data['start_at']) && $data['start_at'] !== null) {
                // Parse as local (Africa/Lusaka), then convert to UTC for storage
                $startAt = $data['start_at'];
            }

            $endAt = null;
            if (isset($data['end_at']) && $data['end_at'] !== null) {
                $endAt = $data['end_at'];
            }

            $poll = Poll::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'start_at' => $startAt,
                'end_at' => $endAt,
                'status' => 'draft',
                'cover_image' => $coverPath,
                'banner_image' => $bannerPath,
                'video' => $videoPath,
                'created_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'poll' => $poll->load(['categories', 'nominees']),
                'message' => 'Poll details saved successfully.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error in store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error saving poll details: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Progressive update for Step 1: Poll Details
     */
    public function updateStep1(Request $request, Poll $poll)
    {
        try {
            // Build validation rules - files only validated if actually uploaded
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'start_at' => ['nullable', 'date'],
                'end_at' => ['nullable', 'date'],
            ];
            
            // Only validate files if they are actually being uploaded
            if ($request->hasFile('cover_image')) {
                $rules['cover_image'] = ['image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'];
            }
            
            if ($request->hasFile('banner_image')) {
                $rules['banner_image'] = ['image', 'mimes:jpeg,jpg,png,gif,webp', 'max:10240'];
            }
            
            if ($request->hasFile('video')) {
                $rules['video'] = ['mimetypes:video/mp4,video/avi,video/quicktime,video/x-msvideo', 'max:51200'];
            }
            
            $data = $request->validate($rules);
            
            // Validate end_at is after start_at if both are provided
            if (!empty($data['start_at']) && !empty($data['end_at'])) {
                if (strtotime($data['end_at']) <= strtotime($data['start_at'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'End date must be after start date.',
                        'errors' => ['end_at' => ['End date must be after start date.']],
                    ], 422);
                }
            }

            // Handle file uploads
            if ($request->hasFile('cover_image')) {
                try {
                    if ($poll->cover_image) {
                        Storage::disk('public')->delete($poll->cover_image);
                    }
                    $poll->cover_image = $request->file('cover_image')->store('polls/covers', 'public');
                } catch (\Exception $e) {
                    \Log::error('Error uploading cover image: ' . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'Error uploading cover image: ' . $e->getMessage(),
                    ], 422);
                }
            }

            if ($request->hasFile('banner_image')) {
                try {
                    if ($poll->banner_image) {
                        Storage::disk('public')->delete($poll->banner_image);
                    }
                    $poll->banner_image = $request->file('banner_image')->store('polls/banners', 'public');
                } catch (\Exception $e) {
                    \Log::error('Error uploading banner image: ' . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'Error uploading banner image: ' . $e->getMessage(),
                    ], 422);
                }
            }

            if ($request->hasFile('video')) {
                try {
                    if ($poll->video) {
                        Storage::disk('public')->delete($poll->video);
                    }
                    $poll->video = $request->file('video')->store('polls/videos', 'public');
                } catch (\Exception $e) {
                    \Log::error('Error uploading video: ' . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'Error uploading video: ' . $e->getMessage(),
                    ], 422);
                }
            }

            $poll->name = $data['name'];
            $poll->description = $data['description'] ?? null;
            
            // Parse as local (Africa/Lusaka), then convert to UTC for storage
            if (!empty($data['start_at'])) {
                $poll->start_at = $data['start_at'];
            } else {
                $poll->start_at = null;
            }

            if (!empty($data['end_at'])) {
                $poll->end_at = $data['end_at'];
            } else {
                $poll->end_at = null;
            }
            $poll->save();

            return response()->json([
                'success' => true,
                'poll' => $poll->fresh(['categories', 'nominees']),
                'message' => 'Poll details updated successfully.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error in updateStep1: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error saving poll details: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Progressive update for Step 2: Categories & Nominees
     */
    public function updateStep2(Request $request, Poll $poll)
    {
        // This endpoint handles category/nominee updates via separate endpoints
        // Just return success for navigation
        return response()->json([
            'success' => true,
            'poll' => $poll->load(['categories.nominees']),
            'message' => 'Step 2 data loaded.',
        ]);
    }

    /**
     * Get Step 2 data (categories and nominees)
     */
    public function getStep2Data(Poll $poll)
    {
        // Load all relationships
        $poll->load([
            'categories' => function ($query) {
                $query->with('nominees');
            },
            'nominees' => function ($query) {
                $query->with('category');
            }
        ]);
        
        return response()->json([
            'success' => true,
            'poll' => $poll,
        ]);
    }

    /**
     * Get Step 3 data (eligible voters)
     */
    public function getStep3Data(Poll $poll)
    {
        return response()->json([
            'success' => true,
            'poll' => $poll->load(['eligibleVoters']),
        ]);
    }

    /**
     * Progressive update for Step 3: Voting Methods & Eligibility
     */
    public function updateStep3(Request $request, Poll $poll)
    {
        $allowedMethods = ['email', 'phone', 'nrc', 'passport', 'biometric_facial', 'biometric_finger'];

        // Handle JSON string from FormData
        $votingMethodsJson = $request->input('voting_methods');
        $votingMethods = [];
        
        // Log what we received for debugging
        \Log::info('updateStep3 - Received voting_methods:', [
            'raw' => $votingMethodsJson,
            'type' => gettype($votingMethodsJson),
            'all_input' => $request->all()
        ]);
        
        if ($votingMethodsJson) {
            if (is_string($votingMethodsJson)) {
                $decoded = json_decode($votingMethodsJson, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $votingMethods = $decoded;
                } else {
                    \Log::warning('Failed to decode voting_methods JSON:', [
                        'json_error' => json_last_error_msg(),
                        'raw' => $votingMethodsJson
                    ]);
                }
            } elseif (is_array($votingMethodsJson)) {
                $votingMethods = $votingMethodsJson;
            }
        }
        
        \Log::info('updateStep3 - Parsed voting_methods:', $votingMethods);

        $data = $request->validate([
            'email_domain' => ['nullable', 'string'],
            'country' => ['nullable', 'string'],
        ]);

        $isPublic = $request->has('is_public') ? (bool) $request->boolean('is_public') : true;
        $allowVoteEdit = $request->has('allow_vote_edit') ? (bool) $request->boolean('allow_vote_edit') : false;

        // Validate voting methods
        if (empty($votingMethods) || !is_array($votingMethods) || count($votingMethods) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'At least one voting method is required.',
            ], 422);
        }

        foreach ($votingMethods as $method) {
            if (!in_array($method, $allowedMethods)) {
                return response()->json([
                    'success' => false,
                    'message' => "Invalid voting method: {$method}",
                ], 422);
            }
        }

        // If not public, ensure at least one eligibility field is present
        if (! $isPublic && empty($data['email_domain']) && empty($data['country'])) {
            return response()->json([
                'success' => false,
                'message' => 'Provide at least one eligibility field (email_domain or country) when poll is not public.',
            ], 422);
        }

        $poll->voting_methods = implode(',', $votingMethods);
        $poll->is_public = $isPublic;
        $poll->email_domain = $data['email_domain'] ?? null;
        $poll->country = $data['country'] ?? null;
        $poll->allow_vote_edit = $allowVoteEdit;
        $poll->save();

        return response()->json([
            'success' => true,
            'poll' => $poll->fresh(['eligibleVoters']),
            'message' => 'Voting methods and eligibility updated successfully.',
        ]);
    }

    /**
     * Step 4: Save as Draft or Publish
     */
    public function finalize(Request $request, Poll $poll)
    {
        $data = $request->validate([
            'action' => ['required', 'in:draft,publish'],
            'notify_nominees' => ['sometimes', 'boolean'],
        ]);

        $notifyNominees = $request->has('notify_nominees') && (bool) $data['notify_nominees'];

        if ($data['action'] === 'publish') {
            // Validate required fields before publishing
            if (! $poll->start_at || ! $poll->end_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Start date and end date are required to publish.',
                ], 422);
            }

            if (! $poll->voting_methods) {
                return response()->json([
                    'success' => false,
                    'message' => 'At least one voting method is required to publish.',
                ], 422);
            }

            $poll->status = 'active';
            $poll->save();

            // Send notifications to nominees if requested
            if ($notifyNominees) {
                $this->notifyNominees($poll);
            }

            return response()->json([
                'success' => true,
                'poll' => $poll->fresh(),
                'message' => 'Poll published successfully.',
            ]);
        }

        // Save as draft
        $poll->status = 'draft';
        $poll->save();

        return response()->json([
            'success' => true,
            'poll' => $poll->fresh(),
            'message' => 'Poll saved as draft.',
        ]);
    }

    /**
     * Notify all nominees about their nomination
     */
    private function notifyNominees(Poll $poll)
    {
        $voteUrl = route('polls.vote', $poll->uuid);
        $nominees = $poll->nominees()->whereNotNull('email')->get();

        foreach ($nominees as $nominee) {
            if ($nominee->email) {
                try {
                    // Create a notifiable object for the nominee
                    $notifiable = new class {
                        public $name;
                        public $email;

                        public function routeNotificationForMail()
                        {
                            return $this->email;
                        }
                    };
                    $notifiable->name = $nominee->name;
                    $notifiable->email = $nominee->email;

                    $notifiable->notify(new NomineeNominatedNotification($poll, $voteUrl));
                } catch (\Exception $e) {
                    // Log error but continue with other nominees
                    \Log::error('Failed to notify nominee: ' . $nominee->email, ['error' => $e->getMessage()]);
                }
            }
        }
    }

    public function show(Poll $poll)
    {
        $poll->load(['categories.nominees', 'eligibleVoters']);

        // Ensure start_at and end_at are ISO 8601 strings for correct JS parsing
        if ($poll->start_at) {
            $poll->start_at = $poll->start_at->toIso8601String();
        }
        if ($poll->end_at) {
            $poll->end_at = $poll->end_at->toIso8601String();
        }

        // For draft polls, allow editing. For active/closed, show read-only view
        if ($poll->status === 'draft') {
            return view('dashboard.sidebar_items.polls.create', compact('poll'));
        }

        return view('dashboard.sidebar_items.polls.show', compact('poll'));
    }

    /**
     * Preview draft poll (public view, no voting)
     */
    public function preview(Poll $poll)
    {
        $poll->load(['categories.nominees']);
        
        return view('polls.preview', compact('poll'));
    }

    public function edit(Poll $poll)
    {
        $poll->load(['categories.nominees', 'eligibleVoters']);

        // Ensure start_at and end_at are ISO 8601 strings for correct JS parsing
        if ($poll->start_at) {
            $poll->start_at = $poll->start_at->toIso8601String();
        }
        if ($poll->end_at) {
            $poll->end_at = $poll->end_at->toIso8601String();
        }

        // Use the same create view but pass the poll for editing
        return view('dashboard.sidebar_items.polls.create', compact('poll'));
    }

    public function update(Request $request, Poll $poll)
    {
        $allowedMethods = ['email', 'phone', 'nrc', 'passport', 'biometric_facial', 'biometric_finger'];

            $data = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'start_at' => ['required', 'date'],
                'end_at' => ['required', 'date', 'after:start_at'],
                'cover_image' => ['nullable', 'image', 'max:5120'],
                'banner_image' => ['nullable', 'image', 'max:10240'],
                'video' => ['nullable', 'mimetypes:video/mp4,video/avi,video/quicktime,video/x-msvideo', 'max:51200'],
                'voting_methods' => ['nullable', 'array'],
                'voting_methods.*' => ['in:' . implode(',', $allowedMethods)],
                'is_public' => ['sometimes', 'boolean'],
                'email_domain' => ['nullable', 'string'],
                'country' => ['nullable', 'string'],
                'allow_vote_edit' => ['sometimes', 'boolean'],
            ]);

            $isPublic = $request->has('is_public') ? (bool) $request->boolean('is_public') : true;

        if (! $isPublic && empty($data['email_domain']) && empty($data['country'])) {
            return back()
                ->withErrors([
                    'email_domain' => 'Provide at least one eligibility field (email_domain or country) when poll is not public.',
                ])
                ->withInput();
        }

        // File uploads: replace existing paths if new files uploaded
            if ($request->hasFile('cover_image')) {
            if ($poll->cover_image) {
                Storage::disk('public')->delete($poll->cover_image);
            }
            $poll->cover_image = $request->file('cover_image')->store('polls/covers', 'public');
            }

            if ($request->hasFile('banner_image')) {
            if ($poll->banner_image) {
                Storage::disk('public')->delete($poll->banner_image);
            }
            $poll->banner_image = $request->file('banner_image')->store('polls/banners', 'public');
            }

            if ($request->hasFile('video')) {
            if ($poll->video) {
                Storage::disk('public')->delete($poll->video);
            }
            $poll->video = $request->file('video')->store('polls/videos', 'public');
            }

            $votingMethods = $data['voting_methods'] ?? [];
        $poll->voting_methods = is_array($votingMethods) && count($votingMethods)
            ? implode(',', $votingMethods)
            : null;

        $poll->name = $data['name'];
        $poll->description = $data['description'] ?? null;
        // Parse datetimes: if ISO-8601 format (from client conversion), parse as UTC.
        // Otherwise parse in server timezone (for manual API calls).
        try {
            $poll->start_at = $data['start_at'];
        } catch (\Exception $e) {
            $poll->start_at = $data['start_at'];
        }
        try {
            $poll->end_at = $data['end_at'];
        } catch (\Exception $e) {
            $poll->end_at = $data['end_at'];
        }
        // NOTE: status is controlled exclusively by publish/draft actions, not update()
        $poll->is_public = $isPublic;
        $poll->email_domain = $data['email_domain'] ?? null;
        $poll->country = $data['country'] ?? null;
        $poll->allow_vote_edit = $request->has('allow_vote_edit')
            ? (bool) $request->boolean('allow_vote_edit')
            : false;

        $poll->save();

        return redirect()
            ->route('polls.index')
            ->with('success', 'Poll updated successfully.');
    }

    public function destroy(Poll $poll)
    {
        $poll->delete();

        return redirect()
            ->route('polls.index')
            ->with('success', 'Poll deleted successfully.');
    }

    /* =======================
     | Categories
     ======================= */

    public function storeCategory(Request $request, Poll $poll)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $category = $poll->categories()->create($data);

        return response()->json([
            'success' => true,
            'category' => $category,
            'message' => 'Category created successfully.',
        ]);
    }

    public function updateCategory(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $category->update($data);

        return response()->json([
            'success' => true,
            'category' => $category->fresh(),
            'message' => 'Category updated successfully.',
        ]);
    }

    public function deleteCategory(Category $category)
    {
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully.',
        ]);
    }

    /* =======================
     | Nominees
     ======================= */

    public function storeNominee(Request $request, Poll $poll)
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string'],
            'social_link' => ['nullable', 'url'],
            'bio' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('nominees/photos', 'public');
        }

        $nominee = $poll->nominees()->create([
            ...$data,
            'photo' => $photoPath,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'nominee' => $nominee,
            'message' => 'Nominee added successfully.',
        ]);
    }

    /**
     * Import nominees from CSV
     */
    public function importNominees(Request $request, Poll $poll)
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
            'category_id' => ['required', 'exists:categories,id'],
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        $data = array_map('str_getcsv', file($path));

        // Skip header row
        $header = array_shift($data);
        $imported = 0;
        $errors = [];

        foreach ($data as $index => $row) {
            try {
                // Expected CSV format: name, email, phone, social_link, bio
                $nomineeData = [
                    'poll_id' => $poll->id,
                    'category_id' => $request->category_id,
                    'name' => $row[0] ?? null,
                    'email' => $row[1] ?? null,
                    'phone' => $row[2] ?? null,
                    'social_link' => $row[3] ?? null,
                    'bio' => $row[4] ?? null,
                    'status' => 'pending',
                ];

                if (empty($nomineeData['name'])) {
                    $errors[] = "Row " . ($index + 2) . ": Name is required";
                    continue;
                }

                Nominee::create($nomineeData);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => true,
            'imported' => $imported,
            'errors' => $errors,
            'message' => "Successfully imported {$imported} nominees.",
        ]);
    }

    /**
     * Generate self-registration link for nominees
     */
    public function generateNomineeRegistrationLink(Poll $poll)
    {
        $token = $poll->generateNomineeRegistrationToken();
        $url = route('nominees.register', ['poll' => $poll->uuid, 'token' => $token]);

        return response()->json([
            'success' => true,
            'url' => $url,
            'token' => $token,
            'message' => 'Registration link generated successfully.',
        ]);
    }

    public function updateNominee(Request $request, Nominee $nominee)
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string'],
            'social_link' => ['nullable', 'url'],
            'bio' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('photo')) {
            if ($nominee->photo) {
                Storage::disk('public')->delete($nominee->photo);
            }
            $data['photo'] = $request->file('photo')->store('nominees/photos', 'public');
        }

        $nominee->update($data);

        return response()->json([
            'success' => true,
            'nominee' => $nominee->fresh(),
            'message' => 'Nominee updated successfully.',
        ]);
    }

    public function approveNominee(Nominee $nominee)
    {
        $nominee->update(['status' => 'approved']);

        return response()->json([
            'success' => true,
            'nominee' => $nominee->fresh(),
            'message' => 'Nominee approved successfully.',
        ]);
    }

    public function deleteNominee(Nominee $nominee)
    {
        $nominee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Nominee deleted successfully.',
        ]);
    }

    /* =======================
     | Eligible Voters (for private polls)
     ======================= */

    /**
     * Add eligible voter manually
     */
    public function storeEligibleVoter(Request $request, Poll $poll)
    {
        $data = $request->validate([
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string'],
            'name' => ['nullable', 'string'],
            'identifier_type' => ['nullable', 'in:email,phone,nrc,passport'],
            'identifier_value' => ['nullable', 'string'],
        ]);

        $voter = $poll->eligibleVoters()->create($data);

        return response()->json([
            'success' => true,
            'voter' => $voter,
            'message' => 'Eligible voter added successfully.',
        ]);
    }

    /**
     * Import eligible voters from CSV
     */
    public function importEligibleVoters(Request $request, Poll $poll)
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        $data = array_map('str_getcsv', file($path));

        // Skip header row
        $header = array_shift($data);
        $imported = 0;
        $errors = [];

        foreach ($data as $index => $row) {
            try {
                // Expected CSV format: email, phone, name, identifier_type, identifier_value
                $voterData = [
                    'poll_id' => $poll->id,
                    'email' => $row[0] ?? null,
                    'phone' => $row[1] ?? null,
                    'name' => $row[2] ?? null,
                    'identifier_type' => $row[3] ?? null,
                    'identifier_value' => $row[4] ?? null,
                ];

                if (empty($voterData['email']) && empty($voterData['phone']) && empty($voterData['identifier_value'])) {
                    $errors[] = "Row " . ($index + 2) . ": At least one identifier (email, phone, or identifier_value) is required";
                    continue;
                }

                EligibleVoter::create($voterData);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => true,
            'imported' => $imported,
            'errors' => $errors,
            'message' => "Successfully imported {$imported} eligible voters.",
        ]);
    }

    /**
     * Generate self-registration link for voters
     */
    public function generateVoterRegistrationLink(Poll $poll)
    {
        $token = $poll->generateVoterRegistrationToken();
        $url = route('voters.register', ['poll' => $poll->uuid, 'token' => $token]);

        return response()->json([
            'success' => true,
            'url' => $url,
            'token' => $token,
            'message' => 'Voter registration link generated successfully.',
        ]);
    }

    public function deleteEligibleVoter(EligibleVoter $voter)
    {
        $voter->delete();

        return response()->json([
            'success' => true,
            'message' => 'Eligible voter deleted successfully.',
        ]);
    }

    /* =======================
     | Voting (stubs)
     ======================= */

    public function showVote(Poll $poll)
    {
        // Ensure poll is active
        $poll->load(['categories.nominees']);

        if ($poll->status !== 'active' || $poll->isClosed()) {
            // If not active or has passed end time, show preview with message
            $message = $poll->status === 'draft' 
                ? 'This poll is not yet published.' 
                : 'This poll has ended.';
            return view('polls.preview', compact('poll'))->with('message', $message);
        }

        return view('polls.vote', compact('poll'));
    }

    public function submitVote(Request $request, Poll $poll)
    {
        // To be implemented: handle vote submission
        return response()->json([
            'success' => false,
            'message' => 'Voting not implemented yet.'
        ], 501);
    }

    public function results(Poll $poll)
    {
        // To be implemented
    }

    /**
     * Download CSV template for nominees
     */
    public function downloadNomineesCSVTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="nominees_template.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            // Header row
            fputcsv($file, ['name', 'email', 'phone', 'social_link', 'bio']);
            // Example row
            fputcsv($file, ['John Doe', 'john@example.com', '+1234567890', 'https://linkedin.com/in/johndoe', 'Experienced professional']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Download CSV template for eligible voters
     */
    public function downloadVotersCSVTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="eligible_voters_template.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            // Header row
            fputcsv($file, ['email', 'phone', 'name', 'identifier_type', 'identifier_value']);
            // Example rows
            fputcsv($file, ['voter1@example.com', '+1234567890', 'Jane Smith', 'nrc', '123456/78/1']);
            fputcsv($file, ['voter2@example.com', '', 'Bob Johnson', 'passport', 'AB123456']);
            fputcsv($file, ['', '+9876543210', 'Alice Williams', '', '']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Handle nominee self-registration submission
     */
    public function submitNomineeRegistration(Request $request)
    {
        $request->validate([
            'poll_id' => ['required', 'exists:polls,id'],
            'token' => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string'],
            'social_link' => ['nullable', 'url'],
            'bio' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $poll = Poll::findOrFail($request->poll_id);

        // Verify token
        if ($poll->nominee_registration_token !== $request->token) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid registration token.',
            ], 403);
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('nominees/photos', 'public');
        }

        $nominee = Nominee::create([
            'poll_id' => $poll->id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'social_link' => $request->social_link,
            'bio' => $request->bio,
            'photo' => $photoPath,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registration submitted successfully. You will be notified once approved.',
        ]);
    }

    /**
     * Handle voter self-registration submission
     */
    public function submitVoterRegistration(Request $request)
    {
        $request->validate([
            'poll_id' => ['required', 'exists:polls,id'],
            'token' => ['required', 'string'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string'],
            'name' => ['nullable', 'string'],
            'identifier_type' => ['nullable', 'in:nrc,passport'],
            'identifier_value' => ['nullable', 'string'],
        ]);

        $poll = Poll::findOrFail($request->poll_id);

        // Verify token
        if ($poll->voter_registration_token !== $request->token) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid registration token.',
            ], 403);
        }

        // Ensure at least one identifier is provided
        if (empty($request->email) && empty($request->phone) && empty($request->identifier_value)) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide at least one contact method (email, phone, or identifier).',
            ], 422);
        }

        try {
            $voter = EligibleVoter::create([
                'poll_id' => $poll->id,
                'email' => $request->email,
                'phone' => $request->phone,
                'name' => $request->name,
                'identifier_type' => $request->identifier_type,
                'identifier_value' => $request->identifier_value,
                'registered_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Registration successful. You are now eligible to vote.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed. You may already be registered.',
            ], 422);
        }
    }
}
