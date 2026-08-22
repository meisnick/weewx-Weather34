# WORK ORDER: Highcharts Live Editor Integration (Sandbox)

## Problem Statement
The user is still unsatisfied with the Highcharts layouts, and the cycle of blindly tweaking code and refreshing the browser is inefficient. The existing visual module editor (`sandbox.php`) currently handles CSS layouts but lacks the ability to preview and manipulate Highcharts JavaScript configurations (margins, label offsets, legends) in real-time.

## Goal
Extend the `sandbox.php` visual editor to support real-time, WYSIWYG editing of Highcharts. This will allow the user to select a specific popout almanac or chart tab, use sliders/inputs to adjust layout properties, and immediately see the chart re-render inside the sandbox iframe.

## Instructions for External Agent

### Phase 1: Sandbox UI Controls for Highcharts
1. **Target File:** `sandbox.php`
2. **Action:** Create a new "Highcharts" control group in the editor UI (similar to the recent `flex`, `margin`, and `padding` additions).
3. **Controls Required:**
   - **Container:** Chart Container Height (adjusts `.grid1 > articlegraph { height: Xpx; }`)
   - **Spacing:** `chart.spacingTop`, `chart.spacingBottom`, `chart.spacingLeft`, `chart.spacingRight`
   - **Margins:** `chart.marginTop`, `chart.marginBottom`
   - **Legend:** `legend.margin`, `legend.y`
   - **X-Axis:** `xAxis.labels.y` (offset from gridline)
   - **Y-Axis:** `yAxis.labels.x` (offset from gridline)

### Phase 2: Live Rendering Mechanism (Iframe Communication)
1. **The Challenge:** Unlike CSS which can be injected via a `<style>` tag, Highcharts requires JavaScript execution (`chart.update()`) to re-render dynamically.
2. **Sandbox Side (`sandbox.php`):**
   - When a Highcharts control is adjusted, dispatch a `postMessage` to the preview iframe containing the property and value.
   - Example Payload: `{ type: 'HIGHCHARTS_UPDATE', config: { chart: { spacingTop: 30 } } }`
3. **Preview Side (`w34highcharts/scripts/plots.js` or global script):**
   - Add a `window.addEventListener('message', function(event) { ... })` listener.
   - If `event.data.type === 'HIGHCHARTS_UPDATE'`, iterate through all active charts on the page using `Highcharts.charts.forEach(chart => { if (chart) chart.update(event.data.config, true, true, false); })`.
   - This will instantly re-render the charts in the preview window without a page reload.

### Phase 3: Module & Popout Selection
1. **Target File:** `sandbox.php`
2. **Action:** Add a dropdown or sidebar menu to load specific chart views into the sandbox preview iframe.
   - E.g., "Wind Almanac (Popout)", "Lightning Strikes (Popout)", "Yearly Rain (Main Dashboard Tab)".
   - This ensures the user can test the exact chart they are unhappy with in its native context.

### Phase 4: Persistence Strategy
1. **Action:** Implement a save mechanism for these Highcharts overrides.
2. **Implementation:** 
   - When the user clicks "Save" in `sandbox.php`, the Highcharts configuration overrides should be serialized to a lightweight JSON file (e.g., `settings/highcharts_overrides.json`) OR written to a dedicated override JS file (`w34highcharts/scripts/sandbox-overrides.js`).
   - Modify `plots.js` (specifically `create_common_options`) to dynamically merge these saved overrides into the default chart configuration during initialization.

## Definition of Done
- `sandbox.php` features a dedicated Highcharts tab with inputs for margins, spacing, and label offsets.
- Adjusting these inputs sends a `postMessage` that instantly updates the chart in the iframe via `chart.update()`.
- The sandbox allows loading specific chart popouts (e.g., `pop_lightningalmanac.php`) to edit them in context.
- Saved Highcharts settings persist and are automatically applied when the actual dashboard or popouts are loaded in production.
