<template>
  <div class="k-fieldset k-simplestats-tracking">
    <k-grid variant="columns">
      <k-column width="1/1">
        <k-headline-field :label="$t('simplestats.info.tester.title')" />
      </k-column>

      <!-- LEFT COLUMN: Device & Referrer -->
      <k-column width="1/2">
        <!-- Device Testing -->
        <k-section :label="$t('simplestats.info.tester.device')">
          <div class="k-table">
            <table style="table-layout: auto">
              <tbody>
                <tr>
                  <th data-mobile="true">{{ $t('simplestats.info.tester.device.current.ua') }}</th>
                  <td data-mobile="true" colspan="2" class="k-table-cell">
                    <k-text-field-preview :value="currentUserAgent" />
                  </td>
                </tr>
                <tr>
                  <th data-mobile="true">{{ $t('simplestats.info.tester.device.current.detected') }}</th>
                  <td data-mobile="true" colspan="2" class="k-table-cell">
                    <k-text-field-preview :value="formattedCurrentUA" />
                  </td>
                </tr>
                <tr>
                  <th data-mobile="true">{{ $t('simplestats.info.tester.device.custom.ua') }}</th>
                  <td data-mobile="true" class="k-table-cell">
                    <k-text-input v-model="customUserAgent" class="k-text-field-preview" />
                  </td>
                  <td data-mobile="true" class="k-table-action">
                    <k-button size="sm" variant="filled" @click="testUserAgent">Go!</k-button>
                  </td>
                </tr>
                <tr v-if="customDevice">
                  <th data-mobile="true">{{ $t('simplestats.info.tester.device.custom.detected') }}</th>
                  <td data-mobile="true" colspan="2" class="k-table-cell">
                    <k-text-field-preview :value="formattedCustomUA" />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <br />
          <k-box v-if="customDevice" theme="info" :text="$t('simplestats.info.tester.device.note')" />
        </k-section>

        <!-- Referrer Testing -->
        <k-section :label="$t('simplestats.info.tester.referrer')">
          <div class="k-table">
            <table style="table-layout: auto">
              <tbody>
                <tr>
                  <th data-mobile="true">{{ $t('simplestats.info.tester.referrer.field') }}</th>
                  <td data-mobile="true" class="k-table-cell">
                    <k-url-input v-model="referrerField" class="k-text-field-preview" />
                  </td>
                  <td data-mobile="true" class="k-table-action">
                    <k-button size="sm" variant="filled" @click="testReferrer">Go!</k-button>
                  </td>
                </tr>
                <tr v-for="row in referrerRows" :key="row.key" v-if="referrerResponse">
                  <th data-mobile="true">{{ $t(row.label) }}</th>
                  <td data-mobile="true" colspan="2" class="k-table-cell">
                    <k-text-field-preview :value="row.value" />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </k-section>

      </k-column>

      <!-- RIGHT COLUMN: Stats Generator -->
      <k-column width="1/2">
        <k-section :label="$t('simplestats.info.tester.generator')">
          <div class="k-table">
            <table style="table-layout: auto">
              <tbody>
                <tr>
                  <th data-mobile="true">{{ $t('simplestats.info.tester.generator.mode') }}</th>
                  <td data-mobile="true" class="k-table-cell">
                    <k-select-input v-model="generatorMode" :options="generatorModes"/>
                  </td>
                </tr>
                <tr>
                  <th data-mobile="true">{{ $t('simplestats.info.tester.generator.datefrom') }}</th>
                  <td data-mobile="true" class="k-table-cell">
                    <k-date-input v-model="generatorFrom" />
                  </td>
                </tr>
                <tr>
                  <th data-mobile="true">{{ $t('simplestats.info.tester.generator.dateto') }}</th>
                  <td data-mobile="true" class="k-table-cell">
                    <k-date-input v-model="generatorTo" />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <br />
          <k-bar>
            <k-choice-input :checked="unlockGenerator" :label="$t('simplestats.info.tester.generator.unlockgenerator')" @input="acceptGenerate" />
            <k-button variant="filled" @click="generateStats">Go!</k-button>
          </k-bar>

          <br />
          <k-box v-if="generatorResponse?.error" theme="negative" :text="generatorResponse.error" />
          <k-code v-if="generatorResponse?.data" language="json">{{ generatorResponse.data }}</k-code>
        </k-section>
      </k-column>
    </k-grid>
  </div>
