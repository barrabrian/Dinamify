<template>
  <div>
    <Head title="Fluxos" />

    <div class="shadow-lg bg-gray-100 border border-gray-300 rounded" style="height: calc(100vh - 150px - 3rem);">
        <div class="p-12 bg-white flex justify-between items-center">
            <h1 class="text-3xl font-bold">Fluxos</h1>
            <div>
            <Link class="inline btn-emerald mr-2" href="flows/create">
                Adicionar
            </Link>
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

                <div v-for="(flow, index) in flows.data" :key="index" class="flex justify-between items-center border border-gray-300 rounded" :class="flow.deleted_at ? 'bg-gray-200' : 'bg-white'">
                    <div class="flex justify-start items-center p-6 ">
                        <div>
                            <icon name="bezier" class="mr-4 w-12 h-12" />
                        </div>
                        <div>
                            <div class="text-xs text-gray-400 mb-1">Fluxo</div>
                            <div class="font-bold">{{ flow.name }}</div>
                        </div>
                    </div>
                    <div class="flex justify-center items-center p-6">
                        <span class="inline-block mr-6 w-3 h-3 rounded-full" :class="flow.deleted_at ? 'bg-red-700' : 'bg-green-600'"></span>
                        <Link class="btn-gray block" :href="`/flows/${flow.id}/edit`">
                            Editar
                        </Link>
                    </div>
                </div>

                <div v-if="flows.data.length === 0" class="flex flex-col justify-center items-center border-dashed border-2 border-gray-300 rounded bg-gray-100 p-6">
                    <div class="text-gray-500 mb-1 text-sm">Não tem nada aqui ainda :/</div>
                    <div class="font-bold text-gray-700 text-lg">Tente adicionar algo novo</div>
                </div>

                <pagination class="mt-6" :links="flows.links" />
        
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
import Pagination from '@/Shared/Pagination'
import SearchFilter from '@/Shared/SearchFilter'

export default {
  components: {
    Head,
    Icon,
    Link,
    Pagination,
    SearchFilter,
  },
  layout: Layout,
  props: {
    filters: Object,
    flows: Object,
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
        this.$inertia.get('/', pickBy(this.form), { preserveState: true })
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
