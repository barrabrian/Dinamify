<template>
  <div>
    <Head title="Users" />

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
      
      <div class="col-span-11 bg-gray-100">
        <div class="p-12 bg-white flex justify-between items-center">
          <h1 class="text-3xl font-bold">Usuários</h1>
          <div>
            <Link class="inline btn-emerald mr-2" href="/settings/users/create">
              Adicionar
            </Link>
          </div>
        </div>

        <div class="p-12 border-t border-gray-300 ">
          <div class="flex items-center justify-between mb-6">
            <search-filter v-model="form.search" class="w-full" @reset="reset">
              <label class="block text-gray-700">Permissão:</label>
              <select v-model="form.role" class="form-select mt-1 w-full">
                <option :value="null" >Todos</option>
                <option value="user">Colaborador</option>
                <option value="owner">Admin</option>
              </select>
              <label class="block mt-4 text-gray-700">Status:</label>
              <select v-model="form.trashed" class="form-select mt-1 w-full">
                <option :value="null" >Ativo</option>
                <option value="with">Todos</option>
                <option value="only">Inativo</option>
              </select>
            </search-filter>
          </div>
          <div class="bg-white rounded-md shadow overflow-x-auto">
            <table class="w-full whitespace-nowrap">
              <tr class="text-left font-bold">
                <th class="pb-4 pt-6 px-6">Nome</th>
                <th class="pb-4 pt-6 px-6">Email</th>
                <th class="pb-4 pt-6 px-6" colspan="2">Permissão</th>
              </tr>
              <tr v-for="user in users" :key="user.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
                <td class="border-t">
                  <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/settings/users/${user.id}/edit`">
                    <img v-if="user.photo" class="block -my-2 mr-2 w-5 h-5 rounded-full" :src="user.photo" />
                    {{ user.name }}
                    <icon v-if="user.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-gray-400" />
                  </Link>
                </td>
                <td class="border-t">
                  <Link class="flex items-center px-6 py-4" :href="`/settings/users/${user.id}/edit`" tabindex="-1">
                    {{ user.email }}
                  </Link>
                </td>
                <td class="border-t">
                  <Link class="flex items-center px-6 py-4" :href="`/settings/users/${user.id}/edit`" tabindex="-1">
                    {{ user.owner ? 'Admin' : 'Colaborador' }}
                  </Link>
                </td>
                <td class="w-px border-t">
                  <Link class="flex items-center px-4" :href="`/settings/users/${user.id}/edit`" tabindex="-1">
                    <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
                  </Link>
                </td>
              </tr>
              <tr v-if="users.length === 0">
                <td class="px-6 py-4 border-t" colspan="4">Nenhum usuário encontrado.</td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'
import pickBy from 'lodash/pickBy'
import Layout from '@/Shared/Layout'
import throttle from 'lodash/throttle'
import mapValues from 'lodash/mapValues'
import SearchFilter from '@/Shared/SearchFilter'

export default {
  components: {
    Head,
    Icon,
    Link,
    SearchFilter,
  },
  layout: Layout,
  props: {
    filters: Object,
    users: Array,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        role: this.filters.role,
        trashed: this.filters.trashed,
      },
    }
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        this.$inertia.get('/users', pickBy(this.form), { preserveState: true })
      }, 150),
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
  },
}
</script>
