<template>
  <div>
    <el-row :gutter="15">
      <el-col :lg="6">
        <Menu />
      </el-col>
      <el-col :lg="18">
        <router-view />
      </el-col>
    </el-row>
  </div>
</template>

<script>
import Menu from '../layout/components/Menu';

export default {
  components: { Menu },
}
</script>

<style>
a{
  text-decoration: none;
  color: #409EFF;
}
.el-form-item__label {
  font-weight: bold;
}
</style>
