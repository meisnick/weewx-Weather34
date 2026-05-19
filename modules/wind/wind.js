class WindModule {
  static id      = 'wind';
  static refresh = 4;

  render(data, el) {
    const w    = data.weather;
    const u    = w.wind_units ?? 'mph';
    const spd  = w.wind_speed      ?? w.wind_speed_avg ?? 0;
    const gust = w.wind_gust       ?? 0;
    const avg  = w.wind_speed_avg  ?? spd;
    const deg  = w.wind_direction  ?? 0;
    const cls  = W34.windClass(spd, u);
    const gCls = W34.windClass(gust, u);
    const card = W34.dir(deg);

    el.innerHTML = `
      <div class="mod">
        <div class="mod-header">
          <span class="mod-time">${w.time ?? '--'}</span>
        </div>

        <div class="mod-primary" style="gap:8px;align-items:flex-end">
          <span class="mod-xl ${cls}">${W34.fmt(spd, 0)}</span>
          <span class="mod-unit" style="margin-bottom:3px">${u}</span>
          <span class="mod-lg mod-muted-c" style="margin-left:6px">${card}</span>
        </div>

        <div class="mod-rows">
          <div class="mod-row">
            <span class="mod-label">Gust</span>
            <span class="mod-sm ${gCls}">${W34.fmt(gust, 0)}<span class="mod-unit"> ${u}</span></span>
          </div>
          <div class="mod-row">
            <span class="mod-label">Avg</span>
            <span class="mod-sm">${W34.fmt(avg, 0)}<span class="mod-unit"> ${u}</span></span>
          </div>
          <div class="mod-row">
            <span class="mod-label">Direction</span>
            <span class="mod-sm">${W34.fmtInt(deg)}<span class="mod-unit">°</span> ${card}</span>
          </div>
        </div>
      </div>`;
  }
}
W34.register(WindModule);
