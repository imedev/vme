<template>

    <div id="data-list-list-view" class="data-list-container">

        <the-navbar-horizontal
            :navbarType= "navbarType"
            class="text-base" />

        <div class="mt-3 filter-section">

            <vx-card ref="filterCard" title="Filters" class="user-list-filters mb-4" actionButtons
                     @refresh="resetColFilters" @remove="resetColFilters">
                <div class="vx-row">
                    <div class="vx-col md:w-1/2 sm:w-1/2 w-full">
                        <label class="text-sm opacity-75">Brand</label>
                        <vs-input :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                  v-model="brandFilter" class="mb-4 md:mb-0 wi-strict"/>
                    </div>

                    <div class="vx-col md:w-1/2 sm:w-1/2 w-full">
                        <label class="text-sm opacity-75">Price</label>
                        <v-select :options="priceOptions" :clearable="false" :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                  v-model="priceFilter" class="mb-4 md:mb-0"/>
                    </div>
                </div>
                <div class="vx-row mt-6">

                    <div class="vx-col md:w-1/4 sm:w-1/2 w-full">

                        <div
                            class="btn-add-new rounded-lg p-3 mb-4 md:mb-0 cursor-pointer flex  items-center justify-center text-lg font-medium text-base text-primary border border-solid border-primary"
                            @click="filterDataByBrandAndPrice">
                            <feather-icon icon="CheckIcon" svgClasses="h-4 w-4"/>
                            <span class="ml-2 text-base text-primary">Filter Data</span>
                        </div>
                    </div>

                    <div class="vx-col md:w-1/4 sm:w-1/2 w-full">

                        <div
                            class="btn-add-new rounded-lg p-3 mb-4 md:mb-0 cursor-pointer flex  items-center justify-center text-lg font-medium text-base text-primary border border-solid border-primary"
                            @click="resetColFilters">
                            <feather-icon icon="RotateCwIcon" svgClasses="h-4 w-4"/>
                            <span class="ml-2 text-base text-primary">Reset Filters</span>
                        </div>
                    </div>

                    <div class="vx-col md:w-1/4 sm:w-1/2 w-full">

                        <div
                            class="btn-add-new rounded-lg p-3 mb-4 md:mb-0 cursor-pointer flex  items-center justify-center text-lg font-medium text-base text-primary border border-solid border-primary"
                            @click="mailColFilters">
                            <feather-icon icon="MailIcon" svgClasses="h-4 w-4"/>
                            <span class="ml-2 text-base text-primary">Mail csv File</span>
                        </div>
                    </div>

                </div>
            </vx-card>
        </div>

        <div class="mb-3">


            <data-view-sidebar :isSidebarActive="addNewDataSidebar" @closeSidebar="toggleDataSidebar"
                               :data="sidebarData"/>

            <div class="progress-section">
                <vx-card ref="progressCard" title="Progress Import" class="user-list-filters mb-4"  actionButtons>
                    <div class="vx-row">
                        <div class="vx-col md:w-1/2 sm:w-1/2 w-full text-center">
                            {{ imported }} %
                        </div>

                        <div class="vx-col md:w-1/2 sm:w-1/2 w-full">
                            <vs-progress :percent="imported" color="success"
                                         class="shadow-md mr-4 mb-4 flex flex-wrap-reverse items-center"/>

                        </div>

                    </div>


                </vx-card>
            </div>


            <vs-table ref="table"
                      v-model="selected"
                      :max-items="itemsPerPage"
                      search
                      pagination
                      :total="total"
                      :data="products"
                      :sst="true"
                      @search="handleSearch"
                      @change-page="handleChangePage"
                      @sort="handleSort"
                      :currentPage="currentPage"
            >


                <div slot="header" class="flex flex-wrap-reverse items-center flex-grow justify-between">

                    <div class="flex flex-wrap-reverse items-center data-list-btn-container">


                        <div
                            class="btn-add-new p-3 mb-4 mr-4 rounded-lg cursor-pointer flex items-center justify-center text-lg font-medium text-base text-primary border border-solid border-primary"
                            @click="addNewData">
                            <feather-icon icon="PlusIcon" svgClasses="h-4 w-4"/>
                            <span class="ml-2 text-base text-primary">Add New</span>
                        </div>

                        <div
                            class="btn-add-new p-3 mb-4 mr-4 rounded-lg cursor-pointer flex items-center justify-center text-lg font-medium text-base text-dark border border-solid border-dark"
                            @click="importData">
                            <feather-icon icon="ArrowDownCircleIcon" svgClasses="h-4 w-4"/>
                            <span class="ml-2 text-base text-dark">Import Data</span>
                        </div>


                    </div>


                    <!-- <vs-dropdown vs-trigger-click class="cursor-pointer mb-4 mr-4 items-per-page-handler">
                         <div
                             class="p-4 border border-solid d-theme-border-grey-light rounded-full d-theme-dark-bg cursor-pointer flex items-center justify-between font-medium">
                             <span class="mr-2">{{
                                     currentPage * itemsPerPage - (itemsPerPage - 1)
                                 }} - {{
                                     products.length - currentPage * itemsPerPage > 0 ? currentPage * itemsPerPage : products.length
                                 }} of {{ total }}</span>
                             <feather-icon icon="ChevronDownIcon" svgClasses="h-4 w-4"/>
                         </div>
                         <vs-dropdown-menu>


                             <vs-dropdown-item @click="itemsPerPage=10">
                                 <span>10</span>
                             </vs-dropdown-item>
                             <vs-dropdown-item @click="itemsPerPage=15">
                                 <span>15</span>
                             </vs-dropdown-item>
                             <vs-dropdown-item @click="itemsPerPage=20">
                                 <span>20</span>
                             </vs-dropdown-item>
                         </vs-dropdown-menu>
                     </vs-dropdown>-->


                </div>

                <template slot="thead">
                    <vs-th sort-key="name">Name</vs-th>
                    <vs-th sort-key="brand">Brand</vs-th>
                    <vs-th sort-key="barcode">Barcode</vs-th>
                    <vs-th sort-key="image_url">Image</vs-th>
                    <vs-th sort-key="price">Price</vs-th>
                    <vs-th sort-key="date_added">Date Added</vs-th>

                    <vs-th>Action</vs-th>
                </template>


                <template slot-scope="{data}">
                    <tbody>
                    <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                        <vs-td>
                            <p class="product-name font-medium truncate">{{ tr.name | title }}</p>
                        </vs-td>

                        <vs-td>
                            <p class="product-category">{{ tr.brand | title }}</p>
                        </vs-td>

                        <vs-td>
                            <p class="product-category">{{ tr.barcode | title }}</p>
                        </vs-td>

                        <vs-td class="img-container">
                            <img :src="tr.image_url" class="product-img"/>
                        </vs-td>


                        <vs-td>
                            <p class="product-price">€{{ tr.price }}</p>
                        </vs-td>

                        <vs-td>
                            <p class="product-category">{{ tr.date_added | datetime }}</p>
                        </vs-td>

                        <vs-td class="whitespace-no-wrap">
                            <feather-icon icon="EditIcon" svgClasses="w-5 h-5 hover:text-primary stroke-current"
                                          @click.stop="editData(tr)"/>
                            <feather-icon icon="TrashIcon" svgClasses="w-5 h-5 hover:text-danger stroke-current"
                                          class="ml-2" @click.stop="deleteData(tr.id)"/>
                        </vs-td>

                    </vs-tr>
                    </tbody>
                </template>


            </vs-table>


        </div>

        <the-footer />



    </div>

