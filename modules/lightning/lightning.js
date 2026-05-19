class LightningModule {
  static id      = 'lightning';
  static refresh = 60;

  render(data, el) {
    const l     = data.lightning ?? {};
    const count = parseInt(l.strike_count_3hr ?? 0, 10);
    const dist  = parseFloat(l.light_last_distance ?? 0);
    const wu    = (data.config?.wind_units === 'mph') ? 'mi' : 'km';
    const distV = (data.config?.wind_units === 'mph')
      ? W34.fmt(dist * 0.621371, 1)
      : W34.fmt(dist, 1);
    const lastDate = l.last_time && parseInt(l.last_time) >= 1
      ? new Date(parseInt(l.last_time) * 1000).toLocaleDateString([], { day: 'numeric', month: 'short', year: 'numeric' })
      : null;

    el.innerHTML = `
      <div class="mod mod-top">
        <div class="mod-primary" style="margin-top:2px;gap:6px">
          <span class="mod-lg ${count > 0 ? 'mod-warm' : 'mod-muted-c'}">${count}</span>
          <span class="mod-unit">strikes</span>
          ${count > 0 ? '<img src="css/icons/lightning.svg" width="13" height="13">' : ''}
        </div>
        <div class="mod-label">
          <a href="pop_lightningalmanac.php" data-lity style="color:inherit;text-decoration:none">Last 3 hrs</a>
        </div>
        <div class="mod-rows" style="margin-top:3px">
          ${lastDate ? `
          <div class="mod-row">
            <span class="mod-label">Last</span>
            <span class="mod-sm mod-warm">${lastDate}</span>
          </div>` : ''}
          <div class="mod-row">
            <span class="mod-label">Distance</span>
            <span class="mod-sm mod-warm">${distV}<span class="mod-unit"> ${wu}</span></span>
          </div>
          <div class="mod-row">
            <span class="mod-label">All-time</span>
            <span class="mod-sm mod-warm">${W34.fmtInt(l.strike_count)}</span>
          </div>
        </div>
      </div>`;
  }
}
W34.register(LightningModule);
