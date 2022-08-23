<template>
  <div>
    <Head title="Novo Fluxo" />

    <form @submit.prevent="store">

        <div class="shadow-lg bg-gray-100 border border-gray-300 rounded" style="min-height: calc(100vh - 150px - 3rem);">
            <div class="p-12 bg-white flex justify-between items-center">
                <div class="flex justify-center items-center">
                    <Link class="inline mr-3 group" href="/">
                        <icon name="arrow-left" class="w-7 h-7 fill-gray-700 group-hover:fill-gray-600 inline" />
                    </Link>
                    <h1 class="text-3xl font-bold inline">Novo Fluxo</h1>
                </div>
                <div class="flex gap-4">
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
                        <select-input v-model="form.typeform_id" :error="form.errors.typeform_id" class=" w-full " label="Selecione o Gatilho de Entrada">
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
                            <option v-for="(deliverable, index) in deliverables" :key="index" :value="deliverable.id" >{{ deliverable.name }}</option>
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

        </div>

    </form>


    

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

export default {
  components: {
    Head,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
    TextAreaInput,
    Icon,
  },
  layout: Layout,
  remember: 'form',
  props: {
    typeform_plugins: Object,
    deliverables: Object,
    active_campaign_plugins: Object,
  },
  data() {
    return {
      form: this.$inertia.form({
        name: null,
        typeform_id: null,
        deliverable_id: null,
        active_campaign_id: null,
        email_question_id: null,
      }),
      variables: {},
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
            // console.log(this.variables.length);
        })
    },
  },
  methods: {
    store() {
      this.form.post('/flows')
    },
  },
}
</script>
