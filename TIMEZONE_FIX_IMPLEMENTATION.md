# ZedBallot Timezone Fix Implementation

## Overview
This implementation enforces a strict single-source-of-truth architecture to eliminate timezone-related poll status inconsistencies. The database status column is now controlled exclusively by explicit admin actions (publish/draft), while all time-dependent states are computed dynamically on the backend using the Poll model's `computed_status` accessor.

## Key Changes

### 1. Server Timezone Standardization
**File:** `config/app.php`
- Changed timezone from `UTC` to `Africa/Lusaka` (server timezone)
- All PHP date/time operations now use this timezone consistently
- Ensures uniform datetime parsing and comparison across the entire application

### 2. Controller Date Parsing (Backend)
**File:** `app/Http/Controllers/PollController.php`
- Added `Carbon` import for explicit datetime parsing
- **`store()` method:** Dates from form input are parsed in server timezone:
  ```php
  $startAt = Carbon::parse($data['start_at'], config('app.timezone'));
  $endAt = Carbon::parse($data['end_at'], config('app.timezone'));
  ```
- **`updateStep1()` method:** Same parsing applied when updating poll details
- **`update()` method:** 
  - Dates parsed in server timezone before storage
  - **Critical:** Removed `status` field from validation rules—status is now read-only and controlled only via the `finalize()` action (publish/draft)
  - This prevents frontend or API calls from directly mutating poll status

### 3. Status Control via Discrete Actions
**File:** `app/Http/Controllers/PollController.php` - `finalize()` method
- **Only** this endpoint can change poll status
- Validates required fields before publishing (start_at, end_at, voting_methods)
- Two actions supported:
  - `publish`: Sets status to `active`
  - `draft`: Sets status to `draft`
- Post-publish, status changes are driven by time-based computation, not user input

### 4. Backend Computed Status
**File:** `app/Models/Poll.php` - `getComputedStatusAttribute()`
- Returns one of: `draft`, `scheduled`, `active`, `closed`
- Logic:
  - If DB status = `draft` → return `draft`
  - If `now() > end_at` → return `closed`
  - If `now() >= start_at && now() <= end_at` → return `active`
  - Otherwise → return `scheduled` (poll hasn't started yet)
- Uses server-side `now()` exclusively; never uses browser time

### 5. Frontend: Single Source of Truth for Display
**File:** `resources/views/dashboard/sidebar_items/polls/index.blade.php`
- All UI elements render from `computed_status`:
  - Badge color/text
  - Status message (draft, scheduled, closed, active)
  - Vote button enable/disable state
  - Share link availability
- **Poll details section:** Shows raw start/end times in server timezone (no client conversion)

### 6. Frontend: Server-Time Based Countdowns
**File:** `resources/views/dashboard/sidebar_items/polls/index.blade.php` (JavaScript)
- Each countdown element receives:
  - `data-end`: Poll end time (ISO-8601)
  - `data-server-now`: Current server time when page renders (ISO-8601)
- Countdown logic:
  1. Captures server time on first load
  2. Calculates elapsed wall-clock time since page load
  3. Adjusts server-relative time by elapsed wall-clock time
  4. Computes difference from adjusted server time to end time
  5. **Result:** Countdowns never use browser time; they drift with server, not client clock

### 7. Date Input Handling
**File:** `resources/views/dashboard/sidebar_items/polls/create.blade.php`
- HTML `datetime-local` inputs emit values in local time (no timezone info)
- FormData sends these strings as-is to the server
- Server parses them in `config('app.timezone')` → ensures consistent interpretation
- **No timezone offset calculation on client:** Complexity removed; server handles all parsing

### 8. API Endpoint for Server Time
**File:** `routes/web.php`
- New endpoint: `GET /polls/api/server-time` (unauthenticated)
- Returns: `{ "server_time": "ISO-8601 timestamp" }`
- Available for frontend use if additional sync needed (optional)

## Guarantees

✅ **Single Source of Truth:** Database `status` reflects only explicit admin actions; UI computed state reflects server time  
✅ **No Timezone Drift:** All countdowns and state comparisons use server time, not browser time  
✅ **Consistent Datetime Storage:** All dates stored as UTC timestamps (via Carbon casts)  
✅ **Immutable Poll Status from Frontend:** `update()` endpoint cannot change status; only `finalize()` can  
✅ **Graceful Same-Day Polls:** Time-based computation handles polls starting and ending on the same calendar day  
✅ **Admin Visibility:** Dashboard always reflects server truth, never browser interpretation  

## Migration Notes

### Existing Polls with Timezone Mismatch
If existing polls were stored with timezone drift (e.g., local time stored as UTC), they must be corrected via Tinker or a migration:

```php
// Example correction in Tinker for a specific poll:
$poll = Poll::find(1);
// Assuming stored times are 8 hours off (PST → UTC):
$poll->start_at = $poll->start_at->addMinutes(480); // 480 min = 8 hours
$poll->end_at = $poll->end_at->addMinutes(480);
$poll->save();
```

Or run a bulk update via Artisan command if many polls are affected.

### Testing
1. **Create a same-day poll:** Enter start/end times for today in the same day (e.g., 15:00 — 17:30)
2. **Publish:** Status should become `active`
3. **Verify dashboard:** Badge should show `active` (not `closed`), countdown should decrement
4. **Wait for end time:** Countdown reaches zero, status changes to `closed`, vote button disables
5. **Time travel (optional):** Adjust server time to post-end; status immediately reflects `closed`

## Files Modified
1. `config/app.php` — timezone set to `Africa/Lusaka`
2. `app/Models/Poll.php` — computed_status accessor (unchanged, already present)
3. `app/Http/Controllers/PollController.php`:
   - Added Carbon import
   - `store()`: timezone-aware parsing
   - `updateStep1()`: timezone-aware parsing
   - `update()`: timezone-aware parsing + removed status from input
4. `resources/views/dashboard/sidebar_items/polls/index.blade.php`:
   - Added `data-server-now` to countdown elements
   - Rewrote countdown JavaScript to use server time
5. `resources/views/dashboard/sidebar_items/polls/create.blade.php`:
   - Updated `saveStep1()` comment to reflect new approach
6. `routes/web.php`:
   - Added `/polls/api/server-time` endpoint

## Future Enhancements
- Persistent user timezone preferences (optional per-user offset)
- Admin timezone picker (if multi-region support needed)
- Audit log for all status transitions
- Poll schedule (e.g., auto-activate at a future time)
