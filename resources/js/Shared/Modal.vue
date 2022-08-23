<template>
  <button type="button" @click="toggleShow">
    <slot />
    <!-- <Transition name="pop" appear> -->
      <div v-if="show">
        <div style="position: fixed; top: 0; right: 0; left: 0; bottom: 0; z-index: 99998; background: black; opacity: 0.3"  />
        <div ref="modal" class="px-4 py-8 bg-white cursor-auto text-gray-800 md:p-8 md:overflow-y-auto w-1/2 rounded-lg" style="position: absolute; z-index: 99999; top: 50%; left: 0%; -webkit-transform:translate(50%, -50%);-moz-transform:translate(50%, -50%);-ms-transform:translate(50%, -50%);-o-transform:translate(50%, -50%);transform:translate(50%, -50%); " @click.stop="show = !autoClose">
          <slot name="content" />
        </div>
      </div>
    <!-- </Transition> -->
  </button>
</template>

<script>
import { createPopper } from '@popperjs/core'

export default {
  props: {
    placement: {
      type: String,
      default: 'bottom-end',
    },
    autoClose: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    return {
      show: false,
    }
  },
  watch: {
    show(show) {
      if (show) {
        // this.$nextTick(() => {
        //   this.popper = createPopper(this.$el, this.$refs.dropdown, {
        //     placement: this.placement,
        //     modifiers: [
        //       {
        //         name: 'preventOverflow',
        //         options: {
        //           altBoundary: true,
        //         },
        //       },
        //     ],
        //   })
        // })
      } else if (this.popper) {
        setTimeout(() => this.popper.destroy(), 100)
      }
    },
  },
  methods: {
    toggleShow(){
      if (this.show) {
        this.show = false;
      } else {
        this.show = true;
      }
    },
  },
  mounted() {
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        this.show = false
      }
    })
  },
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