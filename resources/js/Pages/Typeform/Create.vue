<template>
  <div>
    <Head title="Integrações" />
    
    <div class="shadow-lg grid grid-cols-12 grid-flow-col border border-gray-300 rounded h-screen" style="height: calc(100vh - 150px - 3rem);">
      <div class="bg-gray-200 border-r-2 border-gray-300 shadow-lg">
        <div class="flex flex-col justify-center items-top">
          <Link class="group flex justify-center py-8 items-center bg-white w-full border-b border-gray-300" href="/settings/plugins">
            <icon name="plugin" class="w-8 h-8 fill-gray-700 group-hover:fill-gray-900" />
          </Link>
          <Link class="group flex justify-center py-8 items-center  w-full border-b border-gray-300" href="/settings/images">
            <icon name="image" class="w-8 h-8 fill-gray-700 group-hover:fill-gray-900" />
          </Link>
          <Link class="group flex justify-center py-8 items-center  w-full border-b border-gray-300" href="/settings/users">
            <icon name="person" class="w-8 h-8 fill-gray-700 group-hover:fill-gray-900" />
          </Link>
          <Link class="group flex justify-center py-8 items-center  w-full border-b border-gray-300" href="#">
            <icon name="config" class="w-8 h-8 fill-gray-700 group-hover:fill-gray-900" />
          </Link>
        </div>
      </div>
      
      <form class="col-span-11 bg-gray-100" @submit.prevent="store">
        <div class="p-12 bg-white flex justify-between items-center">
          <div class="flex justify-center items-center">
            <Link class="inline mr-3 group" href="/settings/plugins">
              <icon name="arrow-left" class="w-7 h-7 fill-gray-700 group-hover:fill-gray-600 inline" />
            </Link>
            <h1 class="text-3xl font-bold inline">Nova Integração</h1>
          </div>

        </div>
        <div class="p-12 border-t border-gray-300 ">
          <div class="border border-gray-300 rounded ">
            <div class="flex justify-between items-center p-6  border-b border-gray-300">
              <div>
                <icon name="typeform" class="mr-4 w-12 h-12" />
              </div>
              <div>
                <div class="font-bold text-xl">Typeform</div>
              </div>
              <loading-button :loading="form.processing" class="btn-emerald" type="submit">
                Salvar
              </loading-button>
            </div>
            <div class=" grid grid-flow-col gap-4 p-8 font-bold">
              <text-input v-model="form.name" :error="form.errors.name" class="w-full" label="Nome" />
              <text-input v-model="form.token" :error="form.errors.token" class="w-full" label="Token" />
            </div>
          </div>
        </div>

      </form>
    </div>


  </div>
</template>


<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import Icon from '@/Shared/Icon'

export default {
  components: {
    Head,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
    Icon,
  },
  layout: Layout,
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        name: null,
        token: null,
      }),
    }
  },
  methods: {
    store() {
      this.form.post('/settings/plugins/typeform')
    },
  },
}
</script>
