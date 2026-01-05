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
      if (this.percentage < 25) return 'var(--color-red-600)';
      if (this.percentage < 50) return 'var(--color-orange-600)';
      if (this.percentage < 75) return 'var(--color-yellow-600)';
      return 'var(--color-green-600)';
    },
  },
};
</script>

<style>
.ss-percentage-field-preview .ss-track {
  position: relative;
  display: flex;
  align-items: center;
  font-size: 0.9em;
  height: 1.25rem;
  background-color: light-dark(
    var(--color-gray-200),
    var(--color-gray-800)
  );
  border-radius: 0.25rem;
  overflow: hidden;
  user-select: none;
}

.ss-percentage-field-preview .ss-fill {
  height: 100%;
}

.ss-percentage-field-preview .ss-percentage {
  position: absolute;
  left: 50%;
  transform: translateX(-50%);
}
</style>
