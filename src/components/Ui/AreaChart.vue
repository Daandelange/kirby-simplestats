<template>
  <k-section :label="label" :class="'ss-chart ss-' + type.toLowerCase() + '-chart'">
    <a
      v-if="download !== false"
      :download="fileDownloadStr"
      :href="base64image"
      slot="options"
    >
      <k-button
        :disabled="!base64image"
        icon="download"
        size="xs"
        text="Download"
        title="Exports the chart as a PNG file that you can archive or share."
        variant="filled"
      />
    </a>

    <ChartJS
      v-if="hasDataForCurrentPeriod"
      :is="type + 'Chart'"
      :chart-data="fullChartData"
      :chart-options="fullChartOptions"
      :height="height"
      ref="chart"
      class="ss-chart-wrapper"
    />

    <k-empty v-else icon="chart" layout="cards" :text="emptyText" :style="{ height: emptyHeight }" />
  </k-section>
</template>

<script>
import { Line as LineChart, Bar as BarChart, Pie as PieChart } from 'vue-chartjs/legacy';
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  LinearScale,
  CategoryScale,
  PointElement,
  TimeScale,
  Filler,
  BarElement,
  ArcElement,
} from 'chart.js';
import 'chartjs-adapter-date-fns';

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  LineElement,
  LinearScale,
  CategoryScale,
  PointElement,
  TimeScale,
  Filler,
  BarElement,
  ArcElement,
);

