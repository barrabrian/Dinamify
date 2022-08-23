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
      
      <form class="col-span-11 bg-gray-100" @submit.prevent="update">
        <div class="p-12 bg-white flex justify-between items-center">
          <div class="flex justify-center items-center">
            <Link class="inline mr-3 group" href="/settings/plugins">
              <icon name="arrow-left" class="w-7 h-7 fill-gray-700 group-hover:fill-gray-600 inline" />
            </Link>
            <h1 class="text-3xl font-bold inline">{{ form.name }}</h1>
          </div>

        </div>
        <div class="p-12 border-t border-gray-300 ">
          <div class="border border-gray-300 rounded ">
            <div class="flex justify-between items-center p-6  border-b border-gray-300">
              <div>
                <icon name="activecampaign" class="mr-4 w-12 h-12" />
              </div>
              <div>
                <div class="font-bold text-xl">ActiveCampaign</div>
              </div>
              <loading-button :loading="form.processing" class="btn-emerald" type="submit">
                Salvar
              </loading-button>
            </div>
            <div class=" grid grid-flow-col grid-cols-2 gap-4 p-8 font-bold">
              <text-input v-model="form.name" :error="form.errors.name" class="w-full" label="Nome" />
              <text-input v-model="form.api_url" :error="form.errors.api_url" class="w-full" label="Url da API" @blur="callApi" />
            </div>
            <div class=" grid grid-flow-col grid-cols-2 gap-4 pt-2 pb-8 px-8 font-bold">
              <text-input v-model="form.api_key" :error="form.errors.api_key" class="w-full" label="Chave da API" @blur="callApi" />
              <select-input v-model="form.deliverable_link_field_id" :error="form.errors.deliverable_link_field_id" class="w-full" label="Selecione o Campo onde o link deverá ser colocado" >
                <option :value="null" >Selecionar</option>
                <option v-for="(field, index) in fields.fields" :key="index" :value="field.id">{{ field.title }}</option>
              </select-input>
              <!-- <select-input v-model="form.deliverable_tag_id" :error="form.errors.deliverable_tag_id" class="w-full" label="Selecione a Tag que deverá ser adicionada" >
                <option :value="null" >Selecionar</option>
                <option v-for="(tag, index) in tags.tags" :key="index" :value="tag.id">{{ tag.tag }}</option>
              </select-input> -->
            </div>
            <div class="px-8 pb-8 pt-2 font-bold">
                <text-input v-model="search" :error="form.errors.deliverable_tag_id" class="w-full" label="Selecione a Tag que deverá ser adicionada" placeholder="Pesquisar..." />
                <div class="mt-4 grid grid-cols-4 gap-4 p-4 font-bold border-dashed border-2 border-gray-300 rounded">
                    <div v-for="(tag, index) in tags.tags" :key="index" class="flex items-center bg-white pl-4 rounded border border-gray-200 shadow">
                        <input :id="`bordered-radio-${tag.id}`" type="radio" :value="tag.id" v-model="form.deliverable_tag_id" name="bordered-radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-emerald-500 ">
                        <label :for="`bordered-radio-${tag.id}`" class="py-4 ml-2 w-full text-xs font-medium text-gray-900 cursor-pointer">{{ tag.tag }}</label>
                    </div>
                    <div v-if="!tags.tags" class="flex flex-col justify-center items-center col-span-4">
                        <div class="text-gray-500 text-center text-xs italic">Vazio</div>
                    </div>
                </div>
            </div>
          </div>
        </div>

      </form>
    </div>


  </div>
</template>


<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import Icon from '@/Shared/Icon'

export default {
  components: {
    Head,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
    Icon,
  },
  layout: Layout,
  props: {
    active_campaign_plugin: Object,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        name: this.active_campaign_plugin.name,
        api_url: this.active_campaign_plugin.api_url,
        api_key: this.active_campaign_plugin.api_key,
        deliverable_link_field_id: this.active_campaign_plugin.deliverable_link_field_id,
        deliverable_tag_id: this.active_campaign_plugin.deliverable_tag_id,
      }),
      fields: {},
      tags: {},
      search: '',
    }
  },
  watch: {
    search : function (value) {
        if (this.form.api_url !== '' && this.form.api_key !== '') { 
            this.tags = [];
            try {
                this.$page.props.flash.error = null;
                this.$page.props.flash.success = "Carregando...";
                axios.get('/api/activecampaign/callApi?api_url='+this.form.api_url+'/api/3/tags?search='+value+'&api_key='+this.form.api_key)
                .then (response => {
                    this.tags = response.data;
                    console.log(this.tags);
                })
                setTimeout(() => {
                    this.$page.props.flash.success = null;
                    if (!this.fields.fields) {
                        this.$page.props.flash.error = 'Erro! Tem certeza que digitou corretamente?';
                    }
                },1500);
            } catch (error) {
                console.log(error);
            }
        }
    },
  },
  methods: {
    update() {
      this.form.put(`/settings/plugins/activecampaign/${this.active_campaign_plugin.id}`)
    },
    callApi() {
        if (this.form.api_url !== '' && this.form.api_key !== '') { 
            try {
                this.$page.props.flash.error = null;
                this.$page.props.flash.success = "Carregando...";
                axios.get('/api/activecampaign/callApi?api_url='+this.form.api_url+'/api/3/fields?limit=100&api_key='+this.form.api_key)
                .then (response => {
                    this.fields = response.data;
                    // console.log(this.fields);
                })
                setTimeout(() => {
                    this.$page.props.flash.success = null;
                    if (!this.fields.fields) {
                        this.$page.props.flash.error = 'Erro! Tem certeza que digitou corretamente?';
                    }
                },1500);
            } catch (error) {
                console.log(error);
            }
        }
    },
    destroy() {
      if (confirm('Tem certeza que deseja desativar esta integração?')) {
        this.$inertia.delete(`/settings/plugins/activecampaign/${this.active_campaign_plugin.id}`)
      }
    },
    restore() {
      if (confirm('Tem certeza que deseja ativar esta integração?')) {
        this.$inertia.put(`/settings/plugins/activecampaign/${this.active_campaign_plugin.id}/restore`)
      }
    },
  },
  created(){
    this.callApi();
    if (this.form.api_url !== '' && this.form.api_key !== '') { 
        try {
            this.$page.props.flash.error = null;
            this.$page.props.flash.success = "Carregando...";
            axios.get('/api/activecampaign/callApi?api_url='+this.form.api_url+'/api/3/tags/'+this.form.deliverable_tag_id+'&api_key='+this.form.api_key)
            .then (response => {
                this.tags = {'tags' : response.data};
                console.log(this.tags);
            })
            setTimeout(() => {
                this.$page.props.flash.success = null;
                if (!this.fields.fields) {
                    this.$page.props.flash.error = 'Erro! Tem certeza que digitou corretamente?';
                }
            },1500);
        } catch (error) {
            console.log(error);
        }
    }
  },
}
</script>
