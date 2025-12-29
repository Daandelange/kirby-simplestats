<template>
  <div class="k-fieldset">
    <k-grid variant="columns">
      <k-column width="1/1">
        <k-headline-field :label="$t('simplestats.info.title')" />
      </k-column>

      <k-column width="1/1">
        <InfoTable :label="$t('simplestats.info.db.title')" :rows="dbInfo" />
      </k-column>

      <k-column width="1/1">
        <SearchableTable
          :label="$t('simplestats.info.db.versionhistory')"
          :rows="dbHistory"
          :columns="dbHistoryLabels"
        />
      </k-column>

      <k-column width="1/1">
        <k-section :label="$t('simplestats.info.dbreqs.title')">
          <k-box v-if="dbRequirementsPassed" theme="positive" :text="$t('simplestats.info.dbreqs.positive')" />
          <k-box v-else theme="negative">
            <k-text>
              <p>{{ $t('simplestats.info.dbreqs.negative') }}</p>
              <k-code>{{ dbRequirements }}</k-code>
            </k-text>
          </k-box>
        </k-section>
      </k-column>

      <k-column v-if="upgradeRequired" width="1/1">
        <k-section v-if="!updateMessage" :label="$t('simplestats.info.dbupdate.required')">
          <k-box :html="true" theme="negative" :text="$t('simplestats.info.dbupdate.requiredmsg')" />
          <br/>
          <k-checkbox-input :label="$t('simplestats.info.dbupdate.isbackedup')" :value="unlockUpgrade" @input="acceptUpgrade" />
          <br/>
          <k-button variant="filled" :icon="isUpdatingDb ? 'loader' : 'bolt'" @click="requestUpgrade">{{ $t('simplestats.info.dbupdate.go') }}</k-button>
        </k-section>

        <k-section v-else :label="$t('simplestats.info.dbupdate.result')">
          <k-box :html="true" :theme="updateMessageTheme" :text="updateMessage" />
          <br />
          <k-button variant="filled" icon="refresh" @click="load">{{ $t('simplestats.info.dbupdate.refresh') }}</k-button>
        </k-section>
      </k-column>

      <k-column v-else-if="updateMessage==null" width="1/1">
        <k-section :label="$t('simplestats.info.dbupdate.title')">
          <k-info-field theme="positive" :text="$t('simplestats.info.dbupdate.isuptodate')" />
        </k-section>
      </k-column>

      <k-column v-else-if="updateMessage!==null" width="1/1">
        <k-section :label="$t('simplestats.loaderror')">
          <k-info-field html="true" theme="negative" :text="updateMessage" />
        </k-section>
      </k-column>
    </k-grid>
  </div>
</template>

<script>
import SearchableTable from '../Ui/SearchableTable.vue';
import InfoTable from '../Ui/InfoTable.vue';
import { usePanel } from 'kirbyuse';

