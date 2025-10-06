@extends('layouts.lightwave')

@section('title', 'Login – Lightwave')

@section('content')
  <div class="card stack">
    <h1>Bejelentkezés (Sanctum API)</h1>
    <p class="muted">Add meg az e-mailt és a jelszót. A sikeres bejelentkezéskor a rendszer elmenti a Bearer tokent a böngészőbe.</p>

    <div class="stack" style="max-width:420px;">
      <div>
        <label for="email">E-mail</label>
        <input id="email" type="email" placeholder="you@example.com" required>
      </div>
      <div>
        <label for="password">Jelszó</label>
        <input id="password" type="password" placeholder="••••••••" required>
      </div>
      <div class="row">
        <button id="loginBtn">Login</button>
        <span id="state" class="muted"></span>
      </div>
    </div>
  </div>

  <div class="card" style="margin-top:16px;">
    <p class="muted">Tipp: ha van <code>POST /api/v1/auth/token</code> endpoint, a válasz JSON szerkezete az alap projekt szerint: <code>{ success, data: { token }, message }</code>.</p>
  </div>
@endsection

@push('scripts')
<script>
  (function(){
    const $email = document.getElementById('email');
    const $password = document.getElementById('password');
    const $btn = document.getElementById('loginBtn');
    const $state = document.getElementById('state');

    // ha már van token, ugorjunk a /me oldalra
    if(LW.token){
      window.location.href = "{{ route('auth.me') }}";
      return;
    }

    $btn.addEventListener('click', async () => {
      $state.textContent = 'Bejelentkezés...';
      try {
        const url = LW.api('/auth/token').replace('/v1/v1','/v1');
        const res = await fetch(url, {
          method: 'POST',
          headers: { 'Accept':'application/json', 'Content-Type':'application/json' },
          body: JSON.stringify({
            email: $email.value,
            password: $password.value
          })
        });

        const json = await res.json();
        if(!res.ok || json.success === false){
          throw new Error(json.message || 'Hibás belépési adatok');
        }

        const token = (json.data && json.data.token) || json.token;
        if(!token) throw new Error('A válasz nem tartalmaz tokent.');

        LW.token = token;
        $state.innerHTML = '<span class="success">Sikeres bejelentkezés.</span>';
        setTimeout(() => window.location.href = "{{ route('auth.me') }}", 400);
      } catch(e) {
        $state.innerHTML = `<span class="error">${e.message}</span>`;
      }
    });
  })();
</script>
@endpush
