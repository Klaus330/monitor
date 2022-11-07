<script setup>
import { usePage, useForm } from "@inertiajs/inertia-vue3";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from '@/Components/PrimaryButton.vue';

let form = useForm({
  site: "",
});


let submit = () => {
  form.post(route("site.create", usePage().props.value.user.id), {
    preserveScroll: true,
    onSuccess: () => form.reset()
  })
};
</script>

<template>
  <div class="p-3">
    <h2 class="text-xl font-bold mb-3 border-b border-gray-300 pb-2">Add Monitor</h2>
    <form @submit.prevent="submit">
      <div>
        <InputLabel for="site" value="Site Url:" />
        <TextInput
          id="site"
          v-model="form.site"
          type="text"
          class="mt-1 block w-full"
          autofocus
        />
        <InputError class="mt-2" :message="form.errors.site" />
      </div>
      <div class="mt-3 text-right">
        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
            Add
        </PrimaryButton>
      </div>
    </form>
  </div>
</template>
