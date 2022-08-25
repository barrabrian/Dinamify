<template>
  <div>
    <Head :title="form.name" />

    <form @submit.prevent="update">

        <div class="shadow-lg bg-gray-100 border border-gray-300 rounded relative" style="min-height: calc(100vh - 150px - 3rem);">
            <div class="p-12 bg-white flex justify-between items-center">
                <div class="flex justify-center items-center">
                    <Link class="inline mr-3 group" href="/">
                        <icon name="arrow-left" class="w-7 h-7 fill-gray-700 group-hover:fill-gray-600 inline" />
                    </Link>
                    <h1 class="text-3xl font-bold inline">{{ form.name }}</h1>
                </div>
                <div class="flex gap-4">
                    <!-- <Link class="btn-gray px-8" :href="`/flows/${flow.id}/execute`">
                        Executar
                    </Link> -->
                    <div v-if="responses_on_standby.length > 0" class="flex items-center gap-2 border-dashed border-2 border-yellow-300 rounded bg-yellow-100 pl-4">
                      <icon name="warning" class="w-4 h-4 fill-gray-500 inline mr-1" />
                      <span class="italic text-xs">Existem {{ responses_on_standby.length }} Respostas Não Processadas</span>
                      <span class="font-bold text-xs cursor-pointer py-2 px-4" @click="showResponses = true">Ver</span>
                    </div>
                    <dropdown class="" placement="bottom-end">
                      <template #default>
                        <div class="inline btn-gray px-8">
                          Executar
                        </div>
                      </template>
                      <template #dropdown>
                        <div class="mt-1 p-2 text-sm bg-white rounded shadow-xl ">
                          <div class="block w-full text-left px-6 py-2 rounded-lg hover:text-white hover:bg-indigo-900 font-bold cursor-pointer" @click="showTest = true">
                            Um Teste
                          </div>
                          <div class="block w-full text-left px-6 py-2 rounded-lg hover:text-white hover:bg-indigo-900 font-bold cursor-pointer" @click="showExec = true">
                            Uma Resposta Específica
                          </div>
                          <Link class="block text-left px-6 py-2 rounded-lg hover:text-white hover:bg-indigo-900 font-bold" :href="`/flows/${flow.id}/execute`">
                            Todas as Respostas
                          </Link>
                        </div>
                      </template>
                    </dropdown>
                    <loading-button :loading="form.processing" class="btn-emerald px-8" type="submit">
                        Salvar
                    </loading-button>
                </div>
            </div>

            
            <div class="p-8 grid gap-4 grid-cols-3">
                <div class="col-span-3 ">
                    <text-input v-model="form.name" :error="form.errors.name" class="pb-4 w-full" placeholder="Nome do Fluxo" label="Nome do Fluxo" />
                </div>
                <div class="col-span-1 ">
                    <div class="w-100 bg-white rounded-md shadow border border-gray-200">
                       <div class="font-bold p-4 bg-gray-50 border-b border-1 border-gray-200">Passo 1</div>
                       <div class="p-8 ">
                        <select-input v-model="form.typeform_id" :error="form.errors.form_id" class=" w-full " label="Selecione o Gatilho de Entrada">
                            <option v-for="(typeform, index) in typeform_plugins" :key="index" :value="typeform.id">{{ typeform.name }}</option>
                        </select-input>
                       </div>
                    </div>
                </div>
                <div class="col-span-1 ">
                    <div class="w-100 bg-white rounded-md shadow border border-gray-200">
                       <div class="font-bold p-4 bg-gray-50 border-b border-1 border-gray-200">Passo 2</div>
                       <div class="p-8 ">
                        <select-input v-model="form.deliverable_id" :error="form.errors.deliverable_id" class=" w-full " label="Selecione o Entregável">
                            <option v-for="(deliverable, index) in deliverables" :key="index" :value="deliverable.id">{{ deliverable.name }}</option>
                        </select-input>
                       </div>
                    </div>
                </div>
                <div class="col-span-1 ">
                    <div class="w-100 bg-white rounded-md shadow border border-gray-200">
                       <div class="font-bold p-4 bg-gray-50 border-b border-1 border-gray-200">Passo 3</div>
                       <div class="p-8 flex flex-col gap-6">
                        <select-input v-model="form.active_campaign_id" :error="form.errors.active_campaign_id" class=" w-full " label="Selecione o Método de Envio">
                            <option v-for="(active_campaign_plugin, index) in active_campaign_plugins" :key="index" :value="active_campaign_plugin.id" >{{ active_campaign_plugin.name }}</option>
                        </select-input>
                        <select-input v-model="form.email_question_id" :error="form.errors.email_question_id" class=" w-full " label="Identifique o Campo de Email">
                            <option v-for="(variable, index) in variables" :key="index" :value="variable.id" >{{ variable.title }}</option>
                        </select-input>
                       </div>
                    </div>
                </div>

            </div>

            <div class="px-12 py-4 border-t border-gray-300 bg-gray-200 flex justify-between items-center gap-6 absolute bottom-0 w-full">
                <span class="italic">Entregáveis Gerados: {{ ebooks.length }}</span>
                <span class="btn-gray text-xs cursor-pointer py-2 px-4" @click="showEbooks = true">Ver Entregáveis</span>
            </div>

        </div>

    </form>

    <sidebar-modal :show="showTest" @close="showTest = false">
        <div>
            <div class="mb-6">
                <h5 class="text-2xl font-bold">Teste:</h5>
            </div>
            <div v-if="form.email_question_id !== null" class="flex flex-col justify-center items-center border-dashed border-2 border-gray-300 rounded bg-gray-100 p-6">
                <select-input class="pb-4 w-full " v-model="testing.response_id" :error="testing.errors.response_id" label="Selecione a Resposta" >
                    <option v-for="(response, index) in responses" :key="index" :value="response.response_id">{{ response.value }}</option>
                </select-input>
                <text-input v-model="testing.email" :error="testing.errors.email" class="w-full pb-4" placeholder="" label="Email" />
                <loading-button :loading="testing.processing" type="button" class="btn-emerald cursor-pointer mt-2 w-full text-center" @click="test">
                    Testar
                </loading-button>
            </div>
            <div v-else class="flex flex-col justify-center items-center border-dashed border-2 border-gray-300 rounded bg-gray-100 p-6">
                <div class="text-gray-500 text-center text-sm">Identifique o Campo de Email no Passo 3</div>
            </div>
            <div v-if="testing.return.includes('http')" class="flex flex-col justify-center border-dashed border-2 border-gray-300 rounded bg-gray-100 p-6 mt-4">
                <p>Link:</p>
                <a class="flex flex-row justify-between items-center" :href="testing.return" target="_blank" rel="noopener noreferrer">
                  <div class="text-gray-500 mt-2 text-xs truncate inline w-80 underline">{{ testing.return }}</div>
                  <icon name="preview" class="w-4 h-4 fill-gray-400 ml-2" />
                </a>
            </div>
        </div>
    </sidebar-modal>

    <sidebar-modal :show="showExec" @close="showExec = false">
        <div>
            <div class="mb-6">
                <h5 class="text-2xl font-bold">Executar:</h5>
            </div>
            <div v-if="form.email_question_id !== null" class="flex flex-col justify-center items-center border-dashed border-2 border-gray-300 rounded bg-gray-100 p-6">
                <select-input class="pb-4 w-full " v-model="execute.response_id" :error="execute.errors.response_id" label="Selecione a Resposta" >
                    <option v-for="(response, index) in responses" :key="index" :value="response.response_id">{{ response.value }}</option>
                </select-input>
                <loading-button :loading="execute.processing" type="button" class="btn-emerald cursor-pointer mt-2 w-full text-center" @click="executeOnly(execute.response_id)">
                    Executar
                </loading-button>
            </div>
            <div v-else class="flex flex-col justify-center items-center border-dashed border-2 border-gray-300 rounded bg-gray-100 p-6">
                <div class="text-gray-500 text-center text-sm">Identifique o Campo de Email no Passo 3</div>
            </div>
            <div v-if="execute.return.includes('http')" class="flex flex-col justify-center border-dashed border-2 border-gray-300 rounded bg-gray-100 p-6 mt-4">
                <p>Link:</p>
                <a class="flex flex-row justify-between items-center" :href="execute.return" target="_blank" rel="noopener noreferrer">
                  <div class="text-gray-500 mt-2 text-xs truncate inline w-80 underline">{{ execute.return }}</div>
                  <icon name="preview" class="w-4 h-4 fill-gray-400 ml-2" />
                </a>
            </div>
        </div>
    </sidebar-modal>

    <sidebar-modal :show="showEbooks" @close="showEbooks = false">
        <div>
            <div class="mb-6">
                <h5 class="text-2xl font-bold">Entregáveis Gerados:</h5>
            </div>

            <div v-for="(ebook, index) in ebooks" :key="index" class="flex items-center space-x-4 rounded bg-gray-100 shadow p-4 mb-4">
                <div class="flex-shrink-0">
                    <icon name="pdf" class="w-8 h-8 fill-gray-400" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs italic text-gray-500 mb-1">
                        {{ ebook.created_at }} 
                    </p>
                    <p class="text-sm font-medium text-gray-900 ">
                        {{ ebook.email }} 
                    </p>
                </div>
                <a class="inline-flex cursor-pointer items-center text-base font-semibold text-gray-900 " :href="ebook.pdf_path" target="_blank" rel="noopener noreferrer">
                    Ver
                </a>
            </div>
            <div v-if="ebooks.length === 0 || !ebooks.length" class="flex flex-col justify-center items-center border-dashed border-2 border-gray-300 rounded bg-gray-100 p-6">
                <div class="text-gray-500 text-center text-sm">Não tem nada aqui ainda :/</div>
            </div>
        </div>
    </sidebar-modal>

    <sidebar-modal :show="showResponses" @close="showResponses = false">
        <div>
            <div class="mb-6">
                <h5 class="text-2xl font-bold">Respostas Não Processadas:</h5>
            </div>
            <div v-if="flow.email_question_id">
              <div v-for="(response, index) in responses_on_standby" :key="index" class="flex items-center space-x-4 rounded bg-gray-100 shadow p-4 mb-4">
                  <div class="flex-shrink-0">
                      <icon name="warning" class="w-8 h-8 fill-gray-400" />
                  </div>
                  <div class="flex-1 min-w-0">
                      <p class="text-xs italic text-gray-500 mb-1">
                          {{ response.created_at }} 
                      </p>
                      <p class="text-sm font-medium text-gray-900 ">
                          {{ response.email }} 
                      </p>
                  </div>
                  <loading-button :loading="execute.processing" class="inline-flex cursor-pointer items-center text-base font-semibold text-gray-900 spinner-dark" @click="executeOnly(response.id);reload()">
                      <!-- <Link :href="`/flows/${flow.id}/execute/${response.id}`">Executar</Link> -->
                      Executar
                  </loading-button>
              </div>
            </div>
            <div v-else class="flex flex-col justify-center items-center border-dashed border-2 border-gray-300 rounded bg-gray-100 p-6">
                <div class="text-gray-500 text-center text-sm">Primeiro Identifique o Campo de Email no Passo 3</div>
            </div>
        </div>
    </sidebar-modal>


    

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
import Dropdown from '@/Shared/Dropdown'
import SidebarModal from '@/Shared/SidebarModal'

