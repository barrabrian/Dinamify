<template>
  <div>
    <div id="dropdown" />
    <div class="md:flex md:flex-col">
      <div class="md:flex md:flex-col md:h-screen">
        <div class="md:flex md:flex-shrink-0">
          <div class="flex items-center justify-between px-6 py-3 bg-gray-900 md:flex-shrink-0 md:justify-center">
            <Link class="text-white font-bold flex items-center justify-center" href="/">
              <logo class="inline mr-2 fill-emerald-400 w-5 h-5"/>
              <span class="">Dinamify</span> <span class="font-light text-xs ml-1">alpha</span>
            </Link>
            <dropdown class="md:hidden" placement="bottom-end">
              <template #default>
                <svg class="w-6 h-6 fill-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z" /></svg>
              </template>
              <template #dropdown>
                <div class="mt-2 px-8 py-4 bg-indigo-800 rounded shadow-lg">
                  <main-menu />
                </div>
              </template>
            </dropdown>
          </div>
          <div class="md:text-md flex items-center justify-end p-4 w-full text-sm text-white bg-gray-900 md:px-4 md:py-0">
            <div class="mr-6 mt-1 text-teal-400 text-xs">{{ auth.user.account.name }}</div>
            <dropdown class="mt-1" placement="bottom-end">
              <template #default>
                <div class="group flex items-center cursor-pointer select-none">
                  <div class="mr-1 text-white group-hover:text-emerald-500 whitespace-nowrap font-bold">
                    <span>{{ auth.user.first_name }}</span>
                    <span class="hidden md:inline">&nbsp;{{ auth.user.last_name }}</span>
                  </div>
                  <icon class="w-5 h-5 fill-white group-hover:fill-emerald-500" name="cheveron-down" />
                </div>
              </template>
              <template #dropdown>
                <div class="mt-2 p-2 text-sm bg-white rounded shadow-xl ">
                  <Link class="block px-6 py-2 rounded-lg hover:text-white hover:bg-indigo-900 font-bold" :href="`/settings/users/${auth.user.id}/edit`">Meu Perfil</Link>
                  <Link class="block px-6 py-2 rounded-lg hover:text-white hover:bg-indigo-900 font-bold" href="/settings/users">Gerenciar Usuários</Link>
                  <Link class="block px-6 py-2 rounded-lg w-full text-left hover:text-white hover:bg-indigo-900 font-bold" href="/logout" method="delete" as="button">Sair</Link>
                </div>
              </template>
            </dropdown>
          </div>
        </div>
        <main-menu class="hidden flex-shrink-0 w-full bg-gradient-to-b from-gray-600 to-gray-700 overflow-y-auto md:block border-b-2 border-t-2 border-gray-900" />
        <div class="md:flex md:flex-grow md:overflow-hidden">
          <div class="px-4 py-8 md:flex-1 md:p-12 md:overflow-y-auto bg-gray-800" scroll-region>
            <flash-messages />
            <slot />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'
import Logo from '@/Shared/Logo'
import Dropdown from '@/Shared/Dropdown'
import MainMenu from '@/Shared/MainMenu'
import FlashMessages from '@/Shared/FlashMessages'

export default {
  components: {
    Dropdown,
    FlashMessages,
    Icon,
    Link,
    Logo,
    MainMenu,
  },
  props: {
    auth: Object,
  },
}
</script>
