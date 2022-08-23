<template>
    <div class="" v-if="show">
        <div style="position: fixed; top: 0; right: 0; left: 0; bottom: 0; z-index: 99998; background: black; opacity: 0.3" @click="close" ></div>
        <div class="px-4 py-8 bg-white cursor-auto text-gray-800 md:p-8 md:overflow-y-auto w-1/4 h-screen" style="position: absolute; z-index: 99999; top: 0%; right: 0%; " @click.stop="show = !autoClose">
          <slot />
        </div>
    </div>
</template>

<script>
export default {
  props: {
    show: {
      type: Boolean,
      default: false,
    },
  },
  mounted() {
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        this.close()
      }
    })
  },
  methods: {
    close: function() {
      this.$emit('close')
    }
  }
}
</script>
  
<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity .4s linear;
}

.fade-enter,
.fade-leave-to {
  opacity: 0;
}

.pop-enter-active,
.pop-leave-active {
  transition: transform 0.4s cubic-bezier(0.5, 0, 0.5, 1), opacity 0.4s linear;
}

.pop-enter,
.pop-leave-to {
  opacity: 0;
  transform: scale(0.3) translateY(-50%);
}
</style>