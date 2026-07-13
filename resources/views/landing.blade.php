<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Switchboard — STR support triage</title>
    <style>
        :root { color-scheme: dark; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: #0a0a0b; color: #e7e5e4;
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            line-height: 1.6; -webkit-font-smoothing: antialiased;
            display: flex; min-height: 100vh; align-items: center; justify-content: center; padding: 2rem;
        }
        .wrap { max-width: 640px; width: 100%; }
        .brand { display: flex; align-items: center; gap: .6rem; margin-bottom: 2rem; }
        .dot { width: 10px; height: 10px; border-radius: 50%; background: #f59e0b; box-shadow: 0 0 12px #f59e0b; }
        .brand h1 { font-size: 1.15rem; font-weight: 600; letter-spacing: -.01em; }
        h2 { font-size: 1.9rem; font-weight: 700; letter-spacing: -.02em; margin-bottom: .75rem; line-height: 1.2; }
        .lede { color: #a8a29e; margin-bottom: 2rem; font-size: 1.05rem; }
        .card { background: #141416; border: 1px solid #26262a; border-radius: 12px; padding: 1.25rem 1.4rem; margin-bottom: 1rem; }
        .card h3 { font-size: .8rem; text-transform: uppercase; letter-spacing: .06em; color: #78716c; margin-bottom: .5rem; }
        .card p { color: #d6d3d1; font-size: .95rem; }
        a.btn { display: inline-block; margin-top: .75rem; background: #f59e0b; color: #1c1917; text-decoration: none;
            font-weight: 600; padding: .55rem 1.1rem; border-radius: 8px; font-size: .9rem; }
        a.btn:hover { background: #fbbf24; }
        code { display: block; background: #0a0a0b; border: 1px solid #26262a; border-radius: 8px; padding: .8rem 1rem;
            font-family: ui-monospace, "SF Mono", Menlo, monospace; font-size: .82rem; color: #a3e635;
            white-space: pre-wrap; word-break: break-word; margin-top: .6rem; }
        .creds { color: #a8a29e; font-size: .85rem; margin-top: .5rem; }
        .creds b { color: #e7e5e4; font-weight: 600; }
        footer { margin-top: 2rem; color: #57534e; font-size: .8rem; }
        footer a { color: #78716c; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="brand"><span class="dot"></span><h1>Switchboard</h1></div>
        <h2>Support triage for short-term rental teams.</h2>
        <p class="lede">
            Guest messages arrive from every channel. Switchboard matches each one to its reservation,
            classifies the issue, drafts a reply grounded in the real booking, and escalates to a human
            when it isn't confident enough to answer. Built with Laravel and Filament.
        </p>

        <div class="card">
            <h3>Support team back office</h3>
            <p>The Filament admin panel: ticket queue, reservation context, and the triage reasoning per ticket.</p>
            <a class="btn" href="/admin">Open the admin panel &rarr;</a>
            <p class="creds">Demo login &mdash; <b>demo@switchboard.test</b> / <b>switchboard</b></p>
        </div>

        <div class="card">
            <h3>The triage API</h3>
            <p>Hand it a raw guest message; it returns structured context plus a review-ready draft. This is the seam an AI support tool would call.</p>
            <code>curl -X POST {{ url('/api/triage') }} \
  -H "Content-Type: application/json" -H "Accept: application/json" \
  -d '{"from":"anna@example.com","message":"whats the wifi password?","channel":"airbnb"}'</code>
        </div>

        <footer>
            Source &amp; architecture notes on <a href="https://github.com/Holodeck23/switchboard">GitHub</a>.
        </footer>
    </div>
</body>
</html>
