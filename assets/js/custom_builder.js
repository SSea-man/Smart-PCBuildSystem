/**
 * assets/js/custom_builder.js — Live compatibility checker for custom builder
 */
'use strict';

let debounceTimer = null;
const selectedIds = {};    // category -> component_id
const DEBOUNCE_MS = 500;

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.component-select').forEach(sel => {
    sel.addEventListener('change', onSelectChange);
  });
  updateSummary();
});

function onSelectChange(e) {
  const cat = e.target.dataset.category;
  const val = parseInt(e.target.value) || null;
  selectedIds[cat] = val;

  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => {
    checkCompatibility();
    updateSummary();
  }, DEBOUNCE_MS);
}

async function checkCompatibility() {
  const ids = Object.fromEntries(
    Object.entries(selectedIds).filter(([, v]) => v !== null)
  );
  if (Object.keys(ids).length < 2) {
    setCompatResult(null);
    return;
  }

  const box = document.getElementById('compat-result');
  if (box) {
    box.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Checking compatibility…';
    box.className = 'alert alert-info';
  }

  try {
    const data = await apiFetch('/api/check_compatibility.php', { component_ids: ids });
    setCompatResult(data);
  } catch {
    if (box) { box.textContent = 'Compatibility check failed.'; box.className = 'alert alert-warning'; }
  }
}

function setCompatResult(data) {
  const box = document.getElementById('compat-result');
  if (!box) return;
  if (!data) { box.className = 'd-none'; return; }

  if (data.pass) {
    box.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>All selected components are compatible!';
    box.className = 'alert alert-success';
  } else {
    const errs = (data.errors || []).map(e => `<li>${e}</li>`).join('');
    box.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Compatibility issues:</strong><ul class="mb-0 mt-1">${errs}</ul>`;
    box.className = 'alert alert-danger';
  }
}

function updateSummary() {
  let total = 0;
  let tdp   = 0;

  document.querySelectorAll('.component-select').forEach(sel => {
    const opt = sel.options[sel.selectedIndex];
    if (!opt || !opt.value) return;
    const price = parseFloat(opt.dataset.price || 0);
    const comp_tdp = parseInt(opt.dataset.tdp || 0);
    total += price;
    tdp   += comp_tdp;
  });

  const totalEl = document.getElementById('builder-total');
  if (totalEl) totalEl.textContent = '৳' + total.toLocaleString('en-BD');

  const tdpEl = document.getElementById('builder-tdp');
  if (tdpEl) tdpEl.textContent = tdp + 'W';

  const minPsu = Math.ceil((tdp * 1.20) / 50) * 50;
  const psuEl  = document.getElementById('builder-min-psu');
  if (psuEl) psuEl.textContent = minPsu + 'W recommended';
}
