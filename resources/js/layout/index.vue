<template>
  <div>
    <el-row :gutter="15">
      <el-col :lg="4">
        <Menu />
      </el-col>
      <el-col :lg="20">
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
h1 {
  display: inline;
  font-size: 1.8rem;
}
h2 {
  display: inline;
  font-size: 1.5rem;
}
h3 {
  display: inline;
  font-size: 1.2rem;
}
</style>
