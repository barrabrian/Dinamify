<template>
  <div>
    <Head title="Imagens" />
    
    <div class="shadow-lg grid grid-cols-12 grid-flow-col border border-gray-300 rounded h-screen" style="height: calc(100vh - 150px - 3rem);">
      <div class="bg-gray-200 border-r-2 border-gray-300 shadow-lg">
        <div class="flex flex-col justify-center items-top">
          <Link class="group flex justify-center py-8 items-center  w-full border-b border-gray-300" href="/settings/plugins">
            <icon name="plugin" class="w-8 h-8 fill-gray-700 group-hover:fill-gray-900" />
          </Link>
          <Link class="group flex justify-center py-8 items-center bg-white w-full border-b border-gray-300" href="/settings/images">
            <icon name="image" class="w-8 h-8 fill-gray-700 group-hover:fill-gray-900" />
          </Link>
          <Link class="group flex justify-center py-8 items-center  w-full border-b border-gray-300" href="/settings/users">
            <icon name="person" class="w-8 h-8 fill-gray-700 group-hover:fill-gray-900" />
          </Link>
          <Link class="group flex justify-center py-8 items-center  w-full border-b border-gray-300" href="#">
            <icon name="config" class="w-8 h-8 fill-gray-700 group-hover:fill-gray-900" />
          </Link>
        </div>
      </div>
      
      <div class="col-span-11 bg-gray-100">
        <div class="p-12 bg-white flex justify-between items-center">
          <h1 class="text-3xl font-bold">Imagens</h1>
          <div>
            <button type="button" class="inline btn-emerald" @click="showAddImg = true">
              Adicionar
            </button>
          </div>

        </div>
        <div class="p-12 border-t border-gray-300 ">

          <div class="grid gap-4">

            <div class="grid gap-4 grid-cols-6">
                <div v-for="(image, index) in images.data" :key="index" class="rounded shadow bg-white">
                    <img class="object-cover w-full h-32 rounded" :src="image.image_path" >
                </div>
            </div>

            <div v-if="images.data.length === 0" class="flex flex-col justify-center items-center border-dashed border-2 border-gray-300 rounded bg-gray-100 p-6">
              <div class="text-gray-500 mb-1 text-sm">Não tem nada aqui ainda :/</div>
              <div class="font-bold text-gray-700 text-lg">Tente adicionar algo novo</div>
            </div>

            <pagination class="mt-6" :links="images.links" />
        
          </div>
        </div>
      </div>
    </div>

    <add-image-modal :show="showAddImg" @close="showAddImg = false"></add-image-modal>

  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import SearchFilter from '@/Shared/SearchFilter'
import AddImageModal from '@/Shared/AddImageModal'



export default {
  components: {
    Head,
    Icon,
    Link,
    SearchFilter,
    AddImageModal,
  },
  layout: Layout,
  props: {
    images: Array,
  },
  data() {
    return {
      showAddImg: false,
    }
  },
}
</script>
