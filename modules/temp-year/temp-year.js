class TempYearModule {
  static id      = 'temp-year';
  static refresh = 600;

  render(data, el) {
    const w = data.weather;
    const u = w.temp_units ?? 'C';

    const minCls = W34.tempClass(w.tempymin, u);
    const maxCls = W34.tempClass(w.tempymax, u);
    const yr     = new Date().getFullYear();

    el.innerHTML = `
      <div class="mod mod-top">
        <div class="mod-header">
          <span class="mod-time">${yr} Extremes</span>
        </div>
        <div class="mod-rows" style="margin-top:2px">
          <div class="mod-row">
            <span class="mod-label">Min</span>
            <span class="mod-md ${minCls}">${W34.fmt(w.tempymin)}°<span class="mod-unit">${u}</span></span>
            <span class="mod-label" style="font-size:.5rem">${w.tempymintime2 ?? ''}</span>
          </div>
          <div class="mod-row">
            <span class="mod-label">Max</span>
            <span class="mod-md ${maxCls}">${W34.fmt(w.tempymax)}°<span class="mod-unit">${u}</span></span>
            <span class="mod-label" style="font-size:.5rem">${w.tempymaxtime2 ?? ''}</span>
          </div>
        </div>
      </div>`;
  }
}
W34.register(TempYearModule);