</template>

<script>
import axios from "../axios";
import DataViewSidebar from "./DataViewSidebar";
import vSelect from "vue-select";
import TheNavbarHorizontal from '@/views/TheNavbarHorizontal.vue'
import TheFooter from '@/views/TheFooter.vue'
import themeConfig from "../../themeConfig";


export default {
    name: "home",
    data() {
        return {
            selected: [],
            itemsPerPage: 10,
            isMounted: false,
            imported: 0,
            isInProgress: false,
            t: {},
            length: 2,
            page: 1,
            max: 10,
            // Data Sidebar
            addNewDataSidebar: false,
            sidebarData: {},

            // Filter Options
            brandFilter: "",
            priceFilter: {label: 'All', value: 'all'},

            priceOptions: [
                {label: 'All', value: 'all'},
                {label: '< €3', value: 'lt3'},
                {label: '€3 - €6', value: 'bt3-6'},
                {label: '€6 - €9', value: 'bt6-9'},
                {label: '> €9', value: 'gt9'},
            ],


            searchQuery: "",
            navbarType        : themeConfig.navbarType  || 'floating',

        }
    },
    methods: {
        importData() {
            this.isInProgres = true;
            this.imported = 0;
            this.$store.dispatch("importData")
                .then(response => {
                    this.isInProgress = true;

                })
                .catch(err => {

                    console.error(err)
                })
        },
        bachDetails() {

            return new Promise((resolve, reject) => {
                axios.get("/api/products/batchInProgress")
                    .then((response) => {
                        if (Array.isArray(response.data)) {
                            if (response.data.length === 0) {
                                this.isInProgress = false;
                                this.imported = 100

                                this.$store.dispatch("getData")
                                    .catch(err => {
                                        console.error(err)
                                    })

                            }
                        } else {
                            this.imported = response.data.progress;
                            //console.log(response.data.progress);

                        }
                        resolve(response)
                    })
                    .catch((error) => {
                        //  console.log(error)
                        reject(error)
                    })
            })
        },

        addNewData() {
            this.sidebarData = {}
            this.toggleDataSidebar(true)
        },
        deleteData(id) {
            this.$store.dispatch("removeItem", id).catch(err => {
                console.error(err)
            })
        },
        editData(data) {
            // this.sidebarData = JSON.parse(JSON.stringify(this.blankData))
            this.sidebarData = data
            this.toggleDataSidebar(true)
        },

        toggleDataSidebar(val = false) {
            this.addNewDataSidebar = val
        },

        setColumnFilter(column, val) {

        },
        resetColFilters() {

            // Reset Filter Options
            this.priceFilter = {
                label: 'All',
                value: 'all'
            }
            this.brandFilter = ""

            this.$refs.filterCard.removeRefreshAnimation();

            this.handleChangePage()
        },
        updateSearchQuery(val) {
        },

        mailColFilters() {
            return new Promise((resolve, reject) => {
                axios
                    .get(`/api/products/mailProducts?brand=${this.brandFilter}&price=${this.priceFilter.value}`
                    )
                    .then(
                        (response) => {
                            resolve(response.data);
                        },
                        (err) => {
                            console.log(err);
                            reject(err);
                        }
                    );
            });

        },

        filterDataByBrandAndPrice() {
            this.$store.dispatch("filterDataByBrandAndPrice", {brand: this.brandFilter, price: this.priceFilter.value})
                .then(response => {
                    this.products = response.data
                    this.total = response.total
                    this.currentPage = response.current_page
                })

                .catch(err => {
                    console.error(err)
                })
        },

        handleSearch(searching) {
            return new Promise((resolve, reject) => {
                axios
                    .get(`/api/products/searchProducts?search=${searching}`
                    )
                    .then(
                        (response) => {
                            console.log(response.data)
                            this.products = response.data
                            resolve(response.data);
                        },
                        (err) => {
                            console.log(err);
                            reject(err);
                        }
                    );
            });


        },
        handleChangePage(page) {

            this.$store.dispatch("paginateProducts", {
                page: page,
                itemsPerPage: this.itemsPerPage,
                brand: this.brandFilter,
                price: this.priceFilter.value
            })
                .then(response => {
                    this.products = response.data
                })

                .catch(err => {
                    console.error(err)
                })


        },
        handleSort(key, active) {

            return new Promise((resolve, reject) => {
                axios
                    .get(`/api/products/sortProducts?sort=${key}&sortBy=${active}`
                    )
                    .then(
                        (response) => {
                            console.log(response.data)
                            this.products = response.data
                            resolve(response.data);
                        },
                        (err) => {
                            console.log(err);
                            reject(err);
                        }
                    );
            });


        }
    },
    mounted() {
        this.isMounted = true;

        this.$store.dispatch("getData").catch(err => {
            console.error(err)
        })

    },
    components: {
        DataViewSidebar,
        vSelect,
        TheNavbarHorizontal,
        TheFooter
    },
    computed: {
        currentPage: {
            // getter
            get() {
                return this.$store.state.current_page
            },
            // setter
            set(newValue) {
                // Note: we are using destructuring assignment syntax here.
                this.$store.state.current_page = newValue
            }
        },
        total: {
            // getter
            get() {
                return this.$store.state.total
            },
            // setter
            set(newValue) {
                // Note: we are using destructuring assignment syntax here.
                this.$store.state.total = newValue
            },

        },

        products: {
            // getter
            get() {
                return this.$store.state.products

            },
            // setter
            set(newValue) {
                // Note: we are using destructuring assignment syntax here.
                this.$store.state.products = newValue
                this.$refs.table.datax = newValue
            }
        },
        queriedItems() {
            // return this.$refs.table ? this.$refs.table.queriedResults.length : this.products.length
        }


    },

    watch: {
        isInProgress(val) {
            if (val) { // it seems to me this additional check would make sense?
                this.t = setInterval(() => {
                    this.bachDetails()
                }, 1000)
            } else {
                clearInterval(this.t)
            }
        },

        products(newValue, oldValue) {
            console.log(`Updating products  to ${newValue}`);
        }


    },
}

