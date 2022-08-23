<template>
    <div class="" v-if="show">
        <div style="position: fixed; top: 0; right: 0; left: 0; bottom: 0; z-index: 999991; background: black; opacity: 0.3" @click="close" ></div>
        <div class="bg-white cursor-auto text-gray-800 w-1/3 rounded-lg shadow" style="position: absolute; z-index: 999992; top: 50%; left: 50%; -webkit-transform:translate(-50%, -50%);-moz-transform:translate(-50%, -50%);-ms-transform:translate(-50%, -50%);-o-transform:translate(-50%, -50%);transform:translate(-50%, -50%); " @click.stop="show = !autoClose">
            <div class="bg-gray-100 w-full p-8 flex justify-center rounded-t">
                <icon name="image" class="w-12 h-12 fill-gray-400" />
            </div>
            <form @submit.prevent="store" class="flex flex-col items-center gap-4 px-4 py-8 md:p-8 md:overflow-x-auto">
                <text-input v-model="form.name" :error="form.errors.name" class="w-full " label="Nome" />
                <file-input v-model="form.image" :error="form.errors.image" class="w-full " type="file" accept="image/*" label="Imagem" />
                <loading-button :loading="form.processing" class="btn-emerald w-full cursor-pointer mt-4" type="submit">Salvar</loading-button>
            </form>
        </div>
    </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import FileInput from '@/Shared/FileInput'
import TextInput from '@/Shared/TextInput'
import LoadingButton from '@/Shared/LoadingButton'

export default {
  components: {
    FileInput,
    LoadingButton,
    TextInput,
    Icon,
  },
  props: {
    show: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      form: this.$inertia.form({
        name: '',
        image: null,
      }),
    }
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
    },
    store() {
      this.form.post('/settings/images');
      this.form.name = '';
      this.form.image = null;
      this.$emit('close');
    },
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