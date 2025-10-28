<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Server Information</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        pre { white-space: pre-wrap; word-break: break-word; }
    </style>
</head>
<body class="bg-light">
<div class="container py-4">
    <h1 class="mb-3">Server Information</h1>
    <p class="text-muted">Detected OS: {{ $data['os'] }}</p>

    <div class="row g-3">
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-header">Uptime</div>
                <div class="card-body"><pre class="mb-0">{{ $data['uptime'] }}</pre></div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-header">Memory</div>
                <div class="card-body"><pre class="mb-0">{{ $data['memory'] }}</pre></div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-header">Disk</div>
                <div class="card-body"><pre class="mb-0">{{ $data['disk'] }}</pre></div>
            </div>
        </div>
    </div>

    <p class="mt-3 text-muted small">Note: these outputs are produced by executing system commands. If they appear blank, your PHP setup may have <code>shell_exec</code> disabled or commands may not be available on the host.</p>
</div>
</body>
</html>
