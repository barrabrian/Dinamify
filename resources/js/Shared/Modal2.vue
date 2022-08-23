<template>
    <Transition name="fade" appear>
        <div class="modal-mask flex justify-center" @click="close" v-if="show">
            <div class="modal-container rounded-lg shadow-lg min-w-2xl" >
                <slot></slot>
            </div>
        </div>
    </Transition>
</template>

<script>
export default {
  props: {
    show: {
      type: Boolean,
      default: false,
    },
  },
  mounted: function () {
    document.addEventListener("keydown", (e) => {
      if (this.show && e.keyCode == 27) {
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
* {
    box-sizing: border-box;
}
.modal-mask {
    position: fixed;
    z-index: 9998;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, .3);
    transition: opacity .3s ease;
    overflow-x: auto;
}
.modal-container {
    margin: auto;
    padding: 20px 30px;
    background-color: #fff;
    transition: all .3s ease;
}

/*
 * The following styles are auto-applied to elements with
 * transition="modal" when their visibility is toggled
 * by Vue.js.
 *
 * You can easily play with the modal transition by editing
 * these styles.
 */
.modal-enter,
.modal-leave-active {
  opacity: 0;
}

.modal-enter .modal-container,
.modal-leave-active .modal-container {
  -webkit-transform: scale(1.1);
  transform: scale(1.1);
}


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