export default {
  components: {
    Head,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
    TextAreaInput,
    Icon,
    Dropdown,
    SidebarModal,
  },
  layout: Layout,
  remember: 'form',
  props: {
    typeform_plugins: Array,
    deliverables: Array,
    active_campaign_plugins: Array,
    flow: Object,
    ebooks: Array,
    responses_on_standby: Array,
  },
  data() {
    return {
      form: this.$inertia.form({
        name: this.flow.name,
        typeform_id: this.flow.typeform_id,
        deliverable_id: this.flow.deliverable_id,
        active_campaign_id: this.flow.active_campaign_id,
        email_question_id: this.flow.email_question_id,
      }),
      variables: {},
      responses: {},
      showTest: false,
      showEbooks: false,
      showExec: false,
      showResponses: false,
      testing: {
        response_id: null,
        flow_id: this.flow.id,
        email: '',
        return: '',
        processing : false,
        errors: {
          email: null,
          response_id: null,
        },
      },
      execute: {
        response_id: null,
        flow_id: this.flow.id,
        return: '',
        processing : false,
        errors: {
          response_id: null,
        },
      },
    }
  },
  mounted() {
    axios.get('/api/questions?deliverable_id=' + this.form.deliverable_id)
    .then (response => {
        this.variables = response.data;
        if (this.variables.length == 0){
          this.$page.props.flash.error = "Parece que não há um formulário atrelado a este entregável.";
          setTimeout(() => {
              this.$page.props.flash.error = null;
          },5000);
        } else {
          this.$page.props.flash.error = null;
        }
    });
    if (this.form.email_question_id !== null) {
      axios.get('/api/responses?question_id=' + this.flow.email_question_id)
      .then (response => {
          this.responses = response.data;
      });
    } else {
      this.form.errors.email_question_id = "Este campo é obrigatório.";
    }
  },
  watch: {
    'form.deliverable_id' : function (value) {
        axios.get('/api/questions?deliverable_id=' + value)
        .then (response => {
            this.variables = response.data;
            if (this.variables.length == 0){
              this.$page.props.flash.error = "Parece que não há um formulário atrelado a este entregável.";
              setTimeout(() => {
                  this.$page.props.flash.error = null;
              },5000);
            } else {
              this.$page.props.flash.error = null;
            }
            // console.log(this.variables);
        })
    },
    'form.email_question_id' : function (value) {
      if (this.form.email_question_id !== null) {
        this.form.errors.email_question_id = undefined;
        axios.get('/api/responses?question_id=' + value)
        .then (response => {
            this.responses = response.data;
        });
      }
    }
  },
  methods: {
    update() {
      this.form.put(`/flows/${this.flow.id}`)
    },
    destroy() {
      if (confirm('Tem certeza que deseja deletar este fluxo?')) {
        this.$inertia.delete(`/flows/${this.flow.id}`)
      }
    },
    restore() {
      if (confirm('Tem certeza que deseja restaurar este fluxo?')) {
        this.$inertia.put(`/flows/${this.flow.id}/restore`)
      }
    },
    async test(){
      this.testing.processing = true;
      if (this.testing.response_id !== undefined && this.testing.response_id !== null && this.testing.email !== '') {
        axios.get('/api/flows/test?flow_id=' + this.flow.id + '&ans_id=' + this.testing.response_id + '&email=' + this.testing.email)
        .then (response => {
            this.testing.return = response.data;
            this.testing.processing = false;
            if (this.testing.return.includes('http')) {
              this.$page.props.flash.success = 'Teste do Fluxo executado!';
              setTimeout(() => {
                  this.$page.props.flash.success = null;
              },5000);
            } else {
              this.$page.props.flash.error = this.testing.return;
              setTimeout(() => {
                  this.$page.props.flash.error = null;
              },5000);
            }
        });
      } else {
        this.testing.processing = false;
        this.$page.props.flash.error = "Preencha os todos os campos!";
        if (this.testing.email == ''){
          this.testing.errors.email = 'Este campo é obrigatório';
        } 
        if (this.testing.response_id == undefined || this.testing.response_id == null) {
          this.testing.errors.response_id = 'Este campo é obrigatório';
        }
        setTimeout(() => {
            this.$page.props.flash.error = null;
        },5000);
      }
    },
    async executeOnly(response_id){
      this.execute.processing = true;
      if (response_id !== undefined && response_id !== null && response_id !== '') {
        try {
           axios.get('/api/flows/execute?flow_id=' + this.execute.flow_id + '&ans_id=' + response_id)
          .then (response => {
              this.execute.return = response.data;
              this.execute.processing = false;
              if (this.execute.return.includes('http')) {
                this.$page.props.flash.success = 'Fluxo executado!';
                setTimeout(() => {
                    this.$page.props.flash.success = null;
                },5000);
              } else {
                this.$page.props.flash.error = this.execute.return;
                setTimeout(() => {
                    this.$page.props.flash.error = null;
                },5000);
              }
          });
        } catch (error) {
          this.$page.props.flash.error = "Erro: O Entregável possui variáveis de múltiplas Origens de Dados";
          console.log(error);
          setTimeout(() => {
              this.$page.props.flash.error = null;
          },5000);
        }
       
      } else {
        this.execute.processing = false;
        this.$page.props.flash.error = "Preencha os todos os campos!";
        this.execute.errors.response_id = 'Este campo é obrigatório';
        setTimeout(() => {
            this.$page.props.flash.error = null;
        },5000);
      }
    },
    reload(){
      if (!this.$page.props.flash.error){
        window.location.reload();
      }
    }
  },
}
</script>
