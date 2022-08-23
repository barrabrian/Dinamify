<template>
  <div>
    <Head title="Novo Entregável" />

    <form @submit.prevent="store">

        <div class="shadow-lg bg-gray-100 border border-gray-300 rounded" style="min-height: calc(100vh - 150px - 3rem);">
            <div class="p-12 bg-white flex justify-between items-center">
                <div class="flex justify-center items-center">
                    <Link class="inline mr-3 group" href="/deliverables">
                        <icon name="arrow-left" class="w-7 h-7 fill-gray-700 group-hover:fill-gray-600 inline" />
                    </Link>
                    <h1 class="text-3xl font-bold inline">Novo Entregável</h1>
                </div>
                <div class="flex gap-4">
                    <loading-button :loading="form.processing" class="btn-emerald px-8" type="submit">
                        Salvar
                    </loading-button>
                </div>
            </div>

            <div class="p-8 grid gap-8 grid-cols-5">
                <!-- <div class="col-span-3 bg-white rounded-md shadow overflow-hidden">
                    <div class="flex flex-wrap -mb-8 -mr-6 p-8">
                        <text-area-input v-model="form.region" :error="form.errors.region" class="pb-8 pr-6 w-full" label="Código HTML" />
                    </div>
                    
                </div> -->
                <div class="col-span-3 ">
                    <div class="flex justify-between items-center gap-4">
                        <text-input v-model="form.name" :error="form.errors.name" class="pb-4 w-full" placeholder="Nome do Entregável" label="Nome do Entregável" />
                        <select-input v-model="form.form_id" :error="form.errors.form_id" class="pb-4 w-full " label="Origem de Dados" >
                            <option :value="null" >Selecionar</option>
                            <option v-for="(formu, index) in forms" :key="index" :value="formu.id">{{ formu.title }}</option>
                        </select-input>
                    </div>
                    <div class="w-100 bg-gray-50 rounded-md shadow border border-gray-200 ">
                        <div class="flex justify-between items-center py-2 px-3 border-b">
                            <div class="flex flex-wrap items-center divide-gray-200 sm:divide-x">
                                <div class="flex items-center space-x-1 sm:pr-4">
                                    <button type="button" class="p-2 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100"  @click="showVars = true">
                                        <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"></path></svg>
                                    </button>
                                    <button type="button" class="p-2 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100"  @click="showImgs = true">
                                        <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path></svg>
                                    </button>
                                    <button type="button" class="p-2 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100"  @click="showHF = true">
                                        <icon name="header-footer" class="w-5 h-5 fill-gray-700 group-hover:fill-gray-600 inline" />
                                    </button>
                                </div>
                                <div class="flex flex-wrap items-center space-x-1 sm:pl-4">
                                    <button type="button" class="p-2 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600" @click="addPageBreak">
                                        <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                    </button>
                                </div>
                            </div>
                            <div id="text-helper" class="text-xs italic text-gray-400">
                                Ajuda
                            </div>
                        </div>
                        <div class="py-2 px-4 bg-white rounded-b-lg dark:bg-gray-800">
                            <textarea v-model="form.html" :error="form.errors.html" rows="20" class="block px-0 w-full text-sm text-gray-800 bg-white border-0 focus:ring-0 h-full m-0" placeholder="Crie seu pdf em HTML aqui..." required @keyup="preview" @keydown.tab.prevent />
                        </div>
                    </div>
                </div>

                <div class="col-span-2">
                    <div class="font-bold text-xl">Preview</div>
                    <div class="bg-white w-100 rounded-md shadow overflow-auto mt-2 p-8" style="height: calc(100vh - 350px - 6rem);" >
                        <iframe id="preview" style="width:100%; overflow: hidden;" frameborder="0"></iframe>
                    </div>
                </div>
            </div>

        </div>

        <sidebar-modal :show="showHF" @close="showHF = false">
            <div>
                <div class="flex justify-between items-center mb-6">
                    <h5 class="text-2xl font-bold">Cabeçalho e Rodapé:</h5>
                </div>
                <div class="flex flex-col border-dashed border-2 border-gray-300 rounded bg-gray-100 p-4">
                    <text-area-input v-model="form.header" :error="form.errors.header" class="pb-4 w-full " placeholder="Crie o cabeçalho em HTML aqui..." label="Cabeçalho" />
                </div>
                <div class="flex mt-4 flex-col border-dashed border-2 border-gray-300 rounded bg-gray-100 p-4">
                    <text-area-input v-model="form.footer" :error="form.errors.footer" class="pb-4 w-full " placeholder="Crie o rodapé em HTML aqui..." label="Rodapé" />
                </div>

            </div>
        </sidebar-modal>

    </form>

     <sidebar-modal :show="showVars" @close="showVars = false">
        <div>
            <h5 class="text-xl font-bold mb-6">Variáveis Personalizadas:</h5>
            <div v-for="(variable, index) in variables" :key="index" class="flex items-center space-x-4 rounded bg-gray-100 shadow p-4 mb-4">
                <div class="flex-shrink-0">
                    <icon v-if="variable.type == 'statement'" name="text" class="w-8 h-8 fill-gray-400" />
                    <icon v-else-if="variable.type == 'multiple_choice' || variable.type == 'yes_no' || variable.type == 'picture_choice' || variable.type == 'opinion_scale' || variable.type == 'rating' || variable.type == 'group'" name="diagram" class="w-8 h-8 fill-gray-400" />
                    <icon v-else name="input" class="w-8 h-8 fill-gray-400" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-gray-500 mb-1">
                        {{ '{{question[' + variable.qid }}]}}
                    </p>
                    <p class="text-xs font-medium text-gray-900 ">
                        {{ variable.title }}
                    </p>
                </div>
                <div class="inline-flex cursor-pointer items-center text-base font-semibold text-gray-900 " @click="copyTxt('{{question['+variable.qid +']}}')">
                    Copiar
                </div>
            </div>
            <div v-if="variables.length === 0 || !variables.length" class="flex flex-col justify-center items-center border-dashed border-2 border-gray-300 rounded bg-gray-100 p-6">
                <div class="text-gray-500 mb-1 text-center text-sm">Não tem nada aqui ainda :/</div>
                <div class="font-bold text-gray-700 text-center text-lg">Tente selecionar outro formulário</div>
            </div>
        </div>
    </sidebar-modal>

    <sidebar-modal :show="showImgs" @close="showImgs = false">
        <div>
            <div class="flex justify-between items-center mb-6">
                <h5 class="text-2xl font-bold">Imagens:</h5>
                <button type="button" class="btn-emerald px-8" @click="showAddImg = true">
                    Adicionar
                </button>
            </div>
            <div class="grid gap-4 grid-cols-2">
                <div v-for="(image, index) in images" :key="index" class=" rounded shadow bg-gray-100">
                    <img class="object-cover w-full h-32 rounded-t" :src="image.image_path" >
                    <div class="flex items-center justify-center p-2">
                        <div class="cursor-pointer items-center text-base font-semibold text-gray-900 " @click="copyTxt('<img src=&quot;'+ image.image_path + '&quot; alt=&quot;imagem&quot;>')">
                            Copiar
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="images.length === 0" class="flex flex-col justify-center items-center border-dashed border-2 border-gray-300 rounded bg-gray-100 p-6">
                <div class="text-gray-500 mb-1 text-center text-sm">Não tem nada aqui ainda :/</div>
                <div class="font-bold text-gray-700 text-center text-lg">Tente selecionar outro formulário</div>
            </div>
            <!-- <pagination class="mt-6" :links="images.links" /> -->

        </div>
    </sidebar-modal>

    <add-image-modal :show="showAddImg" @close="showAddImg = false"></add-image-modal>


    

  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import TextAreaInput from '@/Shared/TextAreaInput'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import SidebarModal from '@/Shared/SidebarModal'
