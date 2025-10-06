@extends('layouts.lightwave')

@section('title', 'Profil szerkesztése')

@section('content')
  <div class="card">
    <h1>Profil szerkesztése</h1>
    <p class="muted">Az oldal az <code>/api/v1/dating-profiles/{{ $id }}</code> API-t hívja Sanctum tokennel.</p>

    <p>
      <a href="{{ route('profiles.show', $id) }}">&larr; Vissza a profilhoz</a>
      &nbsp;|&nbsp;
      <a href="{{ route('profiles.index') }}">Lista</a>
    </p>

    <form id="editForm" onsubmit="return false;" class="stack" style="max-width:720px;">
      <div class="row">
        <div style="flex:1">
          <label>Becenév *</label>
          <input id="nickname" required>
        </div>
        <div style="width:140px">
          <label>Magasság *</label>
          <input id="height_cm" type="number" min="100" max="250" required>
        </div>
        <div style="width:140px">
          <label>Testsúly *</label>
          <input id="weight_kg" type="number" min="30" max="250" required>
        </div>
      </div>

      <div class="row">
        <div style="flex:1">
          <label>Testalkat *</label>
          <select id="body_type" required>
            <option value="slim">slim</option>
            <option value="average">average</option>
            <option value="athletic">athletic</option>
            <option value="curvy">curvy</option>
            <option value="plus">plus</option>
          </select>
        </div>
        <div style="flex:1">
          <label>Hajszín *</label>
          <select id="hair_color" required>
            <option value="black">black</option>
            <option value="brown">brown</option>
            <option value="blonde">blonde</option>
            <option value="red">red</option>
            <option value="grey">grey</option>
            <option value="other">other</option>
          </select>
        </div>
        <div style="flex:1">
          <label>Szexuális beállítottság *</label>
          <select id="sexual_orientation" required>
            <option value="hetero">hetero</option>
            <option value="homo">homo</option>
            <option value="bi">bi</option>
            <option value="asexual">asexual</option>
            <option value="other">other</option>
          </select>
        </div>
      </div>

      <div class="row">
        <div style="flex:1">
          <label>Családi állapot *</label>
          <select id="marital_status" required>
            <option value="single">single</option>
            <option value="relationship">relationship</option>
            <option value="married">married</option>
            <option value="divorced">divorced</option>
            <option value="widowed">widowed</option>
          </select>
        </div>
        <div style="flex:1">
          <label>Végzettség *</label>
          <select id="education_level" required>
            <option value="primary">primary</option>
            <option value="secondary">secondary</option>
            <option value="vocational">vocational</option>
            <option value="college">college</option>
            <option value="bachelor">bachelor</option>
            <option value="master">master</option>
            <option value="phd">phd</option>
          </select>
        </div>
        <div style="flex:1">
          <label>Foglalkozás *</label>
          <input id="occupation" required>
        </div>
      </div>

      <div class="row">
        <div style="flex:1"><label>Ország *</label><input id="country" required></div>
        <div style="flex:1"><label>Megye *</label><input id="state" required></div>
        <div style="flex:1"><label>Város *</label><input id="city" required></div>
      </div>

      <div class="row">
        <div style="flex:1">
          <label>Regisztráció célja *</label>
          <select id="registration_purpose" required>
            <option value="dating">dating</option>
            <option value="friendship">friendship</option>
            <option value="serious">serious</option>
            <option value="casual">casual</option>
            <option value="networking">networking</option>
          </select>
        </div>
        <div style="flex:2">
          <label>Beszélt nyelvek (ID-k, vesszővel)</label>
          <input id="language_ids" placeholder="1,2,3">
        </div>
      </div>

      <div id="adminInfo" class="muted" style="display:none;"></div>

      <div class="row">
        <button id="saveBtn">Mentés</button>
        <a class="button secondary" href="{{ route('profiles.show', $id) }}" style="display:inline-block;padding:10px 16px;border:1px solid #111;border-radius:8px;background:#fff;text-decoration:none;">Mégse</a>
        <span id="formState" class="muted"></span>
      </div>
    </form>
  </div>
@endsection

@push('scripts')
<script>
(function(){
  if(!LW.token){ window.location.href = "{{ route('auth.login') }}"; return; }
  const id = {{ $id }};
  const $formState = document.getElementById('formState');

  function parseIds(s){
    return (s||'').split(',').map(x=>x.trim()).filter(Boolean).map(n=>Number(n)).filter(n=>Number.isInteger(n) && n>0);
  }

  function fillForm(p){
    nickname.value = p.nickname ?? '';
    height_cm.value = p.height_cm ?? '';
    weight_kg.value = p.weight_kg ?? '';
    body_type.value = p.body_type ?? 'average';
    hair_color.value = p.hair_color ?? 'brown';
    sexual_orientation.value = p.sexual_orientation ?? 'hetero';
    marital_status.value = p.marital_status ?? 'single';
    education_level.value = p.education_level ?? 'bachelor';
    occupation.value = p.occupation ?? '';
    country.value = p.country ?? '';
    state.value = p.state ?? '';
    city.value = p.city ?? '';
    registration_purpose.value = p.registration_purpose ?? 'dating';
    language_ids.value = (p.languages||[]).map(l=>l.id).join(',');
  }

  async function detectAdmin(){
    try{
      const data = await LW.authFetch(LW.api('/me').replace('/v1/v1','/v1'), {headers: LW.headers(false)});
      const me = data?.data?.user ?? data?.data ?? {};
      if(me && typeof me.is_admin !== 'undefined' && me.is_admin){
        document.getElementById('adminInfo').style.display = 'block';
        document.getElementById('adminInfo').textContent = `Admin módban szerkesztesz (User ID: ${ ( (window.profileUserId ?? '-') ) })`;
      }
    }catch{}
  }

  // betöltés
  (async function(){
    try{
      $formState.textContent = 'Betöltés...';
      const res = await LW.authFetch(LW.api(`/dating-profiles/${id}`).replace('/v1/v1','/v1'), {headers: LW.headers(false)});
      const p = res.data || {};
      window.profileUserId = p.user_id;
      fillForm(p);
      $formState.textContent = '';
      detectAdmin();
    }catch(e){
      $formState.innerHTML = `<span class="error">${e.message}</span>`;
      if((e.message||'').toLowerCase().includes('unauthenticated')){ LW.token=''; setTimeout(()=>location.href="{{ route('auth.login') }}", 800); }
    }
  })();

  // mentés
  document.getElementById('saveBtn').addEventListener('click', async ()=>{
    $formState.textContent = 'Mentés...';
    const payload = {
      nickname: nickname.value,
      height_cm: Number(height_cm.value),
      weight_kg: Number(weight_kg.value),
      body_type: body_type.value,
      hair_color: hair_color.value,
      sexual_orientation: sexual_orientation.value,
      marital_status: marital_status.value,
      education_level: education_level.value,
      occupation: occupation.value,
      country: country.value,
      state: state.value,
      city: city.value,
      registration_purpose: registration_purpose.value
    };
    const langs = parseIds(language_ids.value);
    if(langs.length) payload.language_ids = langs;

    try{
      const json = await LW.authFetch(LW.api(`/dating-profiles/${id}`).replace('/v1/v1','/v1'), {
        method: 'PUT',
        headers: LW.headers(true),
        body: JSON.stringify(payload)
      });
      $formState.innerHTML = '<span class="success">Sikeres mentés.</span>';
      setTimeout(()=> window.location.href = "{{ route('profiles.show', $id) }}", 500);
    }catch(e){
      $formState.innerHTML = `<span class="error">${e.message}</span>`;
    }
  });
})();
</script>
@endpush
