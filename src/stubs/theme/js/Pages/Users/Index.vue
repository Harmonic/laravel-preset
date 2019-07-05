<template>
  <layout title="Users">
    <h1 class="mb-8 font-bold text-3xl">Users</h1>
    
    <inertia-table :data="users" id="id" :order="order" :filters="filters" :columns="columns" routeName="users" createLink="users.create" @item-selected="show"></inertia-table>
    
  </layout>
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import InertiaTable from 'inertia-table'

export default {
  components: {
    Icon,
    Layout,
    InertiaTable
  },
  props: {
    users: Array,
    filters: Object,
    order: Object,
  },
  data() {
    return {
      columns: ["name", "email"],
    }
  },
  methods: {
    show(user) {
      this.$inertia.replace(this.route('users.edit', user.id));
    }
  },
}
</script>
