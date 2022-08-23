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
          <Link class="group flex justify-center py-8 items-center  w-full border-b border-gray-300" href="#">
            <icon name="person" class="w-8 h-8 fill-gray-700 group-hover:fill-gray-900" />
          </Link>
          <Link class="group flex justify-center py-8 items-center  w-full border-b border-gray-300" href="#">
            <icon name="config" class="w-8 h-8 fill-gray-700 group-hover:fill-gray-900" />
          </Link>
        </div>
      </div>
      
      <div class="col-span-11 bg-gray-100">
        <div class="p-12 bg-white flex justify-between items-center">
          <h1 class="text-3xl font-bold">Integrações</h1>
          <div>
            <!-- <Link class="inline btn-gray mr-2" href="settings/plugins/typeform/sync">
              Sincronizar
            </Link> -->
            <dropdown class="" placement="bottom-end">
              <template #default>
                <div class="inline btn-emerald">
                  Adicionar
                </div>
              </template>
              <template #dropdown>
                <div class="mt-3 p-2 text-sm bg-white rounded shadow-xl ">
                  <Link class="block w-full text-left px-6 py-2 rounded-lg hover:text-white hover:bg-indigo-900 font-bold" href="/settings/plugins/typeform/create">
                    <icon name="typeform" class="mr-1 w-4 h-4 bg-white inline rounded " />
                    Typeform
                  </Link>
                  <Link class="block text-left px-6 py-2 rounded-lg hover:text-white hover:bg-indigo-900 font-bold" href="/settings/plugins/activecampaign/create">
                    <icon name="activecampaign" class="mr-1 w-4 h-4 bg-white inline rounded " />
                    Active Campaign
                  </Link>
                </div>
              </template>
            </dropdown>
          </div>

        </div>
        <div class="p-12 border-t border-gray-300 ">
          <search-filter v-model="form.search" class="mb-8" @reset="reset">
            <label class="block text-gray-700">Status:</label>
            <select v-model="form.trashed" class="form-select mt-1 w-full">
              <option :value="null">Ativo</option>
              <option value="only">Inativo</option>
              <option value="with">Todos</option>
            </select>
          </search-filter>

          <div class="grid gap-4">

            <div v-for="(active_campaign_plugin, index) in active_campaign_plugins.data" :key="index" class="flex justify-between items-center border border-gray-300 rounded" :class="active_campaign_plugin.deleted_at ? 'bg-gray-200' : 'bg-white'">
              <div class="flex justify-start items-center p-6 ">
                <div>
                  <icon name="activecampaign" class="mr-4 w-12 h-12" />
                </div>
                <div>
                  <div class="text-xs text-gray-400 mb-1">ActiveCampaign</div>
                  <div class="font-bold">{{ active_campaign_plugin.name }}</div>
                </div>
              </div>
              <div class="flex justify-center items-center p-6">
                <span class="inline-block mr-6 w-3 h-3 rounded-full" :class="active_campaign_plugin.deleted_at ? 'bg-red-700' : 'bg-green-600'"></span>
                <Link class="btn-gray block" :href="`/settings/plugins/activecampaign/${active_campaign_plugin.id}/edit`">
                  Editar
                </Link>
              </div>
            </div>

            <div v-for="(typeform_plugin, index) in typeform_plugins.data" :key="index" class="flex justify-between items-center border border-gray-300 rounded" :class="typeform_plugin.deleted_at ? 'bg-gray-200' : 'bg-white'">
              <div class="flex justify-start items-center p-6 ">
                <div>
                  <icon name="typeform" class="mr-4 w-12 h-12" />
                </div>
                <div>
                  <div class="text-xs text-gray-400 mb-1">Typeform</div>
                  <div class="font-bold">{{ typeform_plugin.name }}</div>
                </div>
              </div>
              <div class="flex justify-center items-center p-6">
                <span class="inline-block mr-6 w-3 h-3 rounded-full" :class="typeform_plugin.deleted_at ? 'bg-red-700' : 'bg-green-600'"></span>
                <Link class="btn-gray block" :href="`/settings/plugins/typeform/${typeform_plugin.id}/edit`">
                  Editar
                </Link>
              </div>
            </div>

            <!-- <div class="flex justify-between items-center border border-gray-300 rounded bg-gray-200">
              <div class="flex justify-start items-center p-6">
                <div>
                  <icon name="activecampaign" class="mr-4 w-12 h-12" />
                </div>
                <div>
                  <div class="text-xs text-gray-400 mb-1">Active Campaign</div>
                  <div class="font-bold">Um Canal de Luz</div>
                </div>
              </div>
              <div class="flex justify-center items-center p-6">
                <span class="inline-block w-3 h-3 mr-6 bg-red-700 rounded-full"></span>
                <Link class="btn-gray block" href="#">
                  Editar
                </Link>
              </div>
            </div> -->

            <div v-if="typeform_plugins.data.length === 0 && active_campaign_plugins.data.length === 0" class="flex flex-col justify-center items-center border-dashed border-2 border-gray-300 rounded bg-gray-100 p-6">
              <div class="text-gray-500 mb-1 text-sm">Não tem nada aqui ainda :/</div>
              <div class="font-bold text-gray-700 text-lg">Tente adicionar algo novo</div>
            </div>

            <pagination class="mt-6" :links="typeform_plugins.links" />
        
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
import Dropdown from '@/Shared/Dropdown'



export default {
  components: {
    Head,
    Icon,
    Link,
    SearchFilter,
    Dropdown,
  },
  layout: Layout,
  props: {
    filters: Object,
    typeform_plugins: Array,
    active_campaign_plugins: Array,
    showModal: Boolean,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        trashed: this.filters.trashed,
      },
    }
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        this.$inertia.get('/settings/plugins', pickBy(this.form), { preserveState: true })
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
