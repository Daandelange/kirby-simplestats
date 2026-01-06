<template>
  <k-grid variant="columns" style="column-gap: var(--spacing-8)">
    <k-column width="1/2">
      <k-simplestats-chart
        type="Pie"
        height="200"
        download="Site_ReferrersByMedium.png"
        :label="$t('simplestats.referers.referersbymedium')"
        :chart-data="charts.byMedium.data"
        :chart-labels="charts.byMedium.labels"
        :auto-colorize="true"
      />
    </k-column>

    <k-column width="1/2">
      <k-simplestats-chart
        type="Pie"
        height="200"
        download="Site_ReferrersByDomain.png"
        :label="$t('simplestats.referers.referersbydomain')"
        :chart-data="charts.byDomain.data"
        :chart-labels="charts.byDomain.labels"
        :auto-colorize="true"
      />
    </k-column>

    <k-column width="1/1">
      <k-simplestats-chart
        type="Line"
        height="300"
        download="Site_ReferersEvolution.png"
        :label="$t('simplestats.referers.referersovertime')"
        :chart-data="charts.byMediumOverTime.data"
        :chart-labels="charts.byMediumOverTime.labels"
        :y-title="$t('simplestats.charts.hitspermedium')"
        :stacked="true"
        :auto-colorize="true"
        :x-time-axis="true"
        :y-visits-axis="true"
      />
    </k-column>

    <k-column width="1/1">
      <k-simplestats-filter-table
        :label="$t('simplestats.referers.allreferers')"
        :rows="table.rows"
        :columns="table.columns"
      />
    </k-column>
  </k-grid>
</template>


<script>
import apiFetch from '../../mixins/apiFetch.js';

export default {
  mixins: [apiFetch],

  data() {
    return {
      charts: {
        byDomain: {
          data: [],
          labels: [],
        },
        byMedium: {
          data: [],
          labels: [],
        },
        byMediumOverTime: {
          data: [],
          labels: [],
        },
      },

      table: {
        rows: [],
        columns: {},
      },
    };
  },

  methods: {
    loadData(response) {
      const map = {
        byDomain: {
          data: 'referersbydomaindata',
          labels: 'referersbydomainlabels',
        },
        byMedium: {
          data: 'referersbymediumdata',
          labels: 'referersbymediumlabels',
        },
        byMediumOverTime: {
          data: 'referersbymediumovertimedata',
          labels: 'chartperiodlabels',
        },
      };

      Object.entries(map).forEach(([key, fields]) => {
        this.charts[key].data = response[fields.data] || [];
        this.charts[key].labels = response[fields.labels] || [];
      });

      this.table.rows = response.refererstabledata || [];
      this.table.columns = response.refererstablelabels || {};
    },
  },
};
</script>
