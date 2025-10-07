@extends('layouts.lightwave')

@section('title', 'Profil adatlap')

@section('content')
  <div class="card">
    <h1>Profil adatlap</h1>
    <p class="muted">Az oldal az <code>/api/v1/dating-profiles/{{ $id }}?with=images</code> API-t hívja Sanctum tokennel.</p>

    <p>
      <a href="{{ route('profiles.index') }}">&larr; Vissza a listához</a>
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

  {{-- Képek kártya --}}
  <div class="card" style="margin-top:1rem;">
    <h2>Profil képek</h2>
    <p class="muted">API: <code>/api/v1/profile-images</code></p>

    {{-- Feltöltő űrlap --}}
    <form id="imageUploadForm" enctype="multipart/form-data" style="margin: .5rem 0 1rem 0;">
      @csrf
      <input type="hidden" name="dating_profile_id" id="dating_profile_id" value="{{ $id }}">

      <div style="display:flex; gap:1rem; align-items:flex-end; flex-wrap:wrap;">
        <div>
          <label for="image">Kép</label><br>
          <input type="file" name="image" id="image" accept="image/*" required>
        </div>

        <div>
          <label for="caption">Felirat</label><br>
          <input type="text" name="caption" id="caption" placeholder="Pl. Strand">
        </div>

        <div>
          <label for="sort_order">Sorrend</label><br>
          <input type="number" name="sort_order" id="sort_order" min="0" step="1">
        </div>

        <div>
          <label for="visibility">Láthatóság</label><br>
          <select name="visibility" id="visibility">
            <option value="public">Publikus</option>
            <option value="private">Privát</option>
          </select>
        </div>

        <div style="display:flex; align-items:center; gap:.4rem;">
          <input type="checkbox" name="is_primary" id="is_primary" value="1">
          <label for="is_primary">Legyen elsődleges</label>
        </div>

        <div>
          <button type="submit">Feltöltés</button>
        </div>
      </div>
    </form>

    <div id="flash" class="muted" style="display:none; padding:.5rem .75rem; border-radius:6px;"></div>

    {{-- Galéria --}}
    <div id="imagesGrid" style="display:grid; gap:1rem; grid-template-columns:repeat(auto-fill, minmax(160px,1fr));">
      <div style="opacity:.65;">Képek betöltése…</div>
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
  const id = {{ $id }};
  const $status = document.getElementById('status');

  try {
    $status.textContent = 'Betöltés…';
    const url = LW.api(`/dating-profiles/${id}?with=images`).replace('/v1/v1','/v1');
    const json = await LW.authFetch(url);
    const p = json.data || {};

    // profil mezők
    document.getElementById('p_id').textContent        = p.id ?? '-';
    document.getElementById('p_nickname').textContent  = p.nickname ?? '-';
    document.getElementById('p_body').textContent      = p.body_type ?? '-';
    document.getElementById('p_hair').textContent      = p.hair_color ?? '-';
    document.getElementById('p_orient').textContent    = p.sexual_orientation ?? '-';
    document.getElementById('p_marital').textContent   = p.marital_status ?? '-';
    document.getElementById('p_edu').textContent       = p.education_level ?? '-';
    document.getElementById('p_job').textContent       = p.occupation ?? '-';
    document.getElementById('p_country').textContent   = p.country ?? '-';
    document.getElementById('p_state').textContent     = p.state ?? '-';
    document.getElementById('p_city').textContent      = p.city ?? '-';
    document.getElementById('p_purpose').textContent   = p.registration_purpose ?? '-';
    document.getElementById('p_langs').textContent     = (p.languages || []).map(l => l.name).join(', ') || '—';

    renderImages(Array.isArray(p.images) ? p.images : []);

    $status.textContent = '';
  } catch(e){
    $status.innerHTML = `<span class="error">${e.message}</span>`;
    if((e.message || '').toLowerCase().includes('unauthenticated')){
      LW.token = '';
      setTimeout(()=> window.location.href = "{{ route('auth.login') }}", 800);
    }
  }

  // Feltöltés
  document.getElementById('imageUploadForm').addEventListener('submit', async (ev) => {
    ev.preventDefault();
    const form = ev.currentTarget;
    const fd = new FormData(form);

    try{
    
      const endpoint = LW.api('/profile-images').replace('/v1/v1','/v1');
const res = await fetch(endpoint, {
  method: 'POST',
  headers: {
    'Authorization': 'Bearer ' + LW.token,
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
  },
  body: fd,
  credentials: 'same-origin' // ha ugyanazon a domainen vagy
});
      const data = await safeJson(res);
      if(!res.ok){
        throw new Error(data?.message || 'Feltöltés sikertelen.');
      }
      flash('Kép sikeresen feltöltve.');
      form.reset();
      // profil újratöltése képekkel
      const url = LW.api(`/dating-profiles/${id}?with=images`).replace('/v1/v1','/v1');
      const json = await LW.authFetch(url);
      renderImages(Array.isArray(json.data?.images) ? json.data.images : []);
    }catch(err){
      flash(err.message || 'Feltöltés sikertelen.', false);
    }
  });

})();

