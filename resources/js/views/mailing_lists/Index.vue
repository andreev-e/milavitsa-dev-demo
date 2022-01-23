<template>
  <div class="container">
    <h1>Mailing list</h1>
    {{ list }}
  </div>
</template>

<script>
import MailingListResource from '@/api/mailing_list.js'

const mailingList = new MailingListResource();

export default {
  data() {
    return {
      list: [],
      listQuery: {

      },
      loading: false,
    }
  },
  mounted() {
    this.loadList();
  },
  methods: {
    async loadList() {
      this.loading = true
      const res = await mailingList.list(this.listQuery)
      this.loading = false
    },
  }
}
</script>

<style scoped>

</style>
