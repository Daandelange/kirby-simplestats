<template>
  <k-section :label="label">
    <k-grid variant="columns">
      <!-- Loading message -->
      <k-column v-if="isLoading" width="1/1">
        <k-empty>
          <k-icon type="loader"/>
          <span>Fetching data...</span>
        </k-empty>
      </k-column>

      <!-- Total Visits -->
      <k-column v-if="showTotals && !isLoading" width="1/1">
        <div class="k-stats">
          <k-stat
            :label="$t('simplestats.visits.visitsovertime')"
            :value="totalHits"
            :info="Math.round(averageHits) + ' ' + $t('simplestats.visits.per') + ' ' + timespanUnitName"
            icon="users"
            link="simplestats"
          />
          <k-stat
            v-if="showFullInfo"
            label="Samples"
            :value="trackingPeriods"
            :info="$t('simplestats.visits.since') + ' ' + trackedSince"
            icon="chart"
            link="simplestats"
          />
        </div>
      </k-column>

      <!-- Visits Over Time -->
      <k-column v-if="showTimeline && !isLoading" width="1/1">
        <k-simplestats-chart
          type="Line"
          :download="niceDownloadName('PageVisitsOverTime')"
          :label="$t('simplestats.visits.visitsovertime')"
          :chart-data="languagesOverTime"
          :chart-labels="chartPeriodLabels"
          :stacked="languagesAreEnabled"
          :auto-colorize="true"
          :x-time-axis="true"
          :y-visits-axis="true"
          :height="chartHeight('timeline')"
        />
      </k-column>

      <!-- Visits Per Language -->
      <k-column v-if="languagesAreEnabled && showLanguages && !isLoading" width="1/1">
        <k-simplestats-chart
          type="Pie"
          :chart-data="languageTotalHits"
          :chart-labels="chartLanguagesLabels"
          :chart-options="chartOptions"
          :auto-colorize="true"
          :fill="true"
          :show-legend="languagesAreEnabled"
          :height="chartHeight('languages')"
          :download="niceDownloadName('GlobalLanguageVisits')"
          :label="$t('simplestats.visits.globallanguages')"
        />
      </k-column>
    </k-grid>
  </k-section>
</template>

<script>
export default {
  inheritAttrs: false,

  props: {
    label: String,
    showFullInfo: Boolean,
    showTotals: {
      type: Boolean,
      default: true
    },
    showTimeline: Boolean,
    showLanguages: Boolean,
    size: {
      type: String,
      default: 'medium'
    },
    statsdata: Object,
    uid: String,
  },

  computed: {
    isLoading() {
      return !this.statsdata;
    },

    languagesOverTime() {
      return this.statsdata?.languagesOverTime ?? [];
    },

    chartPeriodLabels() {
      return this.statsdata?.chartperiodlabels ?? [];
    },

    chartLanguagesLabels() {
      return this.statsdata?.chartlanguageslabels ?? [];
    },

    languageTotalHits() {
      return this.statsdata?.languageTotalHits ?? [];
    },

    trackedSince() {
      return this.statsdata?.firstVisited ?? '[unknown]';
    },

    lastVisited() {
      return this.statsdata?.lastVisited ?? '[unknown]';
    },

    totalHits() {
      return this.statsdata?.totalHits ?? 0;
    },

    averageHits() {
      return this.statsdata?.averageHits ?? 0;
    },

    timespanUnitName() {
      return this.statsdata?.timespanUnitName ?? '[unknown]';
    },

    trackingPeriods() {
      return this.statsdata?.trackingPeriods ?? 0;
    },

    normalizedSize() {
      if (['small', 'compact'].includes(this.size)) return 'small';
      if (['large', 'huge'].includes(this.size)) return 'large';
      if (this.size === 'tiny') return 'tiny';
      return 'medium';
    },

    languagesAreEnabled() {
      return this.chartLanguagesLabels.length > 1;
    }
  },

  methods: {
    chartHeight(type) {
      const heights = {
        timeline: { tiny: 120, small: 240, medium: 260, large: 280 },
        languages: { tiny: 80, small: 185, medium: 205, large: 225 }
      };

      return heights[type][this.normalizedSize] ?? heights[type].medium;
    },
    niceDownloadName(statsName){
      if(this.uid?.length){
        let uid = String(this.uid);
        uid.replace('/', '_');
        return "Page_" + uid + "_" + statsName + ".png";
      }

      return "Site_" + statsName + ".png";
    }
  }
};
</script>
