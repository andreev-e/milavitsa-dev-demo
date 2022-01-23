<template>
  <el-menu :default-openeds="['1', '3']">
    <el-submenu index="1">
      <template slot="title"><i class="el-icon-message"></i>Рассылка</template>
      <el-menu-item index="1-1"><router-link to="/admin/mailing_lists">Список</router-link></el-menu-item>
      <el-menu-item index="1-2"><router-link to="/admin/statistics">Статистика</router-link></el-menu-item>
    </el-submenu>
  </el-menu>
</template>

<script>
  export default {

  }
</script>

<style scoped>

</style>
