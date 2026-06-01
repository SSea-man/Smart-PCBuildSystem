'use strict';

let debounceTimer = null;
const selectedIds = {};
const DEBOUNCE_MS = 500;
let budgetChart = null;

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.component-select').forEach(sel => {
    sel.addEventListener('change', onSelectChange);
  });
  
  initBudgetChart();

  const saveBtn = document.getElementById('save-custom-btn');
  if (saveBtn) {
    saveBtn.addEventListener('click', async () => {
      const ids = [];
      document.querySelectorAll('.component-select').forEach(sel => {
        const val = parseInt(sel.value) || null;
        if (val) ids.push(val);
      });
      if (ids.length === 0) {
        showToast('Please select at least one component', 'warning');
        return;
      }
      
      let total = 0;
      let tdp = 0;
      document.querySelectorAll('.component-select').forEach(sel => {
        const opt = sel.options[sel.selectedIndex];
        if (!opt || !opt.value) return;
        total += parseFloat(opt.dataset.price || 0);
        tdp += parseInt(opt.dataset.tdp || 0);
      });

      saveBtn.disabled = true;
      saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving…';
      try {
        const data = await apiFetch('/api/save_build.php', {
          components: ids,
          total_bdt: total,
          name: 'Custom Build (' + new Date().toLocaleDateString() + ')',
          purpose: 'custom',
          wattage: tdp
        });
        if (data.success) {
          saveBtn.classList.replace('btn-accent', 'btn-success');
          saveBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Saved!';
          showToast('Custom build saved to dashboard!', 'success');
        } else {
          throw new Error(data.error || 'Failed');
        }
      } catch (err) {
        saveBtn.disabled = false;
        saveBtn.innerHTML = '<i class="bi bi-bookmark-plus me-1"></i>Save Build';
        showToast('Could not save build: ' + err.message, 'danger');
      }
    });
  }

  const exportBtn = document.getElementById('export-build-btn');
  if (exportBtn) {
    exportBtn.addEventListener('click', generateExportData);
  }

  const copyBtn = document.getElementById('copy-export-btn');
  if (copyBtn) {
    copyBtn.addEventListener('click', copyExportToClipboard);
  }

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

function initBudgetChart() {
  const ctx = document.getElementById('budgetChart');
  if (!ctx) return;

  budgetChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: [],
      datasets: [{
        data: [],
        backgroundColor: [
          '#38bdf8', '#34d399', '#f43f5e', '#fbbf24', 
          '#a78bfa', '#f472b6', '#2dd4bf', '#fb7185',
          '#60a5fa', '#f59e0b', '#ec4899'
        ],
        borderWidth: 0
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: (ctx) => ` ৳${ctx.raw.toLocaleString('en-BD')}`
          }
        }
      },
      cutout: '70%'
    }
  });
}

