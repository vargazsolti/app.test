<!doctype html>
<html lang="hu">
<head>
  <meta charset="utf-8">
  <title>@yield('title', 'Lightwave UI')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    :root { --gap: 16px; --radius: 12px; }
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; margin: 0; background:#fafafa; color:#222; }
    header { background:#111; color:#fff; padding:16px 20px; display:flex; align-items:center; justify-content:space-between; }
    header a { color:#fff; text-decoration:none; margin-right:12px; }
    main { max-width:920px; margin:24px auto; padding:0 16px; }
    .card { background:#fff; border:1px solid #eee; border-radius: var(--radius); padding:20px; box-shadow:0 1px 2px rgba(0,0,0,.03); }
    .stack > * + * { margin-top: var(--gap); }
    label { display:block; font-weight:600; margin-bottom:6px; }
    input { width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:8px; font-size:16px; }
    button { display:inline-block; padding:10px 16px; border-radius:8px; border:1px solid #111; background:#111; color:#fff; cursor:pointer; font-weight:600; }
    button.secondary { background:#fff; color:#111; }
    .muted { color:#666; font-size:12px; }
    .error { color:#b00020; }
    .success { color:#0a8754; }
    table { width:100%; border-collapse: collapse; }
    th, td { text-align:left; padding:10px 8px; border-bottom:1px solid #eee; }
    .row { display:flex; gap:24px; align-items:flex-start; }
  </style>
  {{-- opcionálisan .env APP_URL-ból is kinyerhető, de a front végül origin-t használ --}}
  <script>
    window.LW = {
      base: window.location.origin, // pl. http://localhost
      api: function(path){ return `${this.base}/api/v1${path}`; },
      get token(){ return localStorage.getItem('token') || ''; },
      set token(v){ if(!v){ localStorage.removeItem('token'); } else { localStorage.setItem('token', v); } },
      headers(json=true){
        const h = { 'Authorization': `Bearer ${this.token}`, 'Accept': 'application/json' };
        if(json) h['Content-Type'] = 'application/json';
        return h;
      },
      async authFetch(url, options={}){
        const opts = Object.assign({ headers: this.headers(true) }, options);
        const res = await fetch(url, opts);
        let data = null;
        try { data = await res.json(); } catch(e){ /* no-op */ }
        if(!res.ok || (data && data.success === false)){
          const msg = (data && (data.message || (data.error && data.error.message))) || `HTTP ${res.status}`;
          throw new Error(msg);
        }
        return data ?? {};
      }
    }
  </script>
  @stack('head')
</head>
<body>
<header>
  <div>
    <a href="{{ route('auth.login') }}">Login</a>
    <a href="{{ route('auth.me') }}">Me</a>
  </div>
  <div>
    <button id="logoutBtn" class="secondary" style="display:none;">Logout</button>
  </div>
</header>
<main>
  @yield('content')
</main>

<script>
  // Logout gomb megjelenítése, ha van token
  const $logout = document.getElementById('logoutBtn');
  function syncLogoutVisibility(){ if(!$logout) return; $logout.style.display = (LW.token ? 'inline-block' : 'none'); }
  syncLogoutVisibility();
  if($logout){
    $logout.addEventListener('click', async () => {
      try {
        // Sanctum logout endpoint
        await LW.authFetch(LW.api('/auth/token').replace('/v1/v1','/v1'), { method: 'DELETE', headers: LW.headers(false) });
      } catch(e) {
        // ha lejárt tokennel hívtuk, így is töröljük
      } finally {
        LW.token = '';
        syncLogoutVisibility();
        window.location.href = "{{ route('auth.login') }}";
      }
    });
  }
</script>

@stack('scripts')
</body>
</html>
