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
            <ul class="flex flex-wrap text-sm font-medium text-center text-gray-500 border-b border-gray-200 mb-4">
                <li class="mr-2">
                    <span class="inline-block p-2 rounded-t-lg active hover:text-gray-600 hover:bg-gray-50 cursor-pointer" :class="(activeTab == 0)? 'active bg-gray-100' : ''" @click="activeTab = 0">Respostas</span>
                </li>
                <li class="mr-2">
                    <span class="inline-block p-2 rounded-t-lg hover:text-gray-600 hover:bg-gray-50 cursor-pointer" :class="(activeTab == 1)? 'active bg-gray-100' : ''" @click="activeTab = 1">Condicionais</span>
                </li>
                <li class="mr-2">
                    <span class="inline-block p-2 rounded-t-lg hover:text-gray-600 hover:bg-gray-50 cursor-pointer" :class="(activeTab == 2)? 'active bg-gray-100' : ''" @click="activeTab = 2">Gráficos</span>
                </li>
                <li class="mr-2">
                    <span class="inline-block p-2 rounded-t-lg hover:text-gray-600 hover:bg-gray-50 cursor-pointer" :class="(activeTab == 3)? 'active bg-gray-100' : ''" @click="activeTab = 3">Data</span>
                </li>
            </ul>
            <div v-if="activeTab == 0">
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
                    <div class="font-bold text-gray-700 text-center text-lg">Tente selecionar outra Origem de Dados</div>
                </div>
            </div>
            <div v-else-if="activeTab == 1">
                <div v-if="form.form_id" class="flex flex-col justify-center items-center border-dashed border-2 border-gray-300 rounded bg-gray-100 p-6">
                    <select-input class="pb-4 w-full " v-model="condition.variable" label="Se" >
                        <option v-for="(variable, index) in condition_variables" :key="index" :value="variable.qid">{{ variable.title }}</option>
                    </select-input>
                    <select-input class="pb-4 w-full " v-model="condition.alternative" label="É igual a" >
                        <option v-for="(alternative, index) in alternatives" :key="index" :value="alternative.label">{{ alternative.label }}</option>
                    </select-input>
                    <div class="text-gray-500 mb-1 text-center text-xs">{{ condition.string }}</div>
                    <div class="font-bold text-gray-700 text-center text-lg cursor-pointer" @click="copyCondition">Copiar</div>
                </div>
                <div v-else class="flex flex-col justify-center items-center border-dashed border-2 border-gray-300 rounded bg-gray-100 p-6">
                    <div class="font-bold text-gray-700 text-center text-lg">Tente selecionar uma Origem de Dados</div>
                </div>
            </div>
            <div v-else-if="activeTab == 2">
                <div v-if="!showSC">
                    <div class="flex flex-col border-dashed border-2 border-gray-300 rounded bg-gray-100 p-6 mb-2">
                        <select-input class="pb-4 w-full " v-model="chart.type" label="Tipo" >
                            <option value="vbar-chart" >Barra Vertical</option>
                            <option value="radar-chart" >Radar</option>
                        </select-input>
                        <text-input v-model="chart.color" class="w-full pb-4" placeholder="ex: #000000" label="Cor do Gráfico" />
                        <p class="mb-2 text-gray-600">Pontuação:</p>
                        <div v-for="(rule, index) in chart.score_condition" :key="index" class="w-full form-input shadow mb-2 cursor-pointer " @click="editSC(index)">
                            <div v-if="rule.name == ''" class="flex justify-between items-center text-gray-500">
                                <span>Adicionar</span>
                                <span class="mr-1">+</span>
                            </div>
                            <div v-else class="flex justify-between items-center">
                                <span>{{ rule.name }}</span>
                                <icon name="edit" class="w-3 h-3 fill-gray-600 inline" />
                            </div>
                        </div>
                        
                        <div class="text-gray-500 mb-1 mt-6 text-center text-xs truncate">{{ chart.string }}</div>
                        <div class="font-bold text-gray-700 text-center text-lg cursor-pointer" @click="copyTxt(chart.string)">Copiar</div>
                    </div>
                </div>
                <div v-else class="flex flex-col border-dashed border-2 border-gray-300 rounded bg-gray-100 p-6">
                    <div class="flex items-center mb-6">
                        <div class="inline mr-3 group cursor-pointer" @click="showSC = false">
                            <icon name="arrow-left" class="w-5 h-5 fill-gray-700 group-hover:fill-gray-600 inline" />
                        </div>
                        <p class="inline font-bold">Pontuação {{ chart.score_condition.length > 1 ? ruleIndex + 1 : '' }}:</p>
                    </div>
                    <text-input v-model="chart.score_condition[ruleIndex].name" class="w-full pb-4" placeholder="" label="Nome" />
                    <p class="mb-2 ">Condição de Pontuação: <span class="text-xs text-gray-400 italic">(Min. 2)</span></p>
                    <div v-for="(variable, index) in condition_variables" :key="index" class="w-full p-4 mb-2 bg-gray-50 rounded shadow form-input">
                        <p class="mb-1 text-sm">{{ variable.title }}</p>
                        <ul class="w-full grid grid-cols-2 gap-1 text-sm font-medium text-gray-900 ">
                            <li v-for="(alternative, index) in variable.alternatives" :key="index" class="w-full bg-white rounded-lg border border-gray-200 shadow flex justify-center">
                                <div class="flex items-center pl-3 cursor-pointer">
                                    <input :id="`vue-checkbox-${alternative.id}`" type="checkbox" :value="alternative.aid" v-model="chart.score_condition[ruleIndex].value" class="w-4 h-4 text-blue-600 bg-gray-100 rounded border-gray-300 focus:ring-blue-500 focus:ring-2 cursor-pointer">
                                    <label :for="`vue-checkbox-${alternative.id}`" class="py-3 pl-2 pr-3 w-full text-xs font-medium text-gray-900 cursor-pointer">{{ alternative.label }}</label>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div v-if="condition_variables.length == 0" class="flex flex-col justify-center items-center w-full p-2 mb-2 bg-gray-50 rounded shadow form-input">
                        <div class="text-gray-500 text-center text-xs italic">Vazio</div>
                    </div>
                </div>
            </div>
            <div v-else-if="activeTab == 3">
                <div class="flex flex-col justify-center items-center border-dashed border-2 border-gray-300 rounded bg-gray-100 p-6">
                    <select-input class="pb-4 w-full " v-model="date.var" label="Componente" >
                        <option value="dia">Dia</option>
                        <option value="mes">Mês</option>
                        <option value="ano">Ano</option>
                    </select-input>
                    <select-input v-if="date.var == 'mes'" class="pb-4 w-full " v-model="date.option" label="Formato" >
                        <option value="">Número</option>
                        <option value=",toString()">Texto</option>
                    </select-input>
                    <div class="text-gray-500 mb-1 text-center text-xs">{{ date.return }}</div>
                    <div class="font-bold text-gray-700 text-center text-lg cursor-pointer" @click="copyTxt(date.return)">Copiar</div>
                </div>
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
                        <div class="cursor-pointer items-center text-base font-semibold text-gray-900 " @click="copyTxt('<img stored src=&quot;'+ image.image_path + '&quot; class=&quot;&quot; style=&quot;&quot; alt=&quot;imagem&quot;>')">
                            Copiar
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="images.length === 0" class="flex flex-col justify-center items-center border-dashed border-2 border-gray-300 rounded bg-gray-100 p-6">
                <div class="text-gray-500 mb-1 text-center text-sm">Não tem nada aqui ainda :/</div>
                <div class="font-bold text-gray-700 text-center text-lg">Tente adicionar uma imagem</div>
            </div>
            <!-- <pagination class="mt-6" :links="images.links" /> -->

        </div>
    </sidebar-modal>

    <add-image-modal :show="showAddImg" @close="showAddImg = false"></add-image-modal>

    <sidebar-modal :show="showHelp" @close="showHelp = false">
        <div>
            <div class="mb-6">
                <h5 class="text-2xl font-bold">Ajuda:</h5>
            </div>
            <div class="flex flex-col justify-center items-center border-dashed border-2 border-gray-300 rounded bg-gray-100 p-6">
                <div class="text-gray-500 mb-1 text-sm">Não tem nada aqui ainda :/</div>
                <div class="font-bold text-gray-700 text-lg">Em breve teremos algo incrível aqui</div>
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
import TextAreaInput from '@/Shared/TextareaInput'
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
      alternatives: {},
      condition: {
        string: '',
        variable: '',
        alternative: '',
      },
      chart: {
        string: '',
        color: '',
        name: '',
        type: 'vbar-chart',
        score_condition: [{
            name: '',
            value: [],
        }],
      },
      date: {
        var: 'dia',
        option: '',
        return: '{{now-date[dia]}}',
      },
      showVars: false,
      showImgs: false,
      showHF: false,
      showAddImg: false,
      showSC: false,
      showHelp : false,
      activeTab: 0,
      ruleIndex: 0,
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
    'condition.variable' : function (value) {
        axios.get('/api/alternatives?qid=' + value)
        .then (response => {
            this.alternatives = response.data;
        });
    },
    'condition.alternative' : function (value) {
        // this.condition.string = '{{if(question['+this.condition.variable+']==choice['+value+'])}}';
        this.condition.string = '{{if(question['+this.condition.variable+']=="'+value+'")}}';
    },
    showSC : function () {
        this.setChartString();  
    },
    'chart.color' : function () {
        this.setChartString();  
    },
    'chart.type' : function (value) {
        if (value == 'radar-chart') {
            for (let i = 0; i < 6; i++) {
                this.chart.score_condition.push({
                    name: '',
                    value: [],
                });
            }
        } else {
            for (let i = 0; i < 6; i++) {
                this.chart.score_condition.pop();
            }
        }
        this.setChartString();  
    },
    'date.var' : function (value) {
        if (value == "mes") {
            this.date.return = '{{now-date[' + value + this.date.option + ']}}';
        } else {
            this.date.option = '';
            this.date.return = '{{now-date[' + value + ']}}';
        }
    },
    'date.option' : function (value) {
        this.date.return = '{{now-date[' + this.date.var + value + ']}}';
    },
  },
  methods: {
    store() {
      this.form.post('/deliverables')
    },
    preview() {
               let normalized_html = '';
        let html_aux = this.form.html.split('{{end-if}}');
        html_aux.forEach(el => {
            let aux = el.split('{{if');
            normalized_html = normalized_html + aux[0];
            if (aux.length > 1) {
                normalized_html = normalized_html +  '<span class="dinamify-var"><span style="color:purple;">if</span> (..) {...}</span>';
            }
        });

        // console.log(html_aux);


        html_aux = normalized_html.split('}}');
        normalized_html = '';
        html_aux.forEach(el => {
            let aux = el.split('{{');
            normalized_html = normalized_html + aux[0];

            // console.log(aux);

            if (aux.length > 1) {
                if (aux[1].split('[')[0] == 'question') {
                    normalized_html = normalized_html + '<span class="dinamify-var" style="color:green;">var</span>';
                } else if (aux[1].split('[')[0] == 'now-date') {
                    normalized_html = normalized_html + '<span class="dinamify-var" style="color:green;">now_date</span>';
                } else if (aux[1].split('[')[0] == 'vbar-chart' || aux[1].split('[')[0] == 'radar-chart' ) {
                    normalized_html = normalized_html + '<span class="dinamify-var" style="color:orange;margin: 50px auto;display:block;text-align:center;">chart</span>';
                } else {
                    normalized_html = normalized_html + '<span class="dinamify-var" style="color:purple;">unknown</span>';
                }
            }
        });

        // console.log(html_aux);
        // console.log(normalized_html);
        
        let iframe = document.getElementById("preview");
        iframe.contentWindow.document.head.innerHTML = '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
        iframe.contentWindow.document.body.innerHTML = '<style>.page-break{margin: 4em 0; padding:2em; background: gray;}body{max-width:100vw;overflow-wrap: break-word;white-space: normal;}img{max-width:100%;}.dinamify-var{display:inline-block;background:#fefefe;padding: 5px 10px;border-radius: 5px;border: 1px solid #ccc;font-size: 10px;}</style>'+ normalized_html;
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
        if (mytext !== ''){
            try {
                await navigator.clipboard.writeText(mytext);
                this.showVars = false;
                this.showImgs = false;

                this.$page.props.flash.success = "Item Copiado!";
                setTimeout(() => {
                    this.$page.props.flash.success = null;
                },1500)
            } catch($e) {
                console.log('Cannot copy: ' + $e);
                this.$page.props.flash.error = "Erro ao Copiar.";
            }
        } else {
            this.$page.props.flash.error = "Complete os Campos!";
            setTimeout(() => {
                this.$page.props.flash.error = null;
            },1500)
        }
    },
    async copyCondition() {
        if (this.condition.alternative !== '') {
            this.copyTxt(this.condition.string+'\n  ...\n{{end-if}}');
        } else {
            this.$page.props.flash.error = "Complete os Campos!";
            setTimeout(() => {
                this.$page.props.flash.error = null;
            },1500)
        }
    },
    setChartString() {
        let valid = false;
        this.chart.score_condition.forEach(el => {
            if ( el.name !== '' && el.value.length > 1){
                valid = true;
            } else {
                valid = false;
            }
        });

        if (this.chart.color !== '' && this.chart.color !== null && valid ) {
            this.chart.string = '{{'+this.chart.type+'['+this.chart.color;
            this.chart.score_condition.forEach(el => {
                this.chart.string = this.chart.string + ",'" + el.name + "'(" ;
                el.value.forEach(cond => {
                    this.chart.string = this.chart.string + cond;
                    if (cond !== el.value[el.value.length - 1]) {
                        this.chart.string = this.chart.string + ',';
                    }
            });
                this.chart.string = this.chart.string +')';
            });
            this.chart.string = this.chart.string +']}}';
        }
    },
    editSC(index){
        this.ruleIndex = index;
        this.showSC = true;
    },
  },
  computed: {
    condition_variables: function (){
        return this.variables.filter(function (variable){
            return variable.type === 'multiple_choice'
        })
    },
  },
}
</script>
