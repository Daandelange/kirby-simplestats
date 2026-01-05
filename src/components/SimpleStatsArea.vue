<template>
  <k-panel-inside class="k-simplestats-view">
    <!-- DISCLAIMER -->
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
        <TimeFrameInput
          ref="timeFrame"
          :dateChoices="timeframes"
          :time-period="timePeriod"
          :initial-view-periods="initialViewPeriods"
        />
      </template>
    </k-header>

    <k-tabs :tab="tab" :tabs="tabsWithLinks" />

    <PageStats
      v-if="tab == tabs[0].name"
      :dateFrom="dateFrom"
      :dateTo="dateTo"
      section-name="pagestats"
    />

    <Devices
      v-else-if="tab == tabs[1].name"
      :dateFrom="dateFrom"
      :dateTo="dateTo"
      section-name="devicestats"
    />

    <Referers
      v-else-if="tab == tabs[2].name"
      :dateFrom="dateFrom"
      :dateTo="dateTo"
      section-name="refererstats"
    />

    <k-grid v-else-if="tab == tabs[3].name" variant="columns">
      <!-- CONFIGURATION -->
      <k-column width="1/2">
        <Configuration section-name="configinfo" />
      </k-column>

      <!-- DB INFORMATION -->
      <k-column width="1/2">
        <DbInformation section-name="listdbinfo" />
      </k-column>

      <!-- TRACKING TESTER -->
      <k-column width="1/1">
        <k-line-field />
        <TrackingTester />
      </k-column>

      <!-- VISITORS TABLE -->
      <k-column width="1/1">
        <k-line-field />
        <Visitors section-name="listvisitors" />
      </k-column>
    </k-grid>

    <k-empty v-else layout="cards" :text="$t('simplestats.taberror')" />

    <!-- Invisible color getter util for CSS to JS style data transfer -->
    <div id="chart-default-color-getter"></div>
  </k-panel-inside>
</template>

<script>
import Visitors from "./Sections/Visitors.vue";
import PageStats from "./Sections/PageStats.vue";
import Devices from "./Sections/Devices.vue";
import Referers from "./Sections/Referers.vue";
import DbInformation from "./Sections/DbInformation.vue";
import Configuration from "./Sections/Configuration.vue";
import TrackingTester from "./Sections/TrackingTester.vue";
import TimeFrameInput from "./Ui/TimeFrameInput.vue";

import { usePanel } from "kirbyuse";

export default {
  components: {
    Visitors,
    PageStats,
    Devices,
    Referers,
    DbInformation,
    Configuration,
    TrackingTester,
    TimeFrameInput,
  },

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
      return this.$refs.timeFrame?.dateFrom;
    },

    dateTo() {
      return this.$refs.timeFrame?.dateTo;
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