/** ====== Galéria render + műveletek ====== **/
function renderImages(items){
  const grid = document.getElementById('imagesGrid');
  grid.innerHTML = '';
  if(!items.length){ grid.innerHTML = '<div style="opacity:.7;">Nincs még feltöltött kép ehhez a profilhoz.</div>'; return; }

  items.forEach(img => {
    const wrap = document.createElement('div');
    wrap.style.border = '1px solid #eee';
    wrap.style.borderRadius = '8px';
    wrap.style.padding = '8px';

    const badges = [];
    if (img.is_primary) badges.push('<span class="badge" style="background:#2b8a3e;">PRIMARY</span>');
    if (img.visibility === 'private') badges.push('<span class="badge" style="background:#6c757d; right:8px; left:auto;">PRIVÁT</span>');
    if (img.is_redacted) badges.push('<span class="badge" style="background:#6c757d;">REDAKTÁLT</span>');

    wrap.innerHTML = `
      <div style="position:relative; aspect-ratio:1/1; overflow:hidden; border-radius:6px; background:#f8f8f8;">
        <img src="${img.url ?? '#'}" alt="${escapeHtml(img.caption ?? '')}" style="width:100%; height:100%; object-fit:cover;">
        ${badges.join('')}
      </div>
      <div style="margin-top:.5rem; font-size:.9rem; color:#555;">
        ${img.caption ? ('Felirat: ' + escapeHtml(img.caption)) : (img.is_redacted ? 'Privát fotó' : '')}
      </div>

      <div style="display:flex; gap:.5rem; margin-top:.5rem; flex-wrap:wrap;">
        <button class="btn-make-primary" data-id="${img.id}">Legyen elsődleges</button>
        <button class="btn-delete" data-id="${img.id}" style="background:#c92a2a; color:#fff; border:1px solid #c92a2a;">Törlés</button>
      </div>

      <!-- Megosztás UI -->
      <div style="display:flex; gap:.5rem; margin-top:.5rem; flex-wrap:wrap;">
        <input type="number" placeholder="User ID" class="share-user-id" style="width:110px;">
        <button type="button" class="btn-share" data-image-id="${img.id}">Megosztás</button>
      </div>
    `;
    grid.appendChild(wrap);
  });

  // események
  grid.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', () => deleteImage(btn.dataset.id));
  });
  grid.querySelectorAll('.btn-make-primary').forEach(btn => {
    btn.addEventListener('click', () => makePrimary(btn.dataset.id));
  });
  grid.querySelectorAll('.btn-share').forEach(btn => {
    btn.addEventListener('click', async () => {
      const container = btn.closest('div'); // a megosztás blokk
      const input = container.querySelector('.share-user-id');
      const uid = parseInt(input.value, 10);
      if(!uid){ flash('Adj meg egy User ID-t!', false); return; }

      const res = await fetch(LW.api('/profile-image-shares').replace('/v1/v1','/v1'), {
        method: 'POST',
        headers: {
          'Authorization': 'Bearer ' + LW.token,
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          profile_image_id: btn.dataset.imageId,
          shared_with_user_id: uid
        })
      });
      const data = await safeJson(res);
      if(!res.ok){ flash(data?.message || 'Megosztás sikertelen.', false); return; }
      flash('Megosztva ✔');
    });
  });
}


/** ====== segédek ====== **/
function flash(msg, ok=true){
  const el = document.getElementById('flash');
  el.style.display = 'block';
  el.style.border = '1px solid ' + (ok ? '#3c763d' : '#a94442');
  el.style.color = ok ? '#3c763d' : '#a94442';
  el.textContent = msg;
  clearTimeout(el._t);
  el._t = setTimeout(()=>{ el.style.display='none' }, 3000);
}
function escapeHtml(s){
  return String(s).replace(/[&<>"']/g, (m) => ({
    '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'
  }[m]));
}
async function safeJson(res){
  try { return await res.json(); } catch(e){ return null; }
}

document.querySelectorAll('.btn-share').forEach(btn => {
  btn.addEventListener('click', async () => {
    const wrap = btn.closest('div');
    const uidInput = wrap.querySelector('.share-user-id');
    const userId = parseInt(uidInput.value,10);
    if(!userId){ alert('Add meg a felhasználó ID-t'); return; }

    const res = await fetch(LW.api('/profile-image-shares').replace('/v1/v1','/v1'), {
      method: 'POST',
      headers: {
        'Authorization': 'Bearer ' + LW.token,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        profile_image_id: btn.getAttribute('data-image-id'),
        shared_with_user_id: userId
      })
    });
    const data = await res.json();
    if(!res.ok){ alert(data.message || 'Megosztás sikertelen'); return; }
    alert('Megosztva ✔');
  });
});

</script>

<style>
.badge{position:absolute; top:8px; left:8px; background:#2b8a3e; color:#fff; font-size:12px; padding:2px 6px; border-radius:4px}
.badge + .badge{left:auto; right:8px}
</style>
@endpush
