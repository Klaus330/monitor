<script setup>
import DialogModal from "@/Components/DialogModal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
</script>

<template>
  <DialogModal :show="true" @close="">
    <template #title>Custom Modal</template>

    <template #content>
      <div class="mt-4">
        <slot/>
      </div>
    </template>

    <template #footer>
      <SecondaryButton> Okay </SecondaryButton>
    </template>
  </DialogModal>
</template>
