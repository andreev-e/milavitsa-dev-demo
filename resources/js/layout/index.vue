<template>
  <el-container>
    <el-aside width="300px">
      <el-menu :default-openeds="['1']">
        <el-submenu index="1">
          <template slot="title"><i class="el-icon-message"></i>Рассылка</template>
          <el-menu-item-group>
            <el-menu-item index="1-1">
              <router-link to="/admin/mailing_lists">Список рассылок</router-link>
            </el-menu-item>
          </el-menu-item-group>
        </el-submenu>
      </el-menu>
    </el-aside>
    <el-main>
      <app-main />
    </el-main>
  </el-container>
</template>

<script>

</script>

<style>

</style>