export default {
  extends: 'k-pages-section',

  components: {
    SearchableTable,
    InfoTable,
  },

  data() {
    return {
      db: {
        history: [],
        historyLabels: {},
        version: 'undefined',
        softwareVersion: 'unknown',
        location: '',
        size: '',
        spanFrom: '',
        spanTo: '',
        timeframes: -1,
      },

      requirements: {
        passed: true,
        message: 'unknown',
      },

      upgrade: {
        required: false,
        unlocked: false,
        isUpdating: false,
        resultMessage: null,
        resultTheme: '',
      },
    };
  },

  created() {
    this.load();
  },

  computed: {
    dbInfo() {
      return [
        {
          label: 'simplestats.info.db.file',
          preview: 'k-files-field-preview',
          value: this.db.location,
        },
        {
          label: 'simplestats.info.db.size',
          value: this.db.size,
          format: this.niceSize,
        },
        {
          label: 'simplestats.info.db.dbversion',
          value: this.db.version,
        },
        {
          label: 'simplestats.info.db.softwareversion',
          value: this.db.softwareVersion,
        },
        {
          label: 'simplestats.info.db.spanfromperiod',
          preview: 'k-date-field-preview',
          value: this.db.spanFrom,
        },
        {
          label: 'simplestats.info.db.spantoperiod',
          preview: 'k-date-field-preview',
          value: this.db.spanTo,
        },
        {
          label: 'simplestats.info.db.spannumperiods',
          value: this.db.timeframes,
        },
      ];
    },

    dbHistory() {
      return this.db.history
    },

    dbHistoryLabels() {
      return Object.fromEntries(
        Object.entries(this.db.historyLabels).map(([key, column]) => [
          key,
          {
            ...column,
            mobile: true,
          },
        ]),
      );
    },

    upgradeRequired() {
      return this.upgrade.required;
    },

    dbRequirementsPassed() {
      return this.requirements.passed;
    },

    dbRequirements() {
      return this.requirements.message;
    },

    unlockUpgrade() {
      return this.upgrade.unlocked;
    },

    isUpdatingDb() {
      return this.upgrade.isUpdating;
    },

    updateMessage() {
      return this.upgrade.resultMessage;
    },

    updateMessageTheme() {
      return this.upgrade.resultTheme;
    },
  },

  methods: {
    async load() {
      this.upgrade.resultMessage = null;

      await Promise.all([
        this.loadDbInfo(),
        this.loadRequirements(),
      ]);
    },

    async loadDbInfo() {
      try {
        const response = await this.$api.get('simplestats/listdbinfo');

        this.db.history         = response.dbHistory;
        this.db.historyLabels   = response.dbHistoryLabels;
        this.db.version         = response.dbVersion;
        this.db.softwareVersion = response.softwareDbVersion;
        this.db.location        = response.databaseLocation;
        this.db.size            = response.databaseSize;
        this.db.spanFrom        = response.databaseSpanFrom;
        this.db.spanTo          = response.databaseSpanTo;
        this.db.timeframes      = response.databaseTimeframes;

        this.upgrade.required   = response.upgradeRequired;
      }
      catch (error) {
        this.handleError(error);
        this.upgrade.resultMessage = error.message;
      };
    },

    async loadRequirements() {
      try {
        const response = await this.$api.get('simplestats/checkrequirements');

        this.requirements.message = response.dbRequirements;
        this.requirements.passed  = response.dbRequirementsPassed;
      }
      catch (error) {
        this.requirements.message = error.message;
        this.handleError(error);
      };
    },

    acceptUpgrade(value) {
      this.upgrade.unlocked = value;
    },

    async requestUpgrade(event) {
      event.stopPropagation();

      if (!this.upgrade.unlocked) {
        this.notifyError('Before hitting that button, please ensure to backup your database file!');
        return;
      };

      this.upgrade.isUpdating = true;
      this.upgrade.resultMessage = null;

      try {
        const response = await this.$api.get('simplestats/dbupgrade');

        this.upgrade.resultMessage = response.message;
        this.upgrade.resultTheme   = response.status ? 'positive' : 'passive';
      }
      catch (error) {
        this.handleError(error);
      }
      finally {
        this.upgrade.isUpdating = false;
      };
    },

    handleError(error) {
      const message = error?.message || 'Unknown error';

      if (this.$store?.dispatch) {
        this.$store.dispatch('notification/open', {
          type: 'error',
          message,
          timeout: 5000,
        });
      } else {
        const panel = usePanel();
        panel.error(message, true);
      };
    },

    notifyError(message) {
      this.handleError({ message });
    },

    niceSize(num) {
      num = Number(num);
      if (!Number.isFinite(num)) return '?? B';

      const units = ['B', 'kB', 'MB', 'GB', 'TB', 'PB'];
      const neg = num < 0;
      if (neg) num = Math.abs(num);

      if (num < 1000) return `${neg ? '-' : ''}${num} B`;

      const exp = Math.min(
        Math.floor(Math.log(num) / Math.log(1000)),
        units.length - 1,
      );

      const value = (num / Math.pow(1000, exp)).toFixed(2);
      return `${neg ? '-' : ''}${value} ${units[exp]}`;
    },
  },
};
</script>
