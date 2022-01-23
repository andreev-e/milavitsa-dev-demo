<template>
  <el-menu :default-openeds="['1', '3']">
    <el-submenu index="1">
      <template slot="title"><i class="el-icon-message"></i>Рассылка</template>
      <el-menu-item index="1-1"><el-link href="/admin/mailing_lists">Список</el-link></el-menu-item>
      <el-menu-item index="1-2"><el-link href="/admin/statistics">Статистика</el-link></el-menu-item>
    </el-submenu>
  </el-menu>
</template>

<script>
  export default {

  }
</script>

<style scoped>

</style>
