class WindYearModule {
  static id      = 'wind-year';
  static refresh = 600;

  render(data, el) {
    const w   = data.weather;
    const u   = w.wind_units ?? 'mph';
    const now = new Date();
    const mo  = now.toLocaleString('default', { month: 'short' });
    const yr  = now.getFullYear();

    const mCls = W34.windClass(w.windmmax, u);
    const yCls = W34.windClass(w.windymax, u);

    el.innerHTML = `
      <div class="mod mod-top">
        <div class="mod-header">
          <span class="mod-time">Peak Gusts</span>
        </div>
        <div class="mod-rows" style="margin-top:2px">
          <div class="mod-row">
            <span class="mod-label">${mo}</span>
            <span class="mod-md ${mCls}">${W34.fmt(w.windmmax, 0)}<span class="mod-unit"> ${u}</span></span>
            <span class="mod-label" style="font-size:.5rem">${w.windmmaxtime2 ?? ''}</span>
          </div>
          <div class="mod-row">
            <span class="mod-label">${yr}</span>
            <span class="mod-md ${yCls}">${W34.fmt(w.windymax, 0)}<span class="mod-unit"> ${u}</span></span>
            <span class="mod-label" style="font-size:.5rem">${w.windymaxtime2 ?? ''}</span>
          </div>
        </div>
      </div>`;
  }
}
W34.register(WindYearModule);
