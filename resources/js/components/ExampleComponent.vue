<template>
    <div class="container">
        <div class="grid grid-cols-2 mb-5">
            <div class="text-left">
                <h1 class="font-bold text-xl">{{info.name}}</h1>
            </div>
            <div class="text-right">
                <h1 class="font-bold text-2xl text-green-500">Rp. {{numberFormat(info.balance.idr)}}</h1>
            </div>
        </div>
        <div class="grid grid-cols-3 gap-4">
            <div class="flex flex-row bg-white shadow-sm rounded p-4">
                <div class="flex flex-col flex-grow ml-4">
                    <div class="text-sm text-gray-500">
                        <div class="col-span-12 lg:col-span-8">
                            <a href="#" class="inline-block rounded-full text-white
                        bg-yellow-500
                        text-xs font-bold
                        mr-1 md:mr-2 mb-2 px-2 md:px-4 py-1 ">
                                BTC / IDR
                            </a>
                        </div>
                    </div>
                    <div class="font-bold text-lg">1 BTC = Rp. {{numberFormat(summaries.tickers.btc_idr.last)}} </div>
                    <div class="text-gray-300 text-sm">Current Have: {{info.balance.btc}}</div>
                </div>
            </div>
            <div class="flex flex-row bg-white shadow-sm rounded p-4">
                <div class="flex flex-col flex-grow ml-4">
                    <div class="text-sm text-gray-500">
                        <div class="col-span-12 lg:col-span-8">
                            <a href="#" class="inline-block rounded-full text-black
                        bg-blue-100
                        text-xs font-bold
                        mr-1 md:mr-2 mb-2 px-2 md:px-4 py-1 ">
                                ETC / IDR
                            </a>
                        </div>
                    </div>
                    <div class="font-bold text-lg">1 ETC = Rp. {{numberFormat(summaries.tickers.etc_idr.last)}} </div>
                    <div class="text-gray-300 text-sm">Current Have: {{info.balance.etc}}</div>
                </div>
            </div>
            <div class="flex flex-row bg-white shadow-sm rounded p-4">
                <div class="flex flex-col flex-grow ml-4">
                    <div class="text-sm text-gray-500">
                        <div class="col-span-12 lg:col-span-8">
                            <a href="#" class="inline-block rounded-full text-white
                        bg-black
                        text-xs font-bold
                        mr-1 md:mr-2 mb-2 px-2 md:px-4 py-1 ">
                                XRP / IDR
                            </a>
                        </div>
                    </div>
                    <div class="font-bold text-lg">1 XRP = Rp. {{numberFormat(summaries.tickers.xrp_idr.last)}} </div>
                    <div class="text-gray-300 text-sm">Current Have: {{info.balance.xrp}}</div>
                </div>
            </div>
        </div>

        <div>
            <!-- <table class="table table-striped table-borderd">
                <thead id="btc_table">
                    <tr>
                        <th>Date</th>
                        <th>Price</th>
                        <th>Amount</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="trd in btc_market.rows" :key="trd.tid">
                        <td>{{trd.date}}</td>
                        <td>{{trd.price}}</td>
                        <td>{{trd.amount}}</td>
                        <td>{{trd.type}}</td>
                    </tr>
                </tbody>
            </table> -->
            <!-- <datatable :columns="btc_market.columns" :data="btc_market.rows"></datatable> -->
            <!-- <datatable-pager v-model="btc_market.currentPage" type="abbreviated" :per-page="btc_market.perPage"></datatable-pager> -->

        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                info: {
                    name: '',
                    balance: {
                        idr: 0,
                        btc: 0,
                        etc: 0,
                        xrp: 0
                    }
                },
                summaries: {
                    tickers: {
                        btc_idr: {
                            last: 0
                        },
                        etc_idr: {
                            last: 0
                        },
                        xrp_idr: {
                            last: 0
                        }
                    }
                },
                btc_market: {
                    columns: ['date', 'amount', 'price', 'type'],
                    rows: [],
                    perPage: 10,
                    currentPage: 1
                },
                btcTable: null
            }
        },
        mounted() {
            setInterval(() => {
                this.getSummaries()
                this.getInfo()
                this.getTrades('btcidr')
            }, 3000)
        },
        created () {
            // this.btcTable =  $('#btc_table').Datatable({})
        },
        methods: {
            getSummaries() {
                axios.get('/api/indodax/summaries').then(resp => {
                    this.summaries = resp.data
                })
            },
            getInfo() {
                axios.get('/api/indodax/get_info').then(resp => {
                    this.info = resp.data.return
                })
            },
            getTrades(pair_id) {
                axios.get(`/api/indodax/trades/${pair_id}`).then(resp => {
                    this.btc_market.rows = resp.data
                })
            },
            numberFormat(num) {
                return parseInt(num).toLocaleString('id')
            }
        },
    }

</script>
