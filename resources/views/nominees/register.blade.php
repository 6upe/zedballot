<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Nominee Registration - {{ $poll->name }}</title>
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
            <p class="mb-0">Nominee Self-Registration</p>
        </div>
    </div>

    <div class="container">
        <div class="card registration-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user-plus mr-2"></i>Register as Nominee</h5>
            </div>
            <div class="card-body">
                @if($poll->description)
                    <div class="alert alert-info">
                        {{ $poll->description }}
                    </div>
                @endif

                <form id="nomineeRegistrationForm">
                    <div class="form-group">
                        <label for="category_id">Category <span class="text-danger">*</span></label>
                        <select id="category_id" name="category_id" class="form-control" required>
                            <option value="">Select Category</option>
                            @foreach($poll->categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-group">
                        <label for="name">Full Name <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="phone">Phone</label>
                            <input type="text" id="phone" name="phone" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="social_link">Social Media Link</label>
                        <input type="url" id="social_link" name="social_link" class="form-control" placeholder="https://...">
                    </div>

                    <div class="form-group">
                        <label for="bio">Bio/Description</label>
                        <textarea id="bio" name="bio" class="form-control" rows="4" placeholder="Tell us about yourself..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="photo">Photo</label>
                        <input type="file" id="photo" name="photo" class="form-control-file" accept="image/*">
                        <small class="form-text text-muted">Upload your profile photo (optional)</small>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle mr-2"></i>
                        Your registration will be reviewed by the poll administrator before approval.
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
        document.getElementById('nomineeRegistrationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('poll_id', {{ $poll->id }});
            formData.append('token', '{{ $token }}');
            
            fetch('{{ route("nominees.register.submit") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Registration submitted successfully! You will be notified once approved.');
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

