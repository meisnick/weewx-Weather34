class TemperatureModule {
  static id      = 'temperature';
  static refresh = 60;

  render(data, el) {
    const w    = data.weather;
    const u    = w.temp_units ?? 'C';
    const cls  = W34.tempClass(w.temp, u);
    const pill = W34.tempPillClass(w.apptemp ?? w.temp, u);
    const tr   = W34.trend(w.temp_trend);

    el.innerHTML = `
      <div class="mod">
        <div class="mod-header">
          <span class="mod-time">${w.time ?? '--'}</span>
        </div>

        <div class="mod-primary">
          <span class="mod-xl ${cls}">${W34.fmt(w.temp)}°</span>
          <span class="mod-unit">${u}</span>
          <span class="mod-sm ${tr.cls}" style="margin-left:4px">${tr.sym} ${W34.fmt(w.temp_trend, 1)}°</span>
        </div>

        <div class="mod-rows">
          <div class="mod-row">
            <span class="mod-label">High / Low</span>
            <span class="mod-sm">
              <span class="mod-warm">${W34.fmt(w.temp_today_high)}°</span>
              <span class="mod-muted-c"> / </span>
              <span class="mod-cold">${W34.fmt(w.temp_today_low)}°</span>
            </span>
          </div>
          <div class="mod-row">
            <span class="mod-label">Feels like</span>
            <span class="mod-sm mod-pill ${pill}" style="padding:1px 6px">
              ${W34.fmt(w.apptemp ?? w.heatindex ?? w.windchill)}°${u}
            </span>
          </div>
          <div class="mod-row">
            <span class="mod-label">Humidity</span>
            <span class="mod-sm">${W34.fmtInt(w.humidity)}<span class="mod-unit"> %</span></span>
          </div>
          <div class="mod-row">
            <span class="mod-label">Dewpoint</span>
            <span class="mod-sm">${W34.fmt(w.dewpoint)}°<span class="mod-unit">${u}</span></span>
          </div>
        </div>
      </div>`;
  }
}
W34.register(TemperatureModule);