import AddImageModal from '@/Shared/AddImageModal'

export default {
  components: {
    Head,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
    TextAreaInput,
    Icon,
    SidebarModal,
    AddImageModal,
  },
  layout: Layout,
  remember: 'form',
  props: {
    forms: Array,
    images: Array,
  },
  data() {
    return {
      form: this.$inertia.form({
        name: null,
        html: '',
        header: '',
        footer: '',
        form_id: null,
      }),
      variables: {},
      showVars: false,
      showImgs: false,
      showHF: false,
      showAddImg: false,
    }
  },
  watch: {
    'form.form_id' : function (value) {
        axios.get('/api/questions?form_id=' + value)
        .then (response => {
            this.variables = response.data;
            // console.log(this.variables);
        })
    },
  },
  methods: {
    store() {
      this.form.post('/deliverables')
    },
    preview() {
        let iframe = document.getElementById("preview");
        iframe.contentWindow.document.head.innerHTML = '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
        iframe.contentWindow.document.body.innerHTML = '<style>.page-break{margin: 4em 0; padding:2em; background: gray;}body{max-width:100vw;}img{max-width:100%;}</style>'+ this.form.html;
        iframe.height = iframe.contentWindow.document.body.scrollHeight;
    },
    addPageBreak(){
        this.form.html = this.form.html + '\n<div class="page-break"></div>';
        this.preview();
        this.$page.props.flash.success = "Quebra de Página adicionada!";
        setTimeout(() => {
            this.$page.props.flash.success = null;
        },1500);
    },
    async copyTxt(mytext) {
        try {
            await navigator.clipboard.writeText(mytext);
            this.showVars = false;
            this.showImgs = false;
            // console.log(this.$page.props.flash);
            this.$page.props.flash.success = "Item Copiado!";
            setTimeout(() => {
                this.$page.props.flash.success = null;
            },1500);
        } catch($e) {
            console.log('Cannot copy: ' + $e);
            this.$page.props.flash.success = "Erro ao Copiar.";
        }
    },
  },
}
</script>