</script>

<style lang="scss">
#data-list-list-view {
    .vs-con-table {

        /*
          Below media-queries is fix for responsiveness of action buttons
          Note: If you change action buttons or layout of this page, Please remove below style
        */
        @media (max-width: 689px) {
            .vs-table--search {
                margin-left: 0;
                max-width: unset;
                width: 100%;

                .vs-table--search-input {
                    width: 100%;
                }
            }
        }

        @media (max-width: 461px) {
            .items-per-page-handler {
                display: none;
            }
        }

        @media (max-width: 341px) {
            .data-list-btn-container {
                width: 100%;

                .dd-actions,
                .btn-add-new {
                    width: 100%;
                    margin-right: 0 !important;
                }
            }
        }

        .product-name {
            max-width: 23rem;
        }

        .vs-table--header {
            display: flex;
            flex-wrap: wrap;
            margin-left: 1.5rem;
            margin-right: 1.5rem;

            > span {
                display: flex;
                flex-grow: 1;
            }

            .vs-table--search {
                padding-top: 0;

                .vs-table--search-input {
                    padding: 0.9rem 2.5rem;
                    font-size: 1rem;

                    & + i {
                        left: 1rem;
                    }

                    &:focus + i {
                        left: 1rem;
                    }
                }
            }
        }

        .vs-table {
            border-collapse: separate;
            border-spacing: 0 1.3rem;
            padding: 0 1rem;

            tr {
                box-shadow: 0 4px 20px 0 rgba(0, 0, 0, .05);

                td {
                    padding: 20px;

                    &:first-child {
                        border-top-left-radius: .5rem;
                        border-bottom-left-radius: .5rem;
                    }

                    &:last-child {
                        border-top-right-radius: .5rem;
                        border-bottom-right-radius: .5rem;
                    }

                    &.img-container {
                        // width: 1rem;
                        // background: #fff;

                        span {
                            display: flex;
                            justify-content: flex-start;
                        }

                        .product-img {
                            height: 110px;
                        }
                    }

                }

                td.td-check {
                    padding: 20px !important;
                }
            }
        }

        .vs-table--thead {
            th {
                padding-top: 0;
                padding-bottom: 0;

                .vs-table-text {
                    text-transform: uppercase;
                    font-weight: 600;
                }
            }

            th.td-check {
                padding: 0 15px !important;
            }

            tr {
                background: none;
                box-shadow: none;
            }
        }

        .vs-table--pagination {
            justify-content: center;
        }
    }
}

.filter-section {
    border-collapse: separate;
    border-spacing: 0 1.3rem;
    padding: 3rem 1rem;
}

.progress-section {
    border-collapse: separate;
    border-spacing: 0 1.3rem;
    padding: 3rem 1rem;
}

.wi-strict {
    width: 100% !important;
}
</style>

