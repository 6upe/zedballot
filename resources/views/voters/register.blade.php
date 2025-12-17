<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Voter Registration - {{ $poll->name }}</title>
    <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/fontawesome/css/all.min.css') }}">
    <style>
        .registration-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .registration-card {
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="registration-header">
        <div class="container text-center">
            <h2>{{ $poll->name }}</h2>
            <p class="mb-0">Voter Self-Registration</p>
        </div>
    </div>

    <div class="container">
        <div class="card registration-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user-check mr-2"></i>Register as Eligible Voter</h5>
            </div>
            <div class="card-body">
                @if($poll->description)
                    <div class="alert alert-info">
                        {{ $poll->description }}
                    </div>
                @endif

                <form id="voterRegistrationForm">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" class="form-control">
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control">
                            <small class="form-text text-muted">At least one identifier is required</small>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="phone">Phone</label>
                            <input type="text" id="phone" name="phone" class="form-control">
                        </div>
                    </div>

                    <hr>

                    <h6>Additional Identifier (Optional)</h6>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="identifier_type">Identifier Type</label>
                            <select id="identifier_type" name="identifier_type" class="form-control">
                                <option value="">Select Type</option>
                                <option value="nrc">NRC</option>
                                <option value="passport">Passport</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="identifier_value">Identifier Value</label>
                            <input type="text" id="identifier_value" name="identifier_value" class="form-control">
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        Please provide at least one contact method (email or phone) to register.
                    </div>

                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-paper-plane mr-2"></i>Submit Registration
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="{{ url('/') }}" class="text-muted">Return to Home</a>
        </div>
    </div>

    <script src="{{ asset('assets/modules/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/modules/bootstrap/js/bootstrap.min.js') }}"></script>
    <script>
        document.getElementById('voterRegistrationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;
            const identifierValue = document.getElementById('identifier_value').value;
            
            if (!email && !phone && !identifierValue) {
                alert('Please provide at least one contact method (email, phone, or identifier)');
                return;
            }
            
            const formData = new FormData(this);
            formData.append('poll_id', {{ $poll->id }});
            formData.append('token', '{{ $token }}');
            
            fetch('{{ route("voters.register.submit") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Registration submitted successfully! You are now eligible to vote.');
                    window.location.href = '{{ url("/") }}';
                } else {
                    alert(data.message || 'Error submitting registration');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error submitting registration. Please try again.');
            });
        });
    </script>
</body>
</html>

