<template>
  <div class="">
    <Head title="Formulários" />

    <div class="grid grid-flow-col grid-cols-9 ">
        <div class="col-span-2 bg-gray-100 border-r-2 flex flex-col h-full border-gray-00" style="">
            <search-filter v-model="form.search" class="w-full p-4 border-b border-gray-300 bg-white" @reset="reset">
                <label class="block text-gray-700">Status:</label>
                <select v-model="form.trashed" class="form-select mt-1 w-full">
                    <option :value="null">Ativo</option>
                    <option value="only">Inativo</option>
                    <option value="with">Todos</option>
                </select>
            </search-filter>
            <div class="w-full overflow-y-auto " style="height: calc(100vh - 173px);" scroll-region>
                <Link v-for="(form, index) in forms" :key="index" class="block px-6 py-6 bg-white hover:bg-gray-200 border-b border-gray-300"
                :class="isUrl(`forms/${form.fid}/view`) ? 'bg-gray-100' : ''" :href="`/forms/${form.fid}/view`">
                    <div class="flex flex-col j ustify-center items-start">
                        <div class="font-bold">
                            {{ form.title }} 
                        </div>
                        <div class="text-sm italic text-gray-400 mt-2">
                            {{ form.is_public ? 'Público' : 'Privado' }}
                        </div>
                    </div>
                </Link>
            </div>
        </div>

        <div class="col-span-7 bg-white shadow overflow-x-auto " style="max-height: calc(100vh - 96px);">
            
            <div class="p-12 text-sm bg-white w-full">
                <div class="flex justify-left items-center mb-8">
                  <Link class="inline mr-3 group" :href="`/forms/${question.form_id}/view`">
                    <icon name="arrow-left" class="w-7 h-7 fill-gray-700 group-hover:fill-gray-600 inline" />
                  </Link>
                  <h1 class="text-3xl font-bold inline">{{ question.title }}</h1>
                </div>

                <div v-if="alternatives.length > 0" class="p-4 bg-gray-100 border-dashed border-2 border-gray-300 rounded-lg">
                  <h3 class="text-lg font-bold">Alternativas</h3>

                  <div class="overflow-auto">
                    <div class="flex flex-nowrap mt-2 gap-2">
                      <div v-for="(alternative, index) in alternatives" :key="index" class=" flex-none bg-white border border-gray-300 rounded p-4" >
                          <div class="font-bold text-xs w-full">{{ alternative.label }}</div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="p-4 border border-2 border-gray-300 rounded-lg mt-6">
                  <h3 class="text-lg font-bold">Respostas</h3>

                  <div class="flex flex-col mt-4 gap-2">
                    <div v-for="(answer, index) in answers.data" :key="index" class="flex justify-between items-center bg-white border border-gray-300 rounded mr-2" >
                        <div class="flex justify-start items-center p-4 ">
                            <div>
                                <div class="italic text-gray-400 text-xs">{{ answer.type }}</div>
                                <div class="text-gray-900 text-xs">{{ answer.value }}</div>
                            </div>
                        </div>
                    </div>
                  </div>
                </div>

                <pagination class="mt-6" :links="answers.links" />

            </div>
            
            <div v-if="answers.data.length === 0" class="p-12 text-sm bg-white w-full">
                <div class="flex flex-col justify-center items-center border-dashed border-2 border-gray-300 rounded bg-gray-100 p-12">
                    <div class="text-gray-500 mb-1 text-sm">Não tem nada aqui ainda :/</div>
                    <div class="font-bold text-gray-400 text-2xl">Tente selecionar outra pergunta</div>
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
import Layout2 from '@/Shared/Layout2'
import throttle from 'lodash/throttle'
import mapValues from 'lodash/mapValues'
import SearchFilter from '@/Shared/SearchFilter'
import Pagination from '@/Shared/Pagination'

export default {
  components: {
    Head,
    Icon,
    Link,
    SearchFilter,
    Pagination,
  },
  layout: Layout2,
  props: {
    filters: Object,
    forms: Array,
    question: Object,
    answers: Array,
    alternatives: Array,
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
