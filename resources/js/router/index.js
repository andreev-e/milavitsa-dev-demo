import Vue from 'vue';
import Router from 'vue-router';

Vue.use(Router);

/* Layout */
import Layout from '@/layout';

export const constantRoutes = [
  {
    path: '/admin',
    component: Layout,
    hidden: false,
    children: [
      {
        path: 'mailing_lists',
        name: 'mailing-lists-list',
        component: () => import('@/views/mailing_lists/List'),
      },
      {
        path: 'mailing_lists/:id',
        component: () => import('@/views/mailing_lists/Form'),
      },
      {
        path: 'statistics',
        component: () => import('../views/Statistics'),
      }
    ],
  },
];

export const asyncRoutes = [
  { path: '*', redirect: '/404', hidden: true },
];

const createRouter = () => new Router({
  mode: 'history',
  base: process.env.MIX_LARAVUE_PATH,
  routes: constantRoutes,
  scrollBehavior(to, from, savedPosition) {
    if (to.hash) {
      return {
        selector: to.hash,
        behavior: 'smooth',
      };
    }
  },
});

const router = createRouter();

// Detail see: https://github.com/vuejs/vue-router/issues/1234#issuecomment-357941465
export function resetRouter() {
  const newRouter = createRouter();
  router.matcher = newRouter.matcher; // reset router
}

const DEFAULT_TITLE = 'Vue Router';
router.afterEach((to, from) => {
  // Use next tick to handle router history correctly
  // see: https://github.com/vuejs/vue-router/issues/914#issuecomment-384477609
  Vue.nextTick(() => {
    document.title = to.meta.title || DEFAULT_TITLE;
  });
});

export default router;