export default {
  name: 'AreaChart',

  components: {
    LineChart,
    BarChart,
    PieChart,
  },

  inheritAttrs: false,

  props: {
    type: {
      type: String,
      default: 'Line',
      validator: v => ['Line', 'Bar', 'Pie'].includes(v),
    },
    chartData: {
      type: Array,
      default: () => [],
    },
    chartLabels: {
      type: Array,
      default: () => [],
    },
    chartOptions: {
      type: Object,
      default: () => ({}),
    },
    label: String,
    xTitle: {
      type: String,
      default: null,
    },
    yTitle: {
      type: String,
      default: null,
    },
    download: {
      type: [String, Boolean],
      default: false,
    },
    height: Number,
    autoColorize: Boolean,
    autoGreyize: Boolean,
    showLegend: {
      type: Boolean,
      default: true,
    },
    fill: {
      type: [Boolean, String],
      default: true,
    },
    xTimeAxis: Boolean,
    yVisitsAxis: Boolean,
    stacked: Boolean,
  },

  data() {
    return {
      base64image: '',
    };
  },

  computed: {
    emptyText() {
      return this.chartData.length === 0
        ? this.$t('simplestats.nodatayet')
        : this.$t('simplestats.nodatafortimerange');
    },

    emptyHeight() {
      const spacing = this.type === 'Pie' ? '--spacing-10' : '--spacing-8';
      return `calc(${this.height}px + var(${spacing}))`;
    },

    hasDataForCurrentPeriod() {
      return this.chartData.some(
        dataset => Array.isArray(dataset.data) && dataset.data.length > 0
      );
    },

    fileDownloadStr() {
      const date = new Date();
      const suffix =
        date.getFullYear() +
        '-' +
        String(date.getMonth() + 1).padStart(2, '0') +
        '-' +
        String(date.getDate()).padStart(2, '0');

      return String(this.download || 'MyChart.png').replace(
        '.png',
        '-' + suffix + '.png'
      );
    },

    fullChartOptions() {
      const when = (c, o) => (c ? o : {});

      return this.deepMerge(
        {},
        this.chartOptions,

        // Common options
        {
          animation: {
            onComplete: this.generateDownloadLink,
          },
          barPercentage: 0.5,
          height: this.height,
          maintainAspectRatio: false,
          borderColor: '#000',
          borderWidth: 1,

          datasets: {
            line: {
              borderWidth: 0,
              pointRadius: 0,
              pointHitRadius: 3,
              pointHoverRadius: 3,
            },
          },

          plugins: {
            legend: { maxHeight: 75 },
            filler: { propagate: true },
            tooltip: {
              filter(ctx) {
                if (ctx.chart.data.datasets.length > 5 && ctx.raw === 0) {
                  return false;
                }
                return true;
              },
            },
          },
        },

        // Non-pie charts always get a base x-axis
        when(this.type !== 'Pie', {
          scales: {
            xAxis: {
              afterSetDimensions(scale) {
                scale.maxHeight = 25;
              },
            },
          },
        }),

        // Filled charts
        when(this.fill, {
          fill: true,
          backgroundColor: this.chartColor,
        }),

        // Time-based x-axis
        when(this.xTimeAxis, {
          scales: {
            xAxis: {
              type: 'time',
              title: {
                display: false,
                text: this.xTitle ?? this.$t('simplestats.charts.time'),
              },
              time: {
                unit: 'month',
                displayFormats: {
                  month: 'MMM yyyy',
                },
              },
            },
          },
        }),

        // Visits y-axis
        when(this.yVisitsAxis, {
          scales: {
            yAxis: {
              beginAtZero: true,
              title: {
                display: true,
                text: this.yTitle ?? this.$t('simplestats.charts.visits'),
              },
            },
          },
        }),

        // Stacked charts
        when(this.stacked, {
          scales: {
            yAxis: {
              stacked: true,
            },
          },
        }),

        // Legend disabled
        when(this.showLegend === false, {
          plugins: {
            legend: {
              display: false,
              title: { display: false },
            },
          },
          scales: {
            xAxis: {
              display: true,
              title: { display: false },
            },
            yAxis: {
              tickLength: 5,
            },
          },
        }),
      );
    },

    fullChartData() {
      const uidTree = this.chartData.map((d, i) => d.ss_uid ?? i);

      return {
        labels: this.chartLabels,
        datasets: this.chartData.map((dataset, index) => {
          const ds = { ...dataset };

          if (
            (this.autoColorize || this.autoGreyize) &&
            !ds.backgroundColor
          ) {
            ds.backgroundColor =
              this.type === 'Pie'
                ? this.generatePieColors()
                : this.generateDatasetColor(ds, uidTree, index);
          }

          return ds;
        }),
      };
    },

    chartColor() {
      if (typeof this.fill === 'string' && this.fill.length > 0) {
        const parsed = this.$library.colors.parseAs(this.fill, 'hex');
        if (parsed && parsed.length) return parsed;
      };

      const defaultEl = document.getElementById('chart-default-color-getter');
      if (defaultEl) {
        const computedStyle = window.getComputedStyle(defaultEl);
        if (computedStyle?.color) {
          const parsed = this.$library.colors.parseAs(computedStyle.color, 'hex');
          if (parsed && parsed.length) return parsed;
        };
      };

      return '#313740';
    },
  },

  methods: {
    deepMerge(target, ...sources) {
      for (const source of sources) {
        if (!source || typeof source !== 'object') continue;

        for (const key in source) {
          const value = source[key];

          if (value && typeof value === 'object' && !Array.isArray(value)
          ) {
            target[key] = this.deepMerge(target[key] || {}, value);
          } else {
            target[key] = value;
          }
        }
      }

      return target;
    },

    generateDownloadLink() {
      if (this.download === false) return;

      const chart = this.$refs.chart?._data?._chart;
      if (chart?.canvas) {
        this.base64image = chart.toBase64Image('image/png');
      }
    },

    generatePieColors() {
      const count = Math.max(this.chartLabels.length, 1);
      return this.chartLabels.map((_, i) =>
        this.autoColorize
          ? `hsl(${Math.round((360 / count) * i)},30%,40%)`
          : `hsl(0,0%,${40 + (40 / count) * i}%)`
      );
    },

    generateDatasetColor(dataset, uidTree, index) {
      let hue = 0;
      let lightness = 40;
      let saturation = this.autoGreyize ? 0 : 30;

      const parts = dataset.ss_uid ? dataset.ss_uid.split('/') : [index];

      parts.forEach((_, depth) => {
        const siblings =
          depth === 0
            ? uidTree.filter(uid => !String(uid).includes('/'))
            : uidTree.filter(uid =>
                String(uid).startsWith(parts.slice(0, depth).join('/'))
              );

        const pos = siblings.indexOf(dataset.ss_uid ?? index);
        const count = Math.max(siblings.length, 1);

        if (depth === 0) hue = (360 / count) * pos;
        else lightness += (50 / count) * pos;
      });

      return `hsl(${Math.round(hue)},${saturation}%,${Math.round(lightness)}%)`;
    },
  },
}
</script>

<style>
.ss-chart .ss-chart-wrapper {
  background-color: var(--table-color-back);
  padding: var(--spacing-4) var(--spacing-3);
  box-shadow: var(--shadow);
  border-radius: var(--rounded);
}

.ss-chart.ss-pie-chart .ss-chart-wrapper {
  padding: var(--spacing-4) 0 var(--spacing-6);
}
</style>
