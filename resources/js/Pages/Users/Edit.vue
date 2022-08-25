<template>
  <div>
    <Head :title="`${form.first_name} ${form.last_name}`" />
    

    <div class="shadow-lg grid grid-cols-12 grid-flow-col border border-gray-300 rounded h-screen" style="height: calc(100vh - 150px - 3rem);">
        <div class="bg-gray-200 border-r-2 border-gray-300 shadow-lg">
          <div class="flex flex-col justify-center items-top">
            <Link class="group flex justify-center py-8 items-center  w-full border-b border-gray-300" href="/settings/plugins">
              <icon name="plugin" class="w-8 h-8 fill-gray-700 group-hover:fill-gray-900" />
            </Link>
            <Link class="group flex justify-center py-8 items-center  w-full border-b border-gray-300" href="/settings/images">
              <icon name="image" class="w-8 h-8 fill-gray-700 group-hover:fill-gray-900" />
            </Link>
            <Link class="group flex justify-center py-8 items-center bg-white w-full border-b border-gray-300" href="/settings/users">
              <icon name="person" class="w-8 h-8 fill-gray-700 group-hover:fill-gray-900" />
            </Link>
            <Link class="group flex justify-center py-8 items-center  w-full border-b border-gray-300" href="#">
              <icon name="config" class="w-8 h-8 fill-gray-700 group-hover:fill-gray-900" />
            </Link>
          </div>
        </div>
        
        <form class="col-span-11 bg-gray-100" @submit.prevent="update">
          <div class="p-12 bg-white flex justify-between items-center">
            <div class="flex justify-center items-center">
              <Link class="inline mr-3 group" href="/settings/users">
                <icon name="arrow-left" class="w-7 h-7 fill-gray-700 group-hover:fill-gray-600 inline" />
              </Link>
              <h1 class="text-3xl font-bold inline">{{ form.first_name }} {{ form.last_name }}</h1>
              <img v-if="user.photo" class="block ml-4 w-8 h-8 rounded-full" :src="user.photo" />
            </div>
            <div class="flex justify-center items-center gap-6">
              <button v-if="!user.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Deletar</button>
              <loading-button :loading="form.processing" class="btn-emerald px-12" type="submit">Salvar</loading-button>
            </div>
          </div>
          <div class="p-12 border-t border-gray-300 ">
              <trashed-message v-if="user.deleted_at" class="mb-6" @restore="restore"> Este usuário foi deletado. </trashed-message>
              <div class="max-w-3xl overflow-hidden">
                  <div class="flex flex-wrap -mb-8 -mr-6 ">
                    <text-input v-model="form.first_name" :error="form.errors.first_name" class="pb-8 pr-6 w-full lg:w-1/2" label="Primeiro nome" />
                    <text-input v-model="form.last_name" :error="form.errors.last_name" class="pb-8 pr-6 w-full lg:w-1/2" label="Sobrenome" />
                    <text-input v-model="form.email" :error="form.errors.email" class="pb-8 pr-6 w-full lg:w-1/2" label="Email" />
                    <text-input v-model="form.password" :error="form.errors.password" class="pb-8 pr-6 w-full lg:w-1/2" type="password" autocomplete="new-password" label="Senha" />
                    <select-input v-model="form.owner" :error="form.errors.owner" class="pb-8 pr-6 w-full lg:w-1/2" label="Permissão">
                      <option :value="false">Colaborador</option>
                      <option v-if="this.$page.props.auth.user.owner || form.owner" :value="true">Admin</option>
                    </select-input>
                    <file-input v-model="form.photo" :error="form.errors.photo" class="pb-8 pr-6 w-full lg:w-1/2" type="file" accept="image/*" label="Foto" />
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
import FileInput from '@/Shared/FileInput'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import TrashedMessage from '@/Shared/TrashedMessage'
import Icon from '@/Shared/Icon'

export default {
  components: {
    FileInput,
    Head,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage,
    Icon,
  },
  layout: Layout,
  props: {
    user: Object,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        _method: 'put',
        first_name: this.user.first_name,
        last_name: this.user.last_name,
        email: this.user.email,
        password: '',
        owner: this.user.owner,
        photo: null,
      }),
    }
  },
  methods: {
    update() {
      this.form.post(`/users/${this.user.id}`, {
        onSuccess: () => this.form.reset('password', 'photo'),
      })
    },
    destroy() {
      if (confirm('Tem certeza de que quer deletar este usuário?')) {
        this.$inertia.delete(`/users/${this.user.id}`)
      }
    },
    restore() {
      if (confirm('Tem certeza de que quer restaurar este usuário?')) {
        this.$inertia.put(`/users/${this.user.id}/restore`)
      }
    },
  },
}
</script>
