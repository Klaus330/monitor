<script>
    import DialogModal from './DialogModal.vue';
</script>


<template>
  <DialogModal>
    <template #title>
    </template>

    <template #content>

      <div class="mt-4">
        <TextInput
          ref="passwordInput"
          type="password"
          class="mt-1 block w-3/4"
          placeholder="Password"
        />

        <InputError class="mt-2" />
      </div>
    </template>

    <template #footer>
      <SecondaryButton> Cancel </SecondaryButton>

      <PrimaryButton
        class="ml-3"
      >
      </PrimaryButton>
    </template>
  </DialogModal>
</template>
