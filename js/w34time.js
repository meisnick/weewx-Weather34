// weewx-Weather34 fork maintained by meisnick
// w34StationNow(tz, offsetHours): a Date whose local getters (getHours, getDate, ...) read the station's wall-clock
// time. Uses the station's IANA zone, so it follows daylight-saving changes while the page stays open; the fixed
// offsetHours is only a fallback for browsers without Intl time zone support.
if (!window.w34StationNow) {
    window.w34StationNow = (function () {
        var fmt = {};
        return function (tz, offsetHours) {
            var now = new Date();
            try {
                if (!fmt[tz]) fmt[tz] = new Intl.DateTimeFormat('en-US', {timeZone: tz, hourCycle: 'h23',
                    year: 'numeric', month: 'numeric', day: 'numeric', hour: 'numeric', minute: 'numeric', second: 'numeric'});
                var p = {};
                fmt[tz].formatToParts(now).forEach(function (x) { p[x.type] = x.value; });
                return new Date(+p.year, p.month - 1, +p.day, +p.hour % 24, +p.minute, +p.second, now.getMilliseconds());
            } catch (e) {
                return new Date(now.getTime() + (offsetHours * 60 + now.getTimezoneOffset()) * 60000);
            }
        };
    })();
}
