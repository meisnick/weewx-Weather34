# WORK ORDER: Highcharts Layout Refactor & Verification

## Problem Statement
The current approach to fixing Highcharts layout issues (X-axis overlaps, cutoff legends, range selector buttons inside the chart, and popup window overflows) has devolved into manual "whack-a-mole." Hardcoded pixel heights and margins in `plots.js` (e.g., `marginTop`, `marginBottom`, `yAxis[0].height`) are colliding with fixed CSS container constraints, causing unpredictable UI breakages across different chart types and viewport sizes.

## Goal
Implement a systematic, repeatable framework for Highcharts layouts that guarantees responsive, collision-free rendering for all charts (both full-size and almanac popouts) without relying on hardcoded pixel offsets. Establish a programmatic verification step to prevent regressions.

## Instructions for External Agent

### Phase 1: Clean Slate (Remove Hardcoded Offsets)
1. **Target File:** `w34highcharts/scripts/plots.js`
2. **Action:** Strip out all manual layout overrides in the chart-specific configuration functions (e.g., `setWindSmall`, `setStrikeSmall`, `setTempSmall`, `create_lightning_chart`, etc.).
   - Remove `options.yAxis[X].height = "..."`
   - Remove `options.chart.marginTop = ...`
   - Remove `options.chart.marginBottom = ...`
   - Remove `$("#"+plot_div).css("height", ...);`

### Phase 2: Implement the Responsive Framework
1. **Container-Driven Sizing:** Highcharts must rely entirely on its parent container for sizing. 
   - Ensure the `chart` object in `create_common_options()` is set to naturally fill the container (e.g., `height: null`, `width: null` so it defaults to `100%`).
   - The CSS class `.grid1 > articlegraph` in all `pop_*.php` files currently sets `height: 350px;`. This is the single source of truth. Highcharts must flex to fit within this space.
2. **Global Margins & Spacing:** Manage spacing globally in `create_common_options()`.
   - **Legend:** Define `legend: { enabled: true, margin: 25 }` globally so the key never touches the X-axis labels.
   - **Range Selector (Date Buttons):** If `rangeSelector` is enabled, Highcharts usually reserves space for it. If buttons are overlapping the plot area, adjust `chart.spacingTop` dynamically in `create_common_options()` based on the presence of the range selector, rather than hardcoding `marginTop` on a per-chart basis.
   - **X-Axis Labels:** Retain the `labels: { y: 20 }` configuration for `xAxis` to ensure date labels stay below the gridline.

### Phase 3: Automated Verification
1. **Requirement:** Do not submit the code until you have verified that no elements overlap.
2. **Action:** Write a short verification script (e.g., using Puppeteer, Playwright, or a simple browser console script you can inject during testing) that loops through all `pop_*.php` almanacs.
3. **Check:** The script must calculate the `getBoundingClientRect()` of:
   - The range selector group (`.highcharts-range-selector-group`)
   - The plot background (`.highcharts-plot-background`)
   - The legend (`.highcharts-legend`)
   - The X-axis labels (`.highcharts-axis-labels`)
4. **Assert:** Ensure that `rangeSelector.bottom < plotBackground.top` and `legend.top > xAxisLabels.bottom`. If any intersection is detected, the layout fails.

## Definition of Done
- `plots.js` is scrubbed of individual `marginTop`/`marginBottom`/`height` integer hacks.
- Date sorting buttons (range selectors) sit cleanly *above* the plot area on charts where they are enabled.
- X-axis labels sit cleanly *below* the plot gridlines.
- Legends sit cleanly *below* the X-axis labels without being cut off by the `articlegraph` border.
- The automated verification script passes for all almanac popouts.
