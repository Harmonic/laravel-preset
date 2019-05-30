<template>
  <layout :title="`${form.name}`">
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-primary-400 hover:text-primary-600" :href="route('users')">Users</inertia-link>
      <span class="text-primary-400 font-medium">/</span>
      {{ form.name }}
    </h1>
    <trashed-message v-if="user.deleted_at" class="mb-6" @restore="restore">
      This user has been deleted.
    </trashed-message>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.name" :errors="errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Name" />
          <text-input v-model="form.email" :errors="errors.email" class="pr-6 pb-8 w-full lg:w-1/2" label="Email" />
          <text-input v-model="form.password" :errors="errors.password" class="pr-6 pb-8 w-full lg:w-1/2" type="password" autocomplete="new-password" label="Password" />
        </div>
        <div class="px-8 py-4 bg-grey-lightest border-t border-grey-lighter flex items-center">
          <button v-if="!user.deleted_at" class="text-red-500 hover:underline" tabindex="-1" type="button" @click="destroy">Delete User</button>
          <loading-button :loading="sending" class="btn-primary ml-auto" type="submit">Update User</loading-button>
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
import TrashedMessage from '@/Shared/TrashedMessage'

export default {
  components: {
    Layout,
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage,
  },
  props: {
    user: Object,
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
        name: this.user.name,
        email: this.user.email,
        password: this.user.password,
      },
    }
  },
  methods: {
    submit() {
      this.sending = true
      this.$inertia.put(this.route('users.update', this.user.id), this.form)
        .then(() => this.sending = false)
    },
    destroy() {
      if (confirm('Are you sure you want to delete this user?')) {
        this.$inertia.delete(this.route('users.destroy', this.user.id))
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this user?')) {
        this.$inertia.put(this.route('users.restore', this.user.id))
      }
    },
  },
}
</script>
