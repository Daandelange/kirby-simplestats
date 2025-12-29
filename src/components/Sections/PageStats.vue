<template>
  <k-grid variant="columns" style="column-gap: var(--spacing-8)">
    <!-- TODO: Add more stats: Text stats (total unique visits total and per lang, most popular pages, etc) - Pie chart of page-by-page popularity - -->
    <k-column width="1/1">
      <AreaChart
        type="Bar"
        height="150"
        download="Site_Visits.png"
        :label="$t('simplestats.visits.visitsovertime')"
        :chart-data="charts.visitsOverTime.data"
        :chart-labels="charts.visitsOverTime.labels"
        :x-title="$t('simplestats.charts.time')"
        :y-title="$t('simplestats.charts.visits')"
        :x-time-axis="true"
        :y-visits-axis="true"
      />
    </k-column>

    <k-column width="1/1">
      <AreaChart
        height="450"
        download="Site_PageVisits.png"
        :label="$t('simplestats.visits.pagevisitsovertime')"
        :chart-data="pageVisitsOverTimeDataSorted"
        :chart-labels="charts.pageVisitsOverTime.labels"
        :x-title="$t('simplestats.charts.time')"
        :y-title="$t('simplestats.charts.visits')"
        :stacked="true"
        :auto-colorize="true"
        :x-time-axis="true"
        :y-visits-axis="true"
      />
    </k-column>

    <k-column width="2/3" v-if="languagesAreEnabled">
      <AreaChart
        height="250"
        download="Site_LanguagesOverTime.png"
        :label="$t('simplestats.visits.languagesovertime')"
        :chart-data="charts.languagesOverTime.data"
        :chart-labels="charts.languagesOverTime.labels"
        :x-title="$t('simplestats.charts.time')"
        :y-title="$t('simplestats.charts.visits')"
        :stacked="true"
        :auto-colorize="true"
        :x-time-axis="true"
        :y-visits-axis="true"
      />
    </k-column>

    <k-column width="1/3" v-if="languagesAreEnabled">
      <AreaChart
        type="Pie"
        height="242"
        download="Site_LanguagePopularity.png"
        :label="$t('simplestats.visits.globallanguages')"
        :chart-data="charts.globalLanguages.data"
        :chart-labels="charts.globalLanguages.labels"
        :auto-colorize="true"
      />
    </k-column>

    <k-column>
      <SearchableTable
        :label="$t('simplestats.visits.visitedpages')"
        :rows="table.rows"
        :columns="table.columns"
      />
    </k-column>
  </k-grid>
</template>

<script>
import AreaChart from '../Ui/AreaChart.vue';
import SearchableTable from '../Ui/SearchableTable.vue';
import SectionBase from '../Sections/SimpleStatsSectionBase.vue';

export default {
  extends: SectionBase,

  components: {
    SearchableTable,
    AreaChart,
  },

  data() {
    return {
      charts: {
        visitsOverTime: {
          data: [],
          labels: [],
        },
        pageVisitsOverTime: {
          data: [],
          labels: [],
        },
        languagesOverTime: {
          data: [],
          labels: [],
        },
        globalLanguages: {
          data: [],
          labels: [],
        },
      },

      table: {
        rows: [],
        columns: {},
      },

      languagesAreEnabled: false,
    };
  },

  computed: {
    pageVisitsOverTimeDataSorted() {
      return [...this.charts.pageVisitsOverTime.data].sort((a, b) =>
        a.ss_uid < b.ss_uid ? -1 : a.ss_uid > b.ss_uid ? 1 : 0
      );
    },
  },

  methods: {
    loadData(response) {
      const map = {
        visitsOverTime: {
          data: 'visitsovertimedata',
          labels: 'chartperiodlabels',
        },
        pageVisitsOverTime: {
          data: 'pagevisitsovertimedata',
          labels: 'chartperiodlabels',
        },
        languagesOverTime: {
          data: 'languagesovertimedata',
          labels: 'chartperiodlabels',
        },
        globalLanguages: {
          data: 'globallanguagesdata',
          labels: 'chartlanguageslabels',
        },
      };

      Object.entries(map).forEach(([key, fields]) => {
        this.charts[key].data = response[fields.data] || [];
        this.charts[key].labels = response[fields.labels] || [];
      });

      this.table.rows = response.pagestatsdata || [];
      this.table.columns = response.pagestatslabels || {};

      this.languagesAreEnabled = Boolean(response.languagesAreEnabled);
    },
  },
};
</script>
