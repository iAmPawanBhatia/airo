<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quotation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container bg-white p-4 rounded shadow" style="max-width: 500px;">
        @if(session('jwt_token'))
            <!-- Authenticated User View -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">Insurance Quotation</h1>
                    <p class="text-muted small">Logged in as: <strong>{{ session('user_email') }}</strong></p>
                </div>
                <form action="/logout" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">Logout</button>
                </form>
            </div>

            <div class="mb-4">
                <h2 class="h5 mb-3 border-bottom pb-2">Request Quotation</h2>
                <form id="quotation-form">
                    <div class="mb-3">
                        <label for="age" class="form-label">Ages (comma-separated)</label>
                        <input id="age" name="age" type="text" class="form-control" value="" required placeholder="e.g., 28,35">
                    </div>

                    <div class="mb-3">
                        <label for="currency_id" class="form-label">Currency</label>
                        <select id="currency_id" name="currency_id" class="form-select" required>
                            <option value="EUR">EUR (Euro)</option>
                            <option value="GBP">GBP (British Pound)</option>
                            <option value="USD">USD (US Dollar)</option>
                        </select>
                    </div>

                    <div class="row g-3">
                        <div class="col">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input id="start_date" name="start_date" type="date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                        </div>

                        <div class="col">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input id="end_date" name="end_date" type="date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                            </div>                            
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Get Quotation</button>
                </form>
            </div>

            <div id="result-section" style="display: none;">
                <div class="alert alert-info border-start border-primary border-4">
                    <div class="fw-bold mb-1">Total Price:</div>
                    <div class="h4 text-primary fw-bold mb-2" id="result-total">-</div>
                    <div class="fw-bold mb-1">Currency:</div>
                    <div class="mb-2" id="result-currency">-</div>
                    <div class="fw-bold mb-1">Quotation ID:</div>
                    <div id="result-quotation-id">-</div>
                </div>
            </div>

            <div id="error-section" style="display: none;">
                <div class="alert alert-danger" id="error-message"></div>
            </div>

            <script>
                document.getElementById('quotation-form').addEventListener('submit', async function (e) {
                    e.preventDefault();
                    document.getElementById('result-section').style.display = 'none';
                    document.getElementById('error-section').style.display = 'none';

                    const body = {
                        age: document.getElementById('age').value.trim(),
                        currency_id: document.getElementById('currency_id').value,
                        start_date: document.getElementById('start_date').value,
                        end_date: document.getElementById('end_date').value,
                    };

                    try {
                        const response = await fetch('/api/quotation', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'Authorization': 'Bearer {{ session("jwt_token") }}',
                            },
                            body: JSON.stringify(body),
                        });

                        const data = await response.json().catch(function () {
                            return { message: 'Invalid JSON response' };
                        });

                        console.log('API Response:', data);
                        
                        function getFriendlyError(errData) {
                            if (typeof errData !== 'object' || errData === null) {
                                return String(errData);
                            }

                            if (Array.isArray(errData)) {
                                return errData.join('; ');
                            }

                            if (errData.errors && typeof errData.errors === 'object') {
                                return Object.entries(errData.errors)
                                    .map(([field, messages]) => {
                                        if (Array.isArray(messages)) {
                                            return messages.join(' ');
                                        }
                                        return String(messages);
                                    })
                                    .join(' ');
                            }

                            if (errData.message) {
                                return errData.message;
                            }

                            return JSON.stringify(errData, null, 2);
                        }

                        if (!response.ok) {
                            document.getElementById('error-message').textContent = getFriendlyError(data);
                            document.getElementById('error-section').style.display = 'block';
                            return;
                        }

                        document.getElementById('result-total').textContent = data.total;
                        document.getElementById('result-currency').textContent = data.currency_id;
                        document.getElementById('result-quotation-id').textContent = data.quotation_id;
                        document.getElementById('result-section').style.display = 'block';
                        } catch (err) {
                            document.getElementById('error-message').textContent = err.message;
                            document.getElementById('error-section').style.display = 'block';
                        }
                });
            </script>

        @else
            <!-- Not Authenticated - Show Login/Register Forms -->
            <h1 class="h3 mb-4">Insurance Quotation System</h1>

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif

            <ul class="nav nav-tabs mb-4" id="authTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab" aria-controls="login" aria-selected="true">Login</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab" aria-controls="register" aria-selected="false">Create Account</button>
                </li>
            </ul>

            <div class="tab-content" id="authTabsContent">
                <!-- Login Tab -->
                <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
                    <p class="text-muted mb-3">Sign in to your account to get quotations</p>
                    <form action="/login" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="login-email" class="form-label">Email</label>
                            <input id="login-email" name="email" type="email" class="form-control" value="{{ old('email') }}" required placeholder="your@email.com">
                            @error('email')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="login-password" class="form-label">Password</label>
                            <input id="login-password" name="password" type="password" class="form-control" required placeholder="Enter your password">
                            @error('password')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                </div>

                <!-- Register Tab -->
                <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
                    <p class="text-muted mb-3">Create a new account to get started</p>
                    <form action="/register" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="register-email" class="form-label">Email</label>
                            <input id="register-email" name="email" type="email" class="form-control" value="{{ old('email') }}" required placeholder="your@email.com">
                            @error('email')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="register-password" class="form-label">Password</label>
                            <input id="register-password" name="password" type="password" class="form-control" required placeholder="Minimum 6 characters">
                            @error('password')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="register-password-confirm" class="form-label">Confirm Password</label>
                            <input id="register-password-confirm" name="password_confirmation" type="password" class="form-control" required placeholder="Confirm your password">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Create Account</button>
                    </form>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        @endif
    </div>
</body>
</html>