function updateSummary() {
  let total = 0;
  let tdp = 0;
  let psuWattage = 0;
  const chartData = {};

  document.querySelectorAll('.component-select').forEach(sel => {
    const opt = sel.options[sel.selectedIndex];
    if (!opt || !opt.value) return;
    const price = parseFloat(opt.dataset.price || 0);
    const comp_tdp = parseInt(opt.dataset.tdp || 0);
    const comp_psu = parseInt(opt.dataset.psuWattage || 0);
    
    total += price;
    tdp += comp_tdp;
    if (comp_psu > 0) psuWattage = comp_psu;

    if (price > 0) {
      chartData[sel.dataset.category] = price;
    }
  });

  const totalEl = document.getElementById('builder-total');
  if (totalEl) totalEl.textContent = '৳' + total.toLocaleString('en-BD');

  const tdpEl = document.getElementById('builder-tdp');
  if (tdpEl) tdpEl.textContent = tdp + 'W';

  const minPsu = Math.ceil((tdp * 1.20) / 50) * 50;
  const psuEl = document.getElementById('builder-min-psu');
  if (psuEl) psuEl.textContent = minPsu + 'W recommended';

  if (budgetChart) {
    budgetChart.data.labels = Object.keys(chartData);
    budgetChart.data.datasets[0].data = Object.values(chartData);
    budgetChart.update();
  }

  const psuLabel = document.getElementById('psu-eff-label');
  const psuBar = document.getElementById('psu-eff-bar');
  if (psuLabel && psuBar) {
    if (psuWattage > 0 && tdp > 0) {
      const pct = Math.min(Math.round((tdp / psuWattage) * 100), 100);
      let status = '';
      let colorClass = '';

      if (pct >= 40 && pct <= 60) {
        status = `${pct}% [Optimal Efficiency Zone]`;
        colorClass = 'bg-success';
      } else if (pct < 80) {
        status = `${pct}% [Safe Zone]`;
        colorClass = 'bg-info';
      } else {
        status = `${pct}% [Danger Zone - Upgrade PSU!]`;
        colorClass = 'bg-danger';
      }

      psuLabel.textContent = status;
      psuBar.style.width = pct + '%';
      psuBar.className = 'progress-bar ' + colorClass;
    } else {
      psuLabel.textContent = '— Select PSU & components —';
      psuBar.style.width = '0%';
      psuBar.className = 'progress-bar bg-secondary';
    }
  }

  const printBody = document.getElementById('print-table-body');
  if (printBody) {
    printBody.innerHTML = '';
    document.querySelectorAll('.component-select').forEach(sel => {
      const cat = sel.dataset.category;
      const opt = sel.options[sel.selectedIndex];
      const name = (opt && opt.value) ? opt.text.split(' — ')[0].trim() : '— Not Selected —';
      const priceText = (opt && opt.value) ? '৳' + parseFloat(opt.dataset.price || 0).toLocaleString('en-BD') : '—';
      
      const tr = document.createElement('tr');
      tr.innerHTML = `<td><strong>${cat}</strong></td><td>${name}</td><td class="text-end">${priceText}</td>`;
      printBody.appendChild(tr);
    });

    const printTdp = document.getElementById('print-tdp-val');
    if (printTdp) printTdp.textContent = tdp + 'W';

    const printPsu = document.getElementById('print-psu-val');
    if (printPsu) printPsu.textContent = minPsu + 'W';

    const printTotal = document.getElementById('print-total-val');
    if (printTotal) printTotal.textContent = '৳' + total.toLocaleString('en-BD');

    const printDate = document.getElementById('print-date');
    if (printDate) printDate.textContent = new Date().toLocaleString();
  }
}

function generateExportData() {
  let md = `### 💻 PCBuilder BD Custom Build\n\n`;
  md += `| Category | Component Selection | Price |\n`;
  md += `| :--- | :--- | :--- |\n`;

  let plain = `PCBUILDER BD CUSTOM PC BUILD\n`;
  plain += `====================================\n`;

  let total = 0;
  let tdp = 0;

  document.querySelectorAll('.component-select').forEach(sel => {
    const cat = sel.dataset.category;
    const opt = sel.options[sel.selectedIndex];
    const name = (opt && opt.value) ? opt.text.split(' — ')[0].trim() : 'Not Selected';
    const priceVal = (opt && opt.value) ? parseFloat(opt.dataset.price || 0) : 0;
    const priceText = priceVal > 0 ? `৳${priceVal.toLocaleString('en-BD')}` : '—';
    const comp_tdp = (opt && opt.value) ? parseInt(opt.dataset.tdp || 0) : 0;

    total += priceVal;
    tdp += comp_tdp;

    md += `| **${cat}** | ${name} | ${priceText} |\n`;
    plain += `${cat.padEnd(15)} : ${name} (${priceText})\n`;
  });

  md += `\n**Total Estimated Price: ৳${total.toLocaleString('en-BD')}**\n`;
  md += `*Estimated System TDP: ${tdp}W*\n`;

  plain += `====================================\n`;
  plain += `Total Estimated Price: ৳${total.toLocaleString('en-BD')}\n`;
  plain += `Estimated System TDP: ${tdp}W\n`;

  const mdText = document.getElementById('export-markdown-text');
  if (mdText) mdText.value = md;

  const plainText = document.getElementById('export-plain-text');
  if (plainText) plainText.value = plain;
}

function copyExportToClipboard() {
  const activeTab = document.querySelector('#exportTab .nav-link.active');
  let textareaId = 'export-markdown-text';
  if (activeTab && activeTab.id === 'text-tab') {
    textareaId = 'export-plain-text';
  }

  const el = document.getElementById(textareaId);
  if (el) {
    el.select();
    el.setSelectionRange(0, 99999);
    try {
      navigator.clipboard.writeText(el.value);
      showToast('Copied to clipboard successfully!', 'success');
    } catch {
      document.execCommand('copy');
      showToast('Copied to clipboard successfully!', 'success');
    }
  }
}
