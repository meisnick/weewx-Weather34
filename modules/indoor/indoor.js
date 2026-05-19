class IndoorModule {
  static id      = 'indoor';
  static refresh = 300;

  render(data, el) {
    const w    = data.weather;
    const u    = w.temp_units ?? 'C';
    const cls  = W34.tempClass(w.temp_indoor, u);
    const hmTr = W34.trend(w.humidity_indoortrend ?? 0);

    el.innerHTML = `
      <div class="mod">
        <div class="mod-header">
          <span class="mod-time">${w.time ?? '--'}</span>
        </div>

        <div class="mod-primary">
          <span class="mod-xl ${cls}">${W34.fmt(w.temp_indoor)}°</span>
          <span class="mod-unit">${u}</span>
        </div>

        <div class="mod-rows">
          <div class="mod-row">
            <span class="mod-label">High / Low</span>
            <span class="mod-sm">
              <span class="mod-warm">${W34.fmt(w.temp_indoormax)}°</span>
              <span class="mod-muted-c"> / </span>
              <span class="mod-cold">${W34.fmt(w.temp_indoormin)}°</span>
            </span>
          </div>
          <div class="mod-row">
            <span class="mod-label">Humidity</span>
            <span class="mod-sm">
              ${W34.fmtInt(w.humidity_indoor)}<span class="mod-unit"> %</span>
              <span class="${hmTr.cls}" style="margin-left:4px">${hmTr.sym}</span>
            </span>
          </div>
          <div class="mod-row">
            <span class="mod-label">Feels like</span>
            <span class="mod-sm">${W34.fmt(w.temp_indoor_feel)}°<span class="mod-unit">${u}</span></span>
          </div>
        </div>
      </div>`;
  }
}
W34.register(IndoorModule);