</template>

<script>
import { usePanel } from 'kirbyuse';

export default {
  data() {
    const to   = new Date();
    const from = new Date();
    from.setDate(to.getDate() - 30);

    return {
      isLoading: true,
      error: '',

      currentDevice: null,
      currentUserAgent: '',

      customUserAgent: '',
      customDevice: null,

      referrerField: '',
      referrerResponse: null,

      unlockGenerator: false,
      generatorMode: 'randommulti',
      generatorTo: to.toISOString(),
      generatorFrom: from.toISOString(),
      generatorResponse: null
    };
  },

  created() {
    this.load();
  },

  computed: {
    formattedCurrentUA() {
      return this.formatDevice(this.currentDevice);
    },

    formattedCustomUA() {
      return this.formatDevice(this.customDevice);
    },

    referrerRows() {
      const fields = ['host', 'source', 'medium', 'url'];
      return fields.map(key => ({
        key,
        label: `simplestats.info.tester.referrer.response.${key}`,
        value: this.formatReferrerField(key)
      }));
    },

    generatorModes() {
      return [
        { value: 'all', text: 'Static: all pages' },
        { value: 'randomsingle', text: 'Single random page' },
        { value: 'randommulti', text: 'Multiple random pages' }
      ];
    },
  },

  methods: {
    formatDevice(device) {
      if (!device || device.error) return device?.error || '-';
      return `${device.device} - ${device.system} - ${device.engine}`;
    },

    formatReferrerField(field) {
      if (!this.referrerResponse) return '-none-';
      if (this.referrerResponse.error) return this.referrerResponse.error;
      return this.referrerResponse[field] || '-none-';
    },

    handlePanelError(error) {
      const message = error?.message || 'Unknown error';
      if (this.$store?.dispatch) {
        this.$store.dispatch('notification/open', { type: 'error', message, timeout: 5000 });
      } else {
        const panel = usePanel();
        panel.error(message, true);
      }
    },

    normalizeResponse(value) {
      if (!value) return { error: 'Empty response' };
      if (typeof value === 'string') return { error: value };
      if (typeof value === 'object') return value;
      return { error: 'Invalid response format' };
    },

    async load() {
      try {
        const response = await this.$api.get('simplestats/trackingtester');
        this.isLoading = false;
        this.currentDevice = response.currentDeviceInfo;
        this.currentUserAgent = response.currentUserAgent || navigator.userAgent;
      } catch (error) {
        this.isLoading = false;
        this.error = error?.message || '';
        this.handlePanelError(error);
      }
    },

    async testUserAgent() {
      this.customDevice = null;
      try {
        const response = await this.$api.get('simplestats/trackingtester/ua?ua=' + encodeURIComponent(this.customUserAgent));
        this.customDevice = this.normalizeResponse(response);
      } catch (error) {
        this.customDevice = { error: error?.message || 'Loading error' };
      }
    },

    async testReferrer() {
      this.referrerResponse = null;
      try {
        const response = await this.$api.get('simplestats/trackingtester/referrer?referrer=' + encodeURIComponent(this.referrerField));
        this.referrerResponse = this.normalizeResponse(response.referrerInfo);
      } catch (error) {
        this.referrerResponse = { error: error?.message || 'Loading error' };
      }
    },

    async generateStats() {
      const fromTimestamp = Math.floor(new Date(this.generatorFrom).getTime() / 1000);
      const toTimestamp = Math.floor(new Date(this.generatorTo).getTime() / 1000);

      try {
        this.generatorResponse = await this.$api.get(
          `simplestats/trackingtester/generatestats?proceed=${this.unlockGenerator ? 'yes' : 'no'}&from=${fromTimestamp}&to=${toTimestamp}&mode=${this.generatorMode}`
        );
      } catch (error) {
        this.generatorResponse = { error: error?.message || 'Loading error' };
      }
    },

    acceptGenerate(value) {
      this.unlockGenerator = value;
    }
  }
};
</script>

<style>
.k-simplestats-tracking .k-table-action {
  width: 0 !important;
}

.k-simplestats-tracking .k-text-field-preview {
  white-space: normal;
}
</style>
