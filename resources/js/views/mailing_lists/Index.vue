<template>
  <div class="container">
    <el-row>
      <el-col :span="12">
        <h1>Рассылки</h1>
      </el-col>
      <el-col :span="12" style="text-align: right">
        <el-button type="success" icon="el-icon-plus" @click="create">Создать</el-button>
      </el-col>
    </el-row>

    <el-table
      :data="list"
    >
      <el-table-column
        prop="id"
        label="ID"
        sortable
      >
      </el-table-column>
      <el-table-column
        prop="name"
        label="Название"
        sortable
      >
      </el-table-column>
    </el-table>
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
    create() {
      this.$router.push('/admin/mailing_lists/create')
    }
  }
}
</script>

<style scoped>

</style>
