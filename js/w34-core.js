/**
 * W34 — core engine + all helpers
 * Loaded once. Modules call W34.register(ModClass) then W34.init() wires everything.
 */
const W34 = {

  _modules:   {},
  _cache:     null,
  _cacheTime: 0,
  _fetching:  null,

  register(Mod) {
    this._modules[Mod.id] = Mod;
  },

  async fetch() {
    const age = Date.now() - this._cacheTime;
    if (this._cache && age < 2000) return this._cache;
    if (this._fetching) return this._fetching;

    this._fetching = fetch('api.php')
      .then(r => r.json())
      .then(d => {
        this._cache     = d;
        this._cacheTime = Date.now();
        this._fetching  = null;
        return d;
      })
      .catch(() => { this._fetching = null; return this._cache; });

    return this._fetching;
  },

  init() {
    document.querySelectorAll('[data-module]').forEach(el => {
      const id  = el.dataset.module;
      const Mod = this._modules[id];
      if (!Mod) { console.warn('W34: unknown module:', id); return; }

      const mod = new Mod();
      const ms  = (parseInt(el.dataset.refresh, 10) || Mod.refresh || 60) * 1000;

      const tick = async () => {
        try {
          const data = await this.fetch();
          if (data) mod.render(data, el);
        } catch (e) { console.error('W34 render error:', id, e); }
      };

      tick();
      setInterval(tick, ms);
    });
  },

  // ── formatting ──────────────────────────────────────────────────────────

  fmt(val, dp = 1) {
    const v = parseFloat(val);
    return isNaN(v) ? '--' : v.toFixed(dp);
  },

  fmtInt(val) {
    const v = parseInt(val, 10);
    return isNaN(v) ? '--' : String(v);
  },

  // ── temperature ─────────────────────────────────────────────────────────

  toC(val, units) {
    const v = parseFloat(val);
    return (units === 'F') ? (v - 32) * 5 / 9 : v;
  },

  tempClass(val, units) {
    const c = this.toC(val, units);
    if (c <= 0)  return 'mod-cold';
    if (c <= 10) return 'mod-ok';
    if (c <= 28) return 'mod-warm';
    return 'mod-hot';
  },

  tempPillClass(val, units) {
    return this.tempClass(val, units).replace('mod-', 'mod-pill-');
  },

  // ── wind ────────────────────────────────────────────────────────────────

  toMs(val, unit) {
    const v = parseFloat(val);
    if (unit === 'mph')  return v * 0.44704;
    if (unit === 'km/h') return v * 0.27778;
    if (unit === 'kts' || unit === 'kn') return v * 0.51444;
    return v; // already m/s
  },

  windClass(val, unit) {
    const ms = this.toMs(val, unit);
    if (ms > 16.6) return 'mod-hot';
    if (ms > 11)   return 'mod-warm';
    if (ms > 8.3)  return 'mod-caution';
    if (ms > 2.7)  return 'mod-ok';
    return 'mod-cold';
  },

  windPillClass(val, unit) {
    return this.windClass(val, unit).replace('mod-', 'mod-pill-');
  },

  dir(deg) {
    const d = ['N','NNE','NE','ENE','E','ESE','SE','SSE',
                'S','SSW','SW','WSW','W','WNW','NW','NNW'];
    return d[Math.round(parseFloat(deg) / 22.5) % 16] ?? '--';
  },

  // ── trend ────────────────────────────────────────────────────────────────

  trend(val) {
    const v = parseFloat(val);
    if (v >  0.1) return { cls: 'mod-rising',  sym: '▲' };
    if (v < -0.1) return { cls: 'mod-falling', sym: '▼' };
    return            { cls: 'mod-steady',  sym: '—' };
  },

  // ── rain ────────────────────────────────────────────────────────────────

  rainClass(val) {
    const v = parseFloat(val);
    if (v <= 0)  return 'mod-muted-c';
    if (v < 10)  return 'mod-ok';
    if (v < 25)  return 'mod-cold';
    return 'mod-warm';
  },

  // ── UV ──────────────────────────────────────────────────────────────────

  uvClass(val) {
    const v = parseFloat(val);
    if (v < 3)  return 'mod-ok';
    if (v < 6)  return 'mod-caution';
    if (v < 8)  return 'mod-warm';
    return 'mod-hot';
  },

  // ── status ───────────────────────────────────────────────────────────────

  isOffline(data) {
    const dt = data?.weather?.datetime;
    return dt ? (Date.now() / 1000 - dt) > 300 : false;
  },

};
