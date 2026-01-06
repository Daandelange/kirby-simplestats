<template>
  <k-grid variant="columns" style="column-gap: var(--spacing-8)">
    <k-column width="1/3">
      <k-simplestats-chart
        type="Pie"
        height="200"
        download="Site_Devices.png"
        :label="$t('simplestats.devices.graph.devices')"
        :chart-data="charts.devices.data"
        :chart-labels="charts.devices.labels"
        :auto-colorize="true"
      />
    </k-column>

    <k-column width="1/3">
      <k-simplestats-chart
        type="Pie"
        height="200"
        download="Site_BrowserEngines.png"
        :label="$t('simplestats.devices.graph.engines')"
        :chart-data="charts.browsers.data"
        :chart-labels="charts.browsers.labels"
        :auto-colorize="true"
      />
    </k-column>

    <k-column width="1/3">
      <k-simplestats-chart
        type="Pie"
        height="200"
        download="Site_OperatingSystems.png"
        :label="$t('simplestats.devices.graph.oses')"
        :chart-data="charts.systems.data"
        :chart-labels="charts.systems.labels"
        :auto-colorize="true"
      />
    </k-column>

    <k-column width="1/1">
      <k-simplestats-chart
        type="Line"
        height="300"
        download="Site_DevicesEvolution.png"
        :label="$t('simplestats.devices.graph.devicehistory')"
        :chart-data="charts.devicesOverTime.data"
        :chart-labels="charts.devicesOverTime.labels"
        :x-title="$t('simplestats.charts.time')"
        :y-title="$t('simplestats.devices.graph.devicehistory.y')"
        :stacked="true"
        :auto-colorize="true"
        :x-time-axis="true"
        :y-visits-axis="true"
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
        devices: {
          data: [],
          labels: [],
        },
        browsers: {
          data: [],
          labels: [],
        },
        systems: {
          data: [],
          labels: [],
        },
        devicesOverTime: {
          data: [],
          labels: [],
        },
      },
    };
  },

  methods: {
    loadData(response) {
      const map = {
        devices: {
          data: 'devicesdata',
          labels: 'deviceslabels',
        },
        browsers: {
          data: 'enginesdata',
          labels: 'engineslabels',
        },
        systems: {
          data: 'systemsdata',
          labels: 'systemslabels',
        },
        devicesOverTime: {
          data: 'devicesovertime',
          labels: 'chartperiodlabels',
        },
      };

      Object.entries(map).forEach(([key, fields]) => {
        this.charts[key].data = response[fields.data] || [];
        this.charts[key].labels = response[fields.labels] || [];
      });
    },
  },
};
</script>
