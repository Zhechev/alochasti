            </style>
        @endif
    </head>
    <body class="bg-light">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <h1 class="h3 mb-2">{{ config('app.name', 'GiftShare') }}</h1>
                            <p class="text-secondary mb-4">A small community gifting board built with Laravel, Livewire, and Bootstrap.</p>

                            <div class="d-flex gap-2">
                                @auth
                                    <a href="{{ route('dashboard') }}" class="btn btn-primary">Go to dashboard</a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-primary">Log in</a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="btn btn-outline-secondary">Register</a>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
