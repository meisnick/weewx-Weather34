class SolarModule {
  static id      = 'solar';
  static refresh = 300;

  render(data, el) {
    const w      = data.weather;
    const solar  = parseFloat(w.solar ?? 0);
    const uv     = parseFloat(w.uv    ?? 0);
    const uvCls  = W34.uvClass(uv);

    el.innerHTML = `
      <div class="mod">
        <div class="mod-header">
          <span class="mod-time">${w.time ?? '--'}</span>
        </div>

        <div class="mod-primary">
          <span class="mod-xl mod-caution">${W34.fmt(solar, 0)}</span>
          <span class="mod-unit">W/m²</span>
        </div>

        <div class="mod-rows">
          <div class="mod-row">
            <span class="mod-label">UV Index</span>
            <span class="mod-sm ${uvCls}">${W34.fmt(uv, 1)}</span>
          </div>
          <div class="mod-row">
            <span class="mod-label">Solar high</span>
            <span class="mod-sm">${W34.fmt(w.solar_max ?? w.solar, 0)}<span class="mod-unit"> W/m²</span></span>
          </div>
        </div>
      </div>`;
  }
}
W34.register(SolarModule);
