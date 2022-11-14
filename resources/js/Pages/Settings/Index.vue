<script setup>
import { usePage, useForm } from "@inertiajs/inertia-vue3";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import ActionsMenu from "@/Components/ActionsMenu.vue";
import Checkbox from "@/Components/Checkbox.vue";

let form = useForm({
  crawler_delay: usePage().props.value.configuration.crawler_delay,
  respect_robots: usePage().props.value.configuration.respect_robots
});

let saveChanges = () => {
  form.patch(route("site.configuration.update", usePage().props.value.user.id), {
    preserveScroll: true,
  });
};
</script>

<template>
  <div class="sm:px-6 lg:px-8 py-5 w-full mt-5 bg-white w-full">
    <div class="flex items-start justify-between px-2">
      <h2 class="text-2xl font-bold mb-3">Settings</h2>

      <ActionsMenu />
    </div>
    <form @submit.prevent="saveChanges">
      <div class="my-4 w-1/4">
        <InputLabel for="crawler_delay" value="Crawler Delay (ms):" />
        <TextInput
          id="crawler_delay"
          v-model="form.crawler_delay"
          type="number"
          min="0"
          max="10000"
          class="mt-1 block w-full"
          autofocus
        />
        <InputError class="mt-2" :message="form.errors.crawler_delay" />
      </div>

      <div class="my-4">
        <div class="flex items-center justify-start gap-2"> 
          <InputLabel for="respect_robots" value="Respect Robots:" />
          <Checkbox
            id="respect_robots"
            :value="form.respect_robots"
            v-model:checked="form.respect_robots"
            type="checkbox"
            class="block"
          />
        </div>
        <InputError class="mt-2" :message="form.errors.respect_robots" />
      </div>

      <div class="mt-3 text-right">
        <PrimaryButton
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
        >
          Save
        </PrimaryButton>
      </div>
    </form>
  </div>
</template>
