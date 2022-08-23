<template>
  <div>
    <div class="flex items-center justify-left flex-wrap font-bold">
      <Link class="group flex items-center py-4 px-6 border-r border-gray-900" :class="isUrl('') ? 'bg-gradient-to-b from-gray-500 to-gray-600' : ''" href="/">
        <icon name="bezier" class="mr-2 w-4 h-4" :class="isUrl('') ? 'fill-emerald-400' : 'fill-white group-hover:fill-white'" />
        <div :class="isUrl('') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Fluxos</div>
      </Link>


      <Link class="group flex items-center py-4 px-6 border-r border-gray-900" :class="isUrl('forms') ? 'bg-gradient-to-b from-gray-500 to-gray-600' : ''" href="/forms">
        <icon name="post" class="mr-2 w-4 h-4" :class="isUrl('forms') ? 'fill-emerald-400' : 'fill-white group-hover:fill-white'" />
        <div :class="isUrl('forms') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Formulários</div>
      </Link>


      <Link class="group flex items-center py-4 px-6 border-r border-gray-900" :class="isUrl('deliverables') ? 'bg-gradient-to-b from-gray-500 to-gray-600' : ''" href="/deliverables">
        <icon name="product" class="mr-2 w-4 h-4" :class="isUrl('deliverables') ? 'fill-emerald-400' : 'fill-white group-hover:fill-white'" />
        <div :class="isUrl('deliverables') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Entregáveis</div>
      </Link>


      <Link class="group flex items-center py-4 px-6 border-r border-gray-900" :class="isUrl('settings') ? 'bg-gradient-to-b from-gray-500 to-gray-600' : ''" href="/settings/plugins">
        <icon name="config" class="mr-2 w-4 h-4" :class="isUrl('settings') ? 'fill-emerald-400' : 'fill-white group-hover:fill-white'" />
        <div :class="isUrl('settings') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Configurações</div>
      </Link>
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'

export default {
  components: {
    Icon,
    Link,
  },
  methods: {
    isUrl(...urls) {
      let currentUrl = this.$page.url.substr(1)
      if (urls[0] === '') {
        return currentUrl === ''
      }
      return urls.filter((url) => currentUrl.startsWith(url)).length
    },
  },
}
</script>
