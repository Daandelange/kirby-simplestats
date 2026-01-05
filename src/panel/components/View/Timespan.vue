<template>
  <k-button-group layout="collapsed">
    <!-- FROM PICKER -->
    <k-button
      :dropdown="true"
      :text="formatDateLabel(dateFrom)"
      icon="calendar"
      size="sm"
      variant="filled"
      @click="$refs.fromDropdown.toggle()"
    />

    <k-picklist-dropdown
      ref="fromDropdown"
      :multiple="false"
      :options="fromOptions"
      :search="{ display: 12 }"
      :value="dateFrom"
      @input="onFromSelect"
    />

    <!-- TO PICKER -->
    <k-button
      :dropdown="true"
      :text="formatDateLabel(dateTo)"
      icon="calendar"
      size="sm"
      variant="filled"
      @click="$refs.toDropdown.toggle()"
    />

    <k-picklist-dropdown
      ref="toDropdown"
      :multiple="false"
      :options="toOptions"
      :search="{ display: 12 }"
      :value="dateTo"
      @input="onToSelect"
    />
  </k-button-group>
</template>

<script>
export default {
  props: {
    dateChoices: {
      type: Array,
      default: () => [],
    },
    timePeriod: {
      type: String,
      default: 'Monthly',
    },
    initialViewPeriods: {
      type: Number,
      default: -1,
    },
  },

  data() {
    return {
      dateFrom: '',
      dateTo: '',
    };
  },

  mounted() {
    const lastIndex = this.dateChoices.length - 1;

    this.dateFrom = this.dateChoices[0];
    this.dateTo = this.dateChoices[lastIndex];

    if (this.initialViewPeriods > 0) {
      this.dateFrom = this.dateChoices[Math.max(0, lastIndex - this.initialViewPeriods)];
    }
  },

  computed: {
    fromOptions() {
      const toIndex = this.dateChoices.indexOf(this.dateTo);

      return this.dateChoices.map((date, i) => ({
        value: date,
        text: this.formatDateLabel(date),
        disabled: toIndex >= 0 && i > toIndex,
      }));
    },

    toOptions() {
      const fromIndex = this.dateChoices.indexOf(this.dateFrom);

      return this.dateChoices.map((date, i) => ({
        value: date,
        text: this.formatDateLabel(date),
        disabled: fromIndex >= 0 && i < fromIndex,
      }));
    },
  },

  methods: {
    formatDateLabel(date) {
      if (!date) return 'Select date';

      return this.$library.dayjs(date).format(
        this.timePeriod === 'Monthly' ? 'MMM YYYY' : 'MMM YYYY' // use timeFrameUtility?
      );
    },

    onFromSelect(value) {
      if (!value || value === this.dateFrom) {
        this.$refs.fromDropdown?.close();
        return;
      }

      this.dateFrom = value;
      this.$refs.fromDropdown?.close();
      this.emitChange();
    },

    onToSelect(value) {
      if (!value || value === this.dateTo) {
        this.$refs.toDropdown?.close();
        return;
      }

      this.dateTo = value;
      this.$refs.toDropdown?.close();
      this.emitChange();
    },

    emitChange() {
      this.$emit('change', {
        from: this.dateFrom,
        to: this.dateTo,
      });
    },
  },
};
</script>
