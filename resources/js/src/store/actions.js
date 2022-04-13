/*=========================================================================================
  File Name: actions.js
  Description: Vuex Store - actions

==========================================================================================*/
import axios from "@/axios.js"

const actions = {

    // /////////////////////////////////////////////
    // COMPONENTS
    // /////////////////////////////////////////////

    // Vertical NavMenu
    updateVerticalNavMenuWidth({ commit }, width) {
      commit('UPDATE_VERTICAL_NAV_MENU_WIDTH', width)
    },

    // VxAutoSuggest
    updateStarredPage({ commit }, payload) {
      commit('UPDATE_STARRED_PAGE', payload)
    },

    // The Navbar
    arrangeStarredPagesLimited({ commit }, list) {
      commit('ARRANGE_STARRED_PAGES_LIMITED', list)
    },
    arrangeStarredPagesMore({ commit }, list) {
      commit('ARRANGE_STARRED_PAGES_MORE', list)
    },

    // /////////////////////////////////////////////
    // UI
    // /////////////////////////////////////////////

    toggleContentOverlay({ commit }) {
      commit('TOGGLE_CONTENT_OVERLAY')
    },
    updateTheme({ commit }, val) {
      commit('UPDATE_THEME', val)
    },

    // /////////////////////////////////////////////
    // User/Account
    // /////////////////////////////////////////////

    updateUserInfo({ commit }, payload) {
      commit('UPDATE_USER_INFO', payload)
    },
    updateUserRole({ dispatch }, payload) {
      // Change client side

      // Make API call to server for changing role

      // Change userInfo in localStorage and store
      dispatch('updateUserInfo', {userRole: payload.userRole})
    },

/////////////////////////////////////////////////
// Products
/////////////////////////////////////////////////

    addItem({ commit }, item) {

        return new Promise((resolve, reject) => {
            axios.post("/api/products/", item)
                .then((response) => {
                    commit('ADD_ITEM', Object.assign(item, {id: response.data.id}))
                    resolve(response)
                })
                .catch((error) => {
                    console.log(error)
                    reject(error)
                })
        })
    },
    fetchDataListItems({ commit }) {
        return new Promise((resolve, reject) => {
            axios.get("/api/products")
                .then((response) => {
                    commit('SET_PRODUCTS', response.data)
                    resolve(response)
                })
                .catch((error) => { reject(error) })
        })
    },

    updateItem({ commit }, item) {
        return new Promise((resolve, reject) => {
            axios.post(`/api/products/${item.id}`, item)
                .then((response) => {
                    commit('UPDATE_PRODUCT', response.data)
                    resolve(response)
                })
                .catch((error) => { reject(error) })
        })
    },
    removeItem({ commit }, itemId) {
        return new Promise((resolve, reject) => {
            axios.delete(`/api/products/${itemId}`)
                .then((response) => {
                    commit('REMOVE_ITEM', itemId)
                    resolve(response)
                })
                .catch((error) => { reject(error) })
        })
    },
    importData({ commit }){

        return new Promise((resolve, reject) => {
            axios.get("/api/products/import")
                .then((response) => {
                    commit('SET_PRODUCTS', response.data.data)
                    resolve(response)
                })
                .catch((error) => {
                    console.log(error)
                    reject(error)})
        })
    },

    getData({ commit }){

        return new Promise((resolve, reject) => {
            axios.get("/api/products")
                .then((response) => {
                    commit('SET_PRODUCTS', response.data.data)
                    commit('SET_CURRENT_PAGE_PRODUCTS', response.data.current_page)
                    commit('SET_TOTAL_PAGE_PRODUCTS', response.data.total)

                    console.log(response.data.data);
                   // this.products = response.data.data;//Object.assign({},response.data.data);
                    resolve(response)
                })
                .catch((error) => {
                    console.log(error)
                    reject(error)})
        })
    },
    paginateProducts({ commit },item){
        return new Promise((resolve, reject) => {
            axios
                .get(`/api/products/paginateProducts?page=${item.page}&per_page=${item.itemsPerPage}&brand=${item.brand}&price=${item.price}`
                )
                .then(
                    (response) => {
                        commit('SET_PRODUCTS', response.data.data)
                        commit('SET_CURRENT_PAGE_PRODUCTS', response.data.current_page)
                        commit('SET_TOTAL_PAGE_PRODUCTS', response.data.total)
                        resolve(response.data);
                    },
                    (err) => {
                        console.log(err);
                        reject(err);
                    }
                );
        });

    },
    filterDataByBrandAndPrice({ commit },item){
        return new Promise((resolve, reject) => {
            axios
                .get(`/api/products/filterProducts?brand=${item.brand}&price=${item.price}`
                )
                .then(
                    (response) => {
                        commit('SET_PRODUCTS', response.data.data)
                        commit('SET_CURRENT_PAGE_PRODUCTS', response.data.current_page)
                        commit('SET_TOTAL_PAGE_PRODUCTS', response.data.total)
                        resolve(response.data);
                    },
                    (err) => {
                        console.log(err);
                        reject(err);
                    }
                );
        });

    }


}

export default actions
