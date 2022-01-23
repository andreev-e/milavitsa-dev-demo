<template>
  <el-container style="min-height: 500px; border: 1px solid #eee">
    <el-aside width="250px" style="background-color: rgb(238, 241, 246)">
      <Menu />
    </el-aside>
    <router-view />
  </el-container>
</template>

<script>
import Menu from '../layout/components/Menu';

export default {
  components: { Menu },
}
</script>

<style>

</style>
