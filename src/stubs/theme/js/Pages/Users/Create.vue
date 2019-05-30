<template>
  <layout title="Create User">
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-primary-400 hover:text-primary-600" :href="route('users')">Users</inertia-link>
      <span class="text-primary-400 font-medium">/</span> Create
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.name" :errors="errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Name" />
          <text-input v-model="form.email" :errors="errors.email" class="pr-6 pb-8 w-full lg:w-1/2" label="Email" />
          <text-input v-model="form.password" :errors="errors.password" class="pr-6 pb-8 w-full lg:w-1/2" type="password" autocomplete="new-password" label="Password" />
        </div>
        <div class="px-8 py-4 bg-grey-lightest border-t border-grey-lighter flex justify-end items-center">
          <loading-button :loading="sending" class="btn-primary" type="submit">Create User</loading-button>
        </div>
      </form>
    </div>
  </layout>
</template>

<script>
import Layout from '@/Shared/Layout'
import LoadingButton from '@/Shared/LoadingButton'
import SelectInput from '@/Shared/SelectInput'
import TextInput from '@/Shared/TextInput'

export default {
  components: {
    Layout,
    LoadingButton,
    SelectInput,
    TextInput,
  },
  props: {
    errors: {
      type: Object,
      default: () => ({}),
    },
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        name: null,
        email: null,
        password: null,
      },
    }
  },
  methods: {
    submit() {
      this.sending = true
      this.$inertia.post(this.route('users.store'), this.form)
        .then(() => this.sending = false)
    },
  },
}
</script>
