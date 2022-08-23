<template>
  <div class="">
    <Head title="Perguntas" />

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
                <h1 class="mb-8 text-3xl font-bold">Perguntas</h1>

                <div class="grid gap-4">

                    <div v-for="(question, index) in questions.data" :key="index" class="flex justify-between items-center border border-gray-300 rounded" >
                        <div class="flex justify-start items-center p-6 ">
                            <div>
                                <icon v-if="question.type == 'statement'" name="text" class="mr-6 w-10 h-10 fill-gray-400" />
                                <icon v-else-if="question.type == 'multiple_choice' || question.type == 'yes_no' || question.type == 'picture_choice' || question.type == 'opinion_scale' || question.type == 'rating' || question.type == 'group'" name="diagram" class="mr-6 w-10 h-10 fill-gray-400" />
                                <icon v-else name="input" class="mr-6 w-10 h-10 fill-gray-400" />
                            </div>
                            <div>
                                <div class="text-xs text-gray-400 mb-2">{{ question.type }}</div>
                                <div class="font-bold">{{ question.title }}</div>
                                <div class="text-xs text-gray-400 mt-2">{{ question.description }}</div>
                            </div>
                        </div>
                        <div class="flex justify-center items-center p-6">
                            <span class="inline-block mr-6 w-3 h-3 rounded-full" :class="question.is_required ? 'bg-green-600' : 'bg-gray-600'"></span>
                            <Link class="btn-gray block" :href="`/forms/${question.form_id}/${question.qid}/view`">
                            Ver Respostas
                            </Link>
                        </div>
                    </div>
                </div>

                <pagination class="mt-6" :links="questions.links" />

            </div>
            
            <div v-if="questions.data.length === 0" class="p-12 text-sm bg-white w-full">
                <div class="flex flex-col justify-center items-center border-dashed border-2 border-gray-300 rounded bg-gray-100 p-12">
                    <div class="text-gray-500 mb-1 text-sm">Não tem nada aqui ainda :/</div>
                    <div class="font-bold text-gray-400 text-2xl">Tente selecionar outro formulário</div>
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
    questions: Array,
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
