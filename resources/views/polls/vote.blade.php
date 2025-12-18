<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote: {{ $poll->name }}</title>
    <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/fontawesome/css/all.min.css') }}">
    <style>
        .poll-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        .poll-cover {
            width: 100%;
            max-height: 300px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        .poll-banner {
            width: 100%;
            max-height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .category-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            background: white;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        .nominee-option {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            background: white;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .nominee-option:hover {
            border-color: #667eea;
            background: #f8f9ff;
        }
        .nominee-option input[type="radio"] {
            cursor: pointer;
        }
        .nominee-option.selected {
            border-color: #667eea;
            background: #f0f2ff;
        }
        .nominee-photo {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            margin-right: 1rem;
        }
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .status-active { background: #28a745; color: white; }
        .btn-vote {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 0.7rem 2rem;
            font-weight: 600;
            border-radius: 6px;
            transition: transform 0.2s ease;
        }
        .btn-vote:hover {
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        .poll-dates {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="poll-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-2">{{ $poll->name }}</h1>
                    <p class="mb-0">{{ $poll->description }}</p>
                </div>
                <div class="col-md-4 text-right">
                    <span class="status-badge status-active">VOTING OPEN</span>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        @if($poll->cover_image)
            <img src="{{ asset('storage/'.$poll->cover_image) }}" alt="Cover" class="poll-cover">
        @endif

        @if($poll->banner_image)
            <img src="{{ asset('storage/'.$poll->banner_image) }}" alt="Banner" class="poll-banner">
        @endif

        @if($poll->video)
            <div class="mb-4">
                <h5><i class="fas fa-video mr-2"></i>Intro Video</h5>
                <video controls class="w-100" style="max-height: 400px; border-radius: 8px;">
                    <source src="{{ asset('storage/'.$poll->video) }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        @endif

        <div class="poll-dates">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="mb-1"><i class="fas fa-clock mr-2"></i>Voting Period</h6>
                    <p class="mb-0"><strong>Start:</strong> {{ $poll->start_at ? $poll->start_at->format('M d, Y h:i A') : 'N/A' }}</p>
                    <p class="mb-0"><strong>End:</strong> {{ $poll->end_at ? $poll->end_at->format('M d, Y h:i A') : 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="mb-1"><i class="fas fa-check-circle mr-2"></i>Voting Methods</h6>
                    <p class="mb-0">
                        @if($poll->voting_methods)
                            @foreach(explode(',', $poll->voting_methods) as $method)
                                <span class="badge badge-info mr-1">{{ ucfirst(str_replace('_', ' ', $method)) }}</span>
                            @endforeach
                        @else
                            <span class="text-muted">Not configured</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <hr>

        <h3 class="mb-4"><i class="fas fa-list mr-2"></i>Categories & Nominees</h3>

        @if($poll->categories && $poll->categories->count() > 0)
            <form id="votingForm" method="POST" action="{{ route('polls.vote.submit', $poll) }}">
                @csrf

                <h4 class="mb-3">Voter Information</h4>
                <input type="hidden" name="poll_id" value="{{ $poll->id }}">
                @php $methods = array_map('trim', explode(',', $poll->voting_methods)); @endphp
                <div class="form-row">
                    @if(in_array('name', $methods) || $poll->is_public)
                    <div class="form-group col-md-6">
                        <label for="voter_name">Name</label>
                        <input type="text" class="form-control" id="voter_name" name="name" required>
                    </div>
                    @endif
                    @if(in_array('email', $methods))
                    <div class="form-group col-md-6">
                        <label for="voter_email">Email</label>
                        <input type="email" class="form-control" id="voter_email" name="email" required>
                    </div>
                    @endif
                    @if(in_array('phone', $methods))
                    <div class="form-group col-md-6">
                        <label for="voter_phone">Phone</label>
                        <input type="text" class="form-control" id="voter_phone" name="phone" required>
                    </div>
                    @endif
                    @if(in_array('nrc', $methods))
                    <div class="form-group col-md-6">
                        <label for="voter_nrc">NRC</label>
                        <input type="text" class="form-control" id="voter_nrc" name="nrc" required>
                    </div>
                    @endif
                    @if(in_array('passport', $methods))
                    <div class="form-group col-md-6">
                        <label for="voter_passport">Passport</label>
                        <input type="text" class="form-control" id="voter_passport" name="passport" required>
                    </div>
                    @endif
                </div>
                @if(count($methods) === 1)
                    <input type="hidden" name="identifier_type" value="{{ $methods[0] }}">
                    <input type="hidden" name="identifier_value" value="{{ $methods[0] === 'email' ? old('email') : ( ($methods[0] === 'phone') ? old('phone') : ( ($methods[0] === 'nrc') ? old('nrc') : old('passport') ) ) }}">
                @else
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="identifier_type">Identifier Type</label>
                            <select class="form-control" id="identifier_type" name="identifier_type" required>
                                @foreach($methods as $method)
                                    <option value="{{ $method }}">{{ ucfirst($method) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="identifier_value">Identifier Value</label>
                            <input type="text" class="form-control" id="identifier_value" name="identifier_value" required>
                        </div>
                    </div>
                @endif

                <hr>

                @foreach($poll->categories as $category)
                    <div class="category-card">
                        <h5 class="mb-3">
                            <i class="fas fa-folder mr-2"></i>{{ $category->name }}
                        </h5>
                        @if($category->description)
                            <p class="text-muted mb-3">{{ $category->description }}</p>
                        @endif

                        @php
                            $categoryNominees = $poll->nominees->where('category_id', $category->id);
                        @endphp

                        @if($categoryNominees->count() > 0)
                            <div class="nominees-list">
                                @foreach($categoryNominees as $nominee)
                                    <label class="nominee-option" onclick="this.querySelector('input').checked = true;">
                                        <div class="d-flex align-items-start">
                                            <input type="radio" name="votes[{{ $category->id }}]" value="{{ $nominee->id }}" 
                                                   style="margin-top: 0.5rem;">
                                            <div class="flex-grow-1">
                                                @if($nominee->photo)
                                                    <img src="{{ asset('storage/'.$nominee->photo) }}" alt="{{ $nominee->name }}" 
                                                         class="nominee-photo">
                                                @else
                                                    <div class="nominee-photo" style="background: #e0e0e0; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-user" style="font-size: 24px; color: #999;"></i>
                                                    </div>
                                                @endif
                                                <div class="d-inline-block flex-grow-1 ml-2">
                                                    <h6 class="mb-1">{{ $nominee->name }}</h6>
                                                    @if($nominee->email)
                                                        <p class="text-muted small mb-1"><i class="fas fa-envelope mr-1"></i>{{ $nominee->email }}</p>
                                                    @endif
                                                    @if($nominee->phone)
                                                        <p class="text-muted small mb-1"><i class="fas fa-phone mr-1"></i>{{ $nominee->phone }}</p>
                                                    @endif
                                                    @if($nominee->bio)
                                                        <p class="text-muted small mb-2">{{ Str::limit($nominee->bio, 120) }}</p>
                                                    @endif
                                                    @if($nominee->social_link)
                                                        <p class="small mb-0">
                                                            <a href="{{ $nominee->social_link }}" target="_blank" rel="noopener">
                                                                <i class="fas fa-external-link-alt mr-1"></i>Profile
                                                            </a>
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No nominees in this category.</p>
                        @endif
                    </div>
                @endforeach

                <div class="text-center mb-4">
                    <button type="submit" class="btn btn-vote btn-lg">
                        <i class="fas fa-check-circle mr-2"></i>Submit Your Vote
                    </button>
                </div>
            </form>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i>No categories have been added to this poll yet.
            </div>
        @endif

        <div class="text-center mt-4 mb-4">
            <a href="{{ route('polls.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Polls
            </a>
        </div>
    </div>

    <script src="{{ asset('assets/modules/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/modules/bootstrap/js/bootstrap.min.js') }}"></script>
    <script>
        // Highlight selected nominee option
        document.querySelectorAll('.nominee-option input').forEach(input => {
            input.addEventListener('change', function() {
                // Remove selected class from siblings
                this.parentElement.parentElement.querySelectorAll('.nominee-option').forEach(opt => {
                    opt.classList.remove('selected');
                });
                // Add selected class to parent
                this.closest('.nominee-option').classList.add('selected');
            });
        });

        // Handle form submission
        // Remove JS alert and allow normal form submission
    </script>
</body>
</html>
