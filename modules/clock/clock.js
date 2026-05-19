class ClockModule {
  static id      = 'clock';
  static refresh = 1;

  render(_, el) {
    const now  = new Date();
    const time = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false });
    const date = now.toLocaleDateString([], { weekday: 'short', day: 'numeric', month: 'short' });

    el.innerHTML = `
      <div class="mod mod-top" style="justify-content:center;align-items:center;gap:2px">
        <div style="text-align:center">
          <div class="mod-xl mod-warm" style="font-size:1.3rem;line-height:1">${time}</div>
          <div class="mod-label" style="margin-top:3px">${date}</div>
        </div>
      </div>`;
  }
}
W34.register(ClockModule);
