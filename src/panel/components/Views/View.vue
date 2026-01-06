<template>
  <k-panel-inside class="k-simplestats-view">
    <!-- Disclaimer -->
    <k-box v-if="!isLoading && !dismissDisclaimer" theme="text">
      <k-text size="small">
        <h3>{{ $t('simplestats.disclaimer.title') }}</h3>
        <p v-html="$t('simplestats.disclaimer.text')"></p>
        <kbd>{{ $t('simplestats.disclaimer.dismiss') }}</kbd>
      </k-text>
    </k-box>

    <k-header class="k-simplestats-view-header">
      {{ label }}

      <template #buttons>
        <k-simplestats-timespan
          ref="timespan"
          :dateChoices="timeframes"
          :time-period="timePeriod"
          :initial-view-periods="initialViewPeriods"
        />
      </template>
    </k-header>

    <k-tabs :tab="tab" :tabs="tabsWithLinks" />

    <k-simplestats-visits-view
      v-if="tab == tabs[0].name"
      :dateFrom="dateFrom"
      :dateTo="dateTo"
      path="pagestats"
    />

    <k-simplestats-devices-view
      v-else-if="tab == tabs[1].name"
      :dateFrom="dateFrom"
      :dateTo="dateTo"
      path="devicestats"
    />

    <k-simplestats-referrers-view
      v-else-if="tab == tabs[2].name"
      :dateFrom="dateFrom"
      :dateTo="dateTo"
      path="refererstats"
    />

    <k-simplestats-info-view
      v-else-if="tab == tabs[3].name"
    />

    <k-empty
      v-else
      layout="cards"
      :text="$t('simplestats.taberror')"
    />

    <!-- Invisible color getter util for CSS to JS style data transfer -->
    <div id="chart-default-color-getter"></div>
  </k-panel-inside>
</template>

<script>
import { usePanel } from "kirbyuse";

export default {
  props: {
    label: {
      type: String,
      default: "Simple Stats",
    },
    initialtab: {
      type: String,
      default: "pagevisits",
    },
    tabs: {
      type: Array,
      default: [],
    },
    timePeriod: {
      type: String,
      default: "Monthly",
    },
    timeframes: {
      type: Array,
      default: [],
    },
    initialViewPeriods: {
      type: Number,
      default: -1,
    },
  },

  data() {
    return {
      tab: '',
      dismissDisclaimer : false,
      isLoading : true,
    };
  },

  computed: {
    tabsWithLinks() {
      return this.tabs.map(tab => ({
        ...tab,
        click: () => this.onTab(tab.name),
      }));
    },

    dateFrom() {
      return this.$refs.timespan?.dateFrom;
    },

    dateTo() {
      return this.$refs.timespan?.dateTo;
    },
  },

  watch: {
    initialtab(val) {
      if (val) this.onTab(val);
    },
  },

  mounted(){
    this.onTab();
  },

  methods: {
    getStoredTab() {
      try {
        return localStorage.getItem("ss-tabs-menu");
      } catch {
        return null;
      }
    },

    storeTab(tab) {
      try {
        localStorage.setItem("ss-tabs-menu", tab);
      } catch {
        // ignore
      }
    },

    onTab(tab) {
      const validTabs = this.tabs.map(t => t.name);
      let resolved = tab || this.getStoredTab() || this.initialtab || validTabs[0];
      this.tab = validTabs.includes(resolved) ? resolved : validTabs[0];

      this.updateBreadcrumb();
      this.storeTab(this.tab);
    },

    updateBreadcrumb() {
      const panel = usePanel();
      const label = this.tabs.find(t => t.name === this.tab)?.label || this.tab;
      panel.view.breadcrumb[0].label = label;
      panel.view.breadcrumb[0].link = null;
    },
  },
};
</script>

<style>
/* K4 header fix */
.k-simplestats-view .k-header:has(+ .k-simplestats-view-header) {
  margin-bottom: 0;
}

.k-simplestats-view #chart-default-color-getter {
  display: none;
  color: light-dark(var(--color-dark), var(--color-light));
}

@container (max-width: 30rem) {
  .k-simplestats-view .k-header {
    flex-direction: column;
  }
}
</style>
