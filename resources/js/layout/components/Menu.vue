<template>
  <el-menu :default-openeds="['1', '3']">
    <el-submenu index="1">
      <template slot="title"><i class="el-icon-message"></i>Рассылка</template>
      <el-menu-item index="1-1" @click="$router.push({ name: 'mailing-lists-list' }).catch(()=>{})">Список</el-menu-item>
    </el-submenu>
  </el-menu>
</template>

<script>
  export default {

  }
</script>

<style scoped>
  a {
    display:block;

  }
</style>
