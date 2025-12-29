<template>
  <p class="ss-percentage-field-preview k-text-field-preview">
    <span class="ss-track">
      <span
        class="ss-fill"
        :style="{
          width: percentage + '%',
          backgroundColor: color
        }"
      />
      <span class="ss-percentage">{{ percentage }}%</span>
    </span>
  </p>
</template>

<script>
export default {
  props: {
    value: {
      type: [Number, String],
      default: 0,
    },
  },

  computed:{
    percentage() {
      const number = Number(this.value) || 0;
      return Math.min(100, Math.max(0, Math.round(number * 100)));
    },

    color() {
      return this.percentage < 25
        ? 'light-dark(var(--color-red-600), var(--color-red-550))'
        : this.percentage < 50
        ? 'light-dark(var(--color-orange-600), var(--color-orange-550))'
        : this.percentage < 75
        ? 'light-dark(var(--color-yellow-600), var(--color-yellow-550))'
        : 'light-dark(var(--color-green-600), var(--color-green-550))';
    },
  },
};
</script>

<style lang="less">
.ss-percentage-field-preview {
  .ss-track {
    position: relative;
    display: flex;
    align-items: center;
    font-size: .9em;
    height: 1.25rem;
    background-color: light-dark(
      var(--color-gray-200),
      var(--color-gray-800)
    );
    border-radius: .25rem;
    overflow: hidden;
    user-select: none;
  }

  .ss-fill {
    height: 100%;
  }

  .ss-percentage {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
  }
}
</style>
