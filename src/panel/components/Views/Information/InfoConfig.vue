<template>
  <div class="k-fieldset">
    <k-grid variant="columns">
      <k-column width="1/1">
        <k-headline-field :label="$t('simplestats.info.config.title')" />
      </k-column>

      <!-- Tracking Info Table -->
      <k-column width="1/1">
        <k-simplestats-info-table
          :label="$t('simplestats.info.config.tracking')"
          :rows="trackingRows"
        />
      </k-column>

      <!-- Logging Info Table -->
      <k-column width="1/1">
        <k-simplestats-info-table
          :label="$t('simplestats.info.config.log.title')"
          :rows="loggingRows"
        />
      </k-column>

      <k-column v-if="!isLoading && !saltIsSet" width="1/1">
        <k-section :label="$t('simplestats.info.config.tracking.unsalted')">
          <k-box theme="negative" :text="$t('simplestats.info.config.tracking.unsalted.warn')" />
        </k-section>
      </k-column>
    </k-grid>
  </div>
</template>

<script>
import apiFetch from '../../../mixins/apiFetch.js';

export default {
  mixins: [apiFetch],

  data() {
    return {
      tracking: {
        periodName: '',
        since: '',
        uniqueSeconds: '',
        enableReferers: false,
        enableDevices: false,
        enableVisits: false,
        enableVisitLanguages: false,
        ignoredRoles: [],
        ignoredPages: [],
        ignoredTemplates: []
      },
      logging: {
        file: '',
        levels: []
      },
      saltIsSet: false
    };
  },

  computed: {
    trackingRows() {
      return [
        {
          label: 'simplestats.info.config.tracking.period.name',
          value: this.tracking.periodName
        },
        {
          label: 'simplestats.info.config.tracking.period.secs',
          value: this.tracking.uniqueSeconds,
          column: { after: this.$t('simplestats.chart.seconds') }
        },
        {
          label: 'simplestats.info.config.tracking.salted',
          preview: 'k-toggle-field-preview',
          value: this.saltIsSet
        },
        {
          label: 'simplestats.info.config.tracking.features',
          preview: 'k-tags-field-preview',
          value: this.trackingFeatures
        },
        {
          label: 'simplestats.info.config.tracking.ignore.roles',
          preview: 'k-tags-field-preview',
          value: this.tracking.ignoredRoles
        },
        {
          label: 'simplestats.info.config.tracking.ignore.ids',
          preview: 'k-tags-field-preview',
          value: this.tracking.ignoredPages
        },
        {
          label: 'simplestats.info.config.tracking.ignore.templates',
          preview: 'k-tags-field-preview',
          value: this.tracking.ignoredTemplates
        }
      ];
    },

    loggingRows() {
      return [
        {
          label: 'simplestats.info.config.log.file',
          preview: 'k-files-field-preview',
          value: this.logging.file
        },
        {
          label: 'simplestats.info.config.log.level',
          preview: 'k-tags-field-preview',
          value: this.logging.levels
        }
      ];
    },

    trackingFeatures() {
      const featuresMap = [
        ['enableReferers', 'simplestats.info.config.tracking.referers'],
        ['enableDevices', 'simplestats.info.config.tracking.devices'],
        ['enableVisits', 'simplestats.info.config.tracking.visits'],
        ['enableVisitLanguages', 'simplestats.info.config.tracking.languages']
      ];

      return featuresMap
        .filter(([key]) => this.tracking[key])
        .map(([_, label]) => this.$t(label));
    }
  },

  methods: {
    loadData(response) {
      this.saltIsSet = response.saltIsSet;

      Object.assign(this.tracking, {
        periodName: response.trackingPeriodName,
        since: response.trackingSince,
        uniqueSeconds: response.uniqueSeconds,
        enableReferers: response.enableReferers,
        enableDevices: response.enableDevices,
        enableVisits: response.enableVisits,
        enableVisitLanguages: response.enableVisitLanguages,
        ignoredRoles: response.ignoredRoles,
        ignoredPages: response.ignoredPages,
        ignoredTemplates: response.ignoredTemplates
      });

      Object.assign(this.logging, {
        file: response.logFile,
        levels: response.logLevels
      });
    }
  }
};
</script>
