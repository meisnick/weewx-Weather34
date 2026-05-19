class BarometerModule {
  static id      = 'barometer';
  static refresh = 300;

  render(data, el) {
    const w   = data.weather;
    const u   = w.barometer_units ?? 'hPa';
    const tr  = W34.trend(w.barometer_trend);
    const dp  = u === 'in' ? 2 : 1;

    el.innerHTML = `
      <div class="mod">
        <div class="mod-header">
          <span class="mod-time">${w.time ?? '--'}</span>
        </div>

        <div class="mod-primary">
          <span class="mod-xl mod-ok">${W34.fmt(w.barometer, dp)}</span>
          <span class="mod-unit">${u}</span>
        </div>

        <div class="mod-rows">
          <div class="mod-row">
            <span class="mod-label">Trend</span>
            <span class="mod-sm ${tr.cls}">${tr.sym} ${W34.fmt(Math.abs(w.barometer_trend ?? 0), dp)}<span class="mod-unit"> ${u}</span></span>
          </div>
          <div class="mod-row">
            <span class="mod-label">High</span>
            <span class="mod-sm">${W34.fmt(w.barometer_max, dp)}<span class="mod-unit"> ${u}</span></span>
          </div>
          <div class="mod-row">
            <span class="mod-label">Low</span>
            <span class="mod-sm">${W34.fmt(w.barometer_min, dp)}<span class="mod-unit"> ${u}</span></span>
          </div>
        </div>
      </div>`;
  }
}
W34.register(BarometerModule);
