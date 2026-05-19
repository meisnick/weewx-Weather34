class RainTotalsModule {
  static id      = 'rain-totals';
  static refresh = 60;

  render(data, el) {
    const w    = data.weather;
    const u    = w.rain_units ?? 'mm';
    const mon  = parseFloat(w.rain_month ?? 0);
    const yr   = parseFloat(w.rain_year  ?? 0);
    const fmtR = v => v >= 1000 ? Math.round(v).toString() : W34.fmt(v);
    const now  = new Date();
    const mo   = now.toLocaleString('default', { month: 'short' });
    const yr4  = now.getFullYear();

    el.innerHTML = `
      <div class="mod mod-top">
        <div class="mod-primary" style="gap:18px;margin-top:4px">
          <div>
            <div class="mod-label">${mo}</div>
            <span class="mod-lg mod-cold">${fmtR(mon)}</span>
            <span class="mod-unit">${u}</span>
            <div class="mod-label">Total</div>
          </div>
          <div>
            <div class="mod-label">${yr4}</div>
            <span class="mod-lg mod-cold">${fmtR(yr)}</span>
            <span class="mod-unit">${u}</span>
            <div class="mod-label">Total</div>
          </div>
        </div>
      </div>`;
  }
}
W34.register(RainTotalsModule);
