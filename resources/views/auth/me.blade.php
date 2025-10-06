@extends('layouts.lightwave')

@section('title', 'Me – Lightwave')

@section('content')
  <div class="card stack">
    <h1>Saját profil (Me)</h1>
    <p id="status" class="muted">Betöltés...</p>

    <div id="meWrap" style="display:none;">
      <table>
        <tbody>
        <tr><th>ID</th><td id="u_id"></td></tr>
        <tr><th>Név</th><td id="u_name"></td></tr>
        <tr><th>E-mail</th><td id="u_email"></td></tr>
        <tr><th>Admin</th><td id="u_admin"></td></tr>
        </tbody>
      </table>
    </div>

    <div class="row">
      <button id="toDating" class="secondary" onclick="window.location.href='{{ url('/dating') }}'">Társkereső UI (opcionális)</button>
    </div>
  </div>
@endsection

@push('scripts')
<script>
  (async function(){
    if(!LW.token){
      window.location.href = "{{ route('auth.login') }}";
      return;
    }
    try {
      const url = LW.api('/me').replace('/v1/v1','/v1');
      const resp = await LW.authFetch(url, { method:'GET', headers: LW.headers(false) });

      // A backend itt ilyen: { success, data: { user: {...}, current_token: {...} }, message }
      // vagy lapos: { success, data: {id,name,email,...} }
      const payload = resp?.data ?? {};
      const user = payload.user ?? payload;            // <- EZ a lényeg
      const token = payload.current_token ?? null;

      document.getElementById('u_id').textContent = user.id ?? '-';
      document.getElementById('u_name').textContent = user.name ?? '-';
      document.getElementById('u_email').textContent = user.email ?? '-';
      document.getElementById('u_admin').textContent = (typeof user.is_admin !== 'undefined')
        ? (user.is_admin ? 'igen' : 'nem')
        : '—';

      // (opcionális) ha szeretnéd, itt kiírhatod a token nevét / id-ját:
      // console.log('Current token:', token);

      document.getElementById('status').textContent = '';
      document.getElementById('meWrap').style.display = 'block';
    } catch(e){
      document.getElementById('status').innerHTML = `<span class="error">${e.message}</span>`;
      if((e.message || '').toLowerCase().includes('unauthenticated')){
        LW.token = '';
        setTimeout(()=> window.location.href = "{{ route('auth.login') }}", 800);
      }
    }
  })();
</script>
@endpush
