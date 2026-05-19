class RainfallModule {
  static id      = 'rainfall';
  static refresh = 60;

  render(data, el) {
    const w   = data.weather;
    const u   = w.rain_units ?? 'mm';
    const td  = parseFloat(w.rain_today    ?? 0);
    const rt  = parseFloat(w.rain_rate     ?? 0);
    const lh  = parseFloat(w.rain_lasthour ?? 0);
    const l24 = parseFloat(w.rain_24hrs    ?? 0);
    const yes = parseFloat(w.rain_yesterday ?? 0);

    const tdCls = W34.rainClass(td);
    const rtCls = rt > 0 ? 'mod-cold' : 'mod-muted-c';

    el.innerHTML = `
      <div class="mod">
        <div class="mod-header">
          <span class="mod-time">${w.time ?? '--'}</span>
        </div>

        <div class="mod-primary">
          <span class="mod-xl ${tdCls}">${W34.fmt(td)}</span>
          <span class="mod-unit">${u} today</span>
        </div>

        <div class="mod-rows">
          <div class="mod-row">
            <span class="mod-label">Rate</span>
            <span class="mod-sm ${rtCls}">${W34.fmt(rt, 2)}<span class="mod-unit"> ${u}/hr</span></span>
          </div>
          <div class="mod-row">
            <span class="mod-label">Last hour</span>
            <span class="mod-sm">${W34.fmt(lh)}<span class="mod-unit"> ${u}</span></span>
          </div>
          <div class="mod-row">
            <span class="mod-label">Last 24 hrs</span>
            <span class="mod-sm">${W34.fmt(l24)}<span class="mod-unit"> ${u}</span></span>
          </div>
          <div class="mod-row">
            <span class="mod-label">Yesterday</span>
            <span class="mod-sm">${W34.fmt(yes)}<span class="mod-unit"> ${u}</span></span>
          </div>
        </div>
      </div>`;
  }
}
W34.register(RainfallModule);
