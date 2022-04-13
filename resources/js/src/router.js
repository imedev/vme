/*=========================================================================================
  File Name: router.js
  Description: Routes for vue-router. Lazy loading is enabled.

==========================================================================================*/


import Vue from 'vue'
import Router from 'vue-router'


Vue.use(Router)

const router = new Router({
    mode: 'history',
    routes: [

        {
            // =============================================================================
            // MAIN LAYOUT ROUTES
            // =============================================================================
            path: '/',
            component: () => import('./views/home.vue')
        },




        // Redirect to 404 page, if no match found
       /* {
            path: '*',
            redirect: '/views/error-404'
        }*/
    ],
})


router.afterEach(() => {
    // Remove initial loading
    const appLoading = document.getElementById('loading-bg')
    if (appLoading) {
        appLoading.style.display = "none";
    }
})

export default router
