<template>
  <div>
    <Head title="Login" />
    <div class="flex items-center justify-center p-6 min-h-screen bg-gray-900">
      <div class="w-full max-w-md">
        <div class="mx-auto w-full max-w-xs flex justify-center items-center">
          <logo class="inline mr-2 fill-emerald-400 h-8" />
          <span class="inline text-3xl font-bold text-white">Dinamify</span>
        </div>
        <form class="mt-8 bg-gray-50 rounded-lg shadow-xl overflow-hidden" @submit.prevent="login">
          <div class="px-10 py-12">
            <h1 class="text-center text-3xl font-bold">Bem vindo de volta!</h1>
            <div class="mt-6 mx-auto w-24 border-b-2" />
            <text-input v-model="form.email" :error="form.errors.email" class="mt-10 font-bold" label="Email" type="email" autofocus autocapitalize="off" />
            <text-input v-model="form.password" :error="form.errors.password" class="mt-6 font-bold" label="Senha" type="password" />
            <label class="flex items-center mt-6 select-none" for="remember">
              <input id="remember" v-model="form.remember" class="mr-1" type="checkbox" />
              <span class="text-sm text-gray-500">Mantenha-me conectado</span>
            </label>
            <loading-button :loading="form.processing" class="btn-emerald w-full text-center mt-6 py-4 text-xl" type="submit">Entrar</loading-button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { Head } from '@inertiajs/inertia-vue3'
import Logo from '@/Shared/Logo'
import TextInput from '@/Shared/TextInput'
import LoadingButton from '@/Shared/LoadingButton'

export default {
  components: {
    Head,
    LoadingButton,
    Logo,
    TextInput,
  },
  data() {
    return {
      form: this.$inertia.form({
        email: 'johndoe@example.com',
        password: 'secret',
        remember: false,
      }),
    }
  },
  methods: {
    login() {
      this.form.post('/login')
    },
  },
}
</script>
