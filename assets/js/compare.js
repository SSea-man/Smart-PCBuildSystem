/**
 * assets/js/compare.js — Side-by-side comparison logic
 */
'use strict';

document.addEventListener('DOMContentLoaded', () => {
  highlightWinners();
  initStickyHeader();
});

/**
 * Highlight the best value in each spec row.
 * Rows where higher = better: price (lower), benchmark, wattage (lower).
 */
function highlightWinners() {
  document.querySelectorAll('tr[data-compare-row]').forEach(row => {
    const metric = row.dataset.compareRow;   // 'higher' | 'lower'
    const cells  = Array.from(row.querySelectorAll('td[data-value]'));
    if (cells.length < 2) return;

    const vals = cells.map(c => parseFloat(c.dataset.value) || 0);
    const best = metric === 'lower' ? Math.min(...vals) : Math.max(...vals);
    const hasVariance = vals.some(v => v !== vals[0]);

    cells.forEach((cell, i) => {
      if (!hasVariance) return;
      if (vals[i] === best) {
        cell.classList.add('winner-cell');
      } else {
        cell.classList.add('loser-cell');
      }
    });
  });
}

function initStickyHeader() {
  const header = document.getElementById('compare-header-row');
  if (!header) return;
  const observer = new IntersectionObserver(
    ([entry]) => header.classList.toggle('shadow', !entry.isIntersecting),
    { threshold: 1.0, rootMargin: '-60px 0px 0px 0px' }
  );
  observer.observe(document.getElementById('compare-top-sentinel') || document.body);
}
