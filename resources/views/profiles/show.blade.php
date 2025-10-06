@extends('layouts.lightwave')

@section('title', 'Profil adatlap')

@section('content')
  <div class="card">
    <h1>Profil adatlap</h1>
    <p class="muted">Az oldal az <code>/api/v1/dating-profiles/{{ $id }}</code> API-t hívja Sanctum tokennel.</p>

    <p><a href="{{ route('profiles.index') }}">&larr; Vissza a listához</a></p>

    <p>
  <a href="{{ route('profiles.index') }}">&larr; Lista</a>
  &nbsp;|&nbsp;
  <a href="{{ route('profiles.edit', $id) }}">✏️ Szerkesztés</a>
</p>


    <table id="profileTable" style="margin-top:1rem;">
      <tbody>
        <tr><th>ID</th><td id="p_id">-</td></tr>
        <tr><th>Becenév</th><td id="p_nickname">-</td></tr>
        <tr><th>Testalkat</th><td id="p_body">-</td></tr>
        <tr><th>Hajszín</th><td id="p_hair">-</td></tr>
        <tr><th>Szexuális beállítottság</th><td id="p_orient">-</td></tr>
        <tr><th>Családi állapot</th><td id="p_marital">-</td></tr>
        <tr><th>Végzettség</th><td id="p_edu">-</td></tr>
        <tr><th>Foglalkozás</th><td id="p_job">-</td></tr>
        <tr><th>Ország</th><td id="p_country">-</td></tr>
        <tr><th>Megye</th><td id="p_state">-</td></tr>
        <tr><th>Város</th><td id="p_city">-</td></tr>
        <tr><th>Regisztráció célja</th><td id="p_purpose">-</td></tr>
        <tr><th>Nyelvek</th><td id="p_langs">-</td></tr>
      </tbody>
    </table>
    <p id="status" class="muted"></p>
  </div>
@endsection

@push('scripts')
<script>
(async function(){
  if(!LW.token){
    window.location.href = "{{ route('auth.login') }}";
    return;
  }
  const id = {{ $id }};
  const $status = document.getElementById('status');

  try {
    $status.textContent = 'Betöltés...';
    const url = LW.api(`/dating-profiles/${id}`).replace('/v1/v1','/v1');
    const json = await LW.authFetch(url);
    const p = json.data || {};

    document.getElementById('p_id').textContent = p.id ?? '-';
    document.getElementById('p_nickname').textContent = p.nickname ?? '-';
    document.getElementById('p_body').textContent = p.body_type ?? '-';
    document.getElementById('p_hair').textContent = p.hair_color ?? '-';
    document.getElementById('p_orient').textContent = p.sexual_orientation ?? '-';
    document.getElementById('p_marital').textContent = p.marital_status ?? '-';
    document.getElementById('p_edu').textContent = p.education_level ?? '-';
    document.getElementById('p_job').textContent = p.occupation ?? '-';
    document.getElementById('p_country').textContent = p.country ?? '-';
    document.getElementById('p_state').textContent = p.state ?? '-';
    document.getElementById('p_city').textContent = p.city ?? '-';
    document.getElementById('p_purpose').textContent = p.registration_purpose ?? '-';
    document.getElementById('p_langs').textContent = (p.languages || []).map(l => l.name).join(', ') || '—';

    $status.textContent = '';
  } catch(e){
    $status.innerHTML = `<span class="error">${e.message}</span>`;
    if((e.message || '').toLowerCase().includes('unauthenticated')){
      LW.token = '';
      setTimeout(()=> window.location.href = "{{ route('auth.login') }}", 800);
    }
  }
})();
</script>
@endpush
