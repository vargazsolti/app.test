@extends('layouts.lightwave')

@section('title', 'Társkereső profilok')

@section('content')
  <div class="card">
    <h1>Társkereső profilok</h1>
    <p class="muted">A lista az <code>/api/v1/dating-profiles</code> API endpointot hívja meg Sanctum tokennel.</p>

    <div class="row" style="margin-bottom:1rem;">
      <input id="searchCity" placeholder="Város szerint szűrés...">
      <button id="loadBtn">Listázás</button>
      <span id="state" class="muted"></span>
    </div>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Becenév</th>
          <th>Testalkat</th>
          <th>Hajszín</th>
          <th>Hely</th>
        </tr>
      </thead>
      <tbody id="profilesBody"></tbody>
    </table>
  </div>
@endsection

@push('scripts')
<script>
(async function(){
  if(!LW.token){
    window.location.href = "{{ route('auth.login') }}";
    return;
  }

  const $state = document.getElementById('state');
  const $tbody = document.getElementById('profilesBody');
  const $btn = document.getElementById('loadBtn');

  async function loadProfiles() {
    $state.textContent = 'Betöltés...';
    const city = document.getElementById('searchCity').value.trim();
    const qs = city ? `?city=${encodeURIComponent(city)}` : '';
    const url = LW.api(`/dating-profiles${qs}`).replace('/v1/v1','/v1');

    try {
      const json = await LW.authFetch(url);
      const items = json.data.data || json.data || [];

      if(!items.length) {
        $tbody.innerHTML = '<tr><td colspan="5" class="muted">Nincs találat</td></tr>';
      } else {
        $tbody.innerHTML = items.map(p => `
          <tr>
            <td>${p.id}</td>
            <td><a href="{{ url('/profiles') }}/${p.id}">${p.nickname}</a></td>
            <td>${p.body_type ?? '-'}</td>
            <td>${p.hair_color ?? '-'}</td>
            <td>${p.country ?? ''}, ${p.city ?? ''}</td>
          </tr>
        `).join('');
      }

      $state.textContent = `Találatok: ${items.length}`;
    } catch(e){
      $state.innerHTML = `<span class="error">${e.message}</span>`;
      if((e.message || '').toLowerCase().includes('unauthenticated')){
        LW.token = '';
        setTimeout(()=> window.location.href = "{{ route('auth.login') }}", 800);
      }
    }
  }

  $btn.addEventListener('click', loadProfiles);
  loadProfiles();
})();
</script>
@endpush
