<template>
  <div class="order-container">

    <NotifyWithXButton :message="message" v-if="message !== ''" @close="message = ''"/>

    <teleport to="body">
      <Warning :message="warning_message" v-if="warning_message !== ''" @close="warning_message = ''"/>
    </teleport>

    <teleport to="body">
      <Question_first :question="question" :isOpen="isOpen" @close="isOpen=false; act_type='';" @confirm="confirm()"/>
    </teleport>
    
    <!-- Pending Orders -->
    <div class="card">
      <h3 class="section-title">Pending Orders</h3>

      <div class="top-bar">
        <label>{{ pendingOrders.length }} total rows</label>

        <div class="select-all">
          <input type="checkbox" @change="selectAll($event)">
          Select all
        </div>
      </div>

      <div class="table-wrapper">
        <table class="order-table">
          <thead>
            <tr>
              <th></th>
              <th>Delivery Address</th>
              <th>Method</th>
              <th>Buyer</th>
              <th>Product</th>
              <th>Order quantity</th>
              <th>Product stock</th>
              <th>Price</th>
              <th>Total</th>
              <th>Action</th>
            </tr>
          </thead>

          <tbody v-if="pendingOrders.length > 0">
            <tr v-for="order in pendingOrders" :key="order.id">
              <td><input type="checkbox" @change="selectChange($event, order)" :checked="is_selectall"></td>
              <td>{{ order.delivery_loc }}</td>
              <td>{{ order.receive_method.toUpperCase() }}</td>
              <td>{{ order.buyer.firstname }} {{ order.buyer.lastname }}</td>
              <td>{{ order.product.name }}</td>
              <td>{{ order.quantity }}</td>
              <td>{{ order.product.quantity }}</td>
              <td>₱{{ order.product.price }}</td>
              <td>₱{{ order.quantity * order.product.price }}</td>
              <td><button class="view-btn" @click="$router.push({name: 'ViewProduct', params: {id: order.product.id}})">View</button></td>
            </tr>
          </tbody>
            <div v-else style="width: 100%; text-align: center; position: absolute; padding: 20px;">
                NO ORDERS
            </div>
        </table>
      </div>

      <div class="summary">
        <label>Total: ₱{{ calculateTotal }}</label>
      </div>

      <div class="actions">
        <button class="btn ship" @click="goShipOrderAsk()" :disabled="selected_data.length < 1">Fullfill Order</button>
        <button class="btn cancel" :disabled="selected_data.length < 1" @click="goCancelAsk()">Cancel Order</button>
      </div>
    </div>

    <!-- Shipped Orders -->
    <div class="card">
      <h3 class="section-title">Orders</h3>

      <div style="padding-bottom: 10px; padding-top: 20px; display: flex; flex-direction: row; align-items: center; gap: 10px;">
        <input type="text" placeholder="Search by buyer name..." style="border: 1px solid #D2D2D2; width: 300px; padding: 5px; border-radius: 10px; padding-left: 10px;" v-model="ship_search">
        <select v-model="method_select_att" style="padding: 5px; width: 150px; border-radius: 10px;">
          <option value="">ALL</option>
          <option value="delivery">DELIVERY</option>
          <option value="pickup">PICK UP</option>
        </select>
      </div>

      <div class="table-wrapper">
        <table class="order-table">
          <thead>
            <tr>
              <th>Delivery Address</th>
              <th>Status</th>
              <th>Method</th>
              <th>Buyer</th>
              <th>Product</th>
              <th>Order quantity</th>
              <th>Product stock</th>
              <th>Price</th>
              <th>Total</th>
              <th>Action</th>
            </tr>
          </thead>

          <tbody v-if="shippedOrders.length > 0">
            <tr v-for="order in filterShipped" :key="order.id">
              <td>{{ order.delivery_loc }}</td>
              <td style="font-weight: bolder; font-style: italic;" :style="{color: returnColor(order.status)}">{{ order.status === 'Shipped' ? 'ORDER FULFILLED' : order.status.toUpperCase() }}</td>
              <td>{{ order.receive_method.toUpperCase() }}</td>
              <td>{{ order.buyer.firstname }} {{ order.buyer.lastname }}</td>
              <td>{{ order.product.name }}</td>
              <td>{{ order.quantity }}</td>
              <td>{{ order.product.quantity }}</td>
              <td>₱{{ order.product.price }}</td>
              <td>₱{{ order.quantity * order.product.price }}</td>
              <td style="display: flex; flex-direction: row; align-items: center; gap: 10px;">
                <button class="view-btn" @click="$router.push({name: 'ViewProduct', params: {id: order.product.id}})">View</button>
                <button class="delivered-btn" @click="goDeliver_askfirst(order)" v-if="order.status !== 'Delivered' && order.status !== 'Canceled'">Item received</button>
                <button class="remove-btn" @click="goRemove_askfirst(order)" v-if="order.status !== 'Delivered'">Remove</button>
              </td>
            </tr>
          </tbody>
          <div v-else style="width: 100%; text-align: center; position: absolute; padding: 20px;">
                NO ORDERS
            </div>
        </table>
      </div>

    </div>

  </div>
</template>

<script>
import axios from 'axios';
import { useDataStore } from '../../stores/dataStore';

import Question_first from '../../modal_global/Question_first.vue';
import NotifyWithXButton from '../../buyer/notify-modal/NotifyWithXButton.vue';
import Warning from '../modal/Warning.vue';

export default {
    components: {Question_first, NotifyWithXButton, Warning},
    data() {
        return {
          method_select_att: '',
          message: '',
          totalPending: 0,
          totalAmount: 0,
          pendingOrders: [],
          shippedOrders: [],
          is_selectall: false,
          is_disabled: false,
          selected_data: [],
          question: '',
          isOpen: false,
          act_type: '',
          products: null,
          warning_message: '',
          ship_search: '',
          selected_order: null,
        }
    },
    watch: {

    },
    computed: {

      filterShipped() {

        let data = this.shippedOrders.filter(data => {

          let name = `${data.buyer.firstname} ${data.buyer.lastname}`;

          return name.toLowerCase().includes(this.ship_search.toLowerCase());
        });

        data = data.filter(d => {
          
          return d.receive_method.includes(this.method_select_att);
        })

        console.log('data soy: ', data);

        return data;
      },
      
      calculateTotal() {

        if(this.selected_data.length === 0){
          return 0;
        }

        let total = this.selected_data.reduce((sum, acc) => sum + (acc.quantity * acc.product.price), 0);

        return total;
      }
    },  
    methods: {

      goRemove_askfirst(order) {

        this.selected_order = order;
        this.act_type = 'item-remove';
        this.isOpen = true;
        this.question = "DO YOU REALLY WANT TO REMOVE THIS ORDER?";
      },

      async goRemove() {

        let order = this.selected_order;

        const res = await axios.post('/seller/remove-order', {id: order.id});

        if(res.data.message === 'successful'){

          this.message = "SUCCESSFULLY REMOVE AN ORDER";
          this.shippedOrders = this.shippedOrders.filter(data => data.id !== order.id);
        }
      },

      goDeliver_askfirst(order) {

        this.act_type = "item-received"
        this.selected_order = order;
        this.isOpen = true;
        this.question = "DO YOU REALLY WANT TO SET THIS ORDER AS ITEM RECEIVED?";
      },

      async goDeliver() {

        let order = this.selected_order;

        console.log('order: ', order);

        const data = new FormData();
        data.append('seller_id', order.seller.id);
        data.append('buyer_id', order.buyer.id);
        data.append('product_id', order.product.id);
        data.append('id', order.id);

        const res = await axios.post('/seller/item-delivered', order);


        if(res.data.message === 'successful') {

          this.pendingOrders = res.data.pending;
          this.shippedOrders = res.data.shipped;
          this.message = "Successfully change the status as 'delivered'";
        }
      },

      selected_data_check(d_data) {

        let data = {};
        let sum = 0;

        console.log('hey');

        let newval = d_data;

        console.log(newval.length);

        if(newval.length > 0) {

          for(let d of newval) {

            console.log('d: ', d);

            if(Object.keys(data).includes(d.product.name)){

              sum = data[d.product.name] + d.quantity;
              
              if(sum > d.product.quantity) {

                this.warning_message = `CAN'T SELECT NO MORE AS PRODUCT ${d.product.name} has only stock of ${d.product.quantity}`;
                return false;
              }

              data[d.product.name] = sum;
            }
            else {

              if(d.quantity > d.product.quantity) {

                this.warning_message = `CAN'T SELECT NO MORE AS PRODUCT ${d.product.name} has only stock of ${d.product.quantity}`;
                return false;
              }

              data[d.product.name] = d.quantity;
            }
          }
        }

        return true;
      },

      returnColor(status) {

        switch(status) {

          case 'Shipped':
            return 'blue';
          case 'Canceled':
            return 'red';
        }
      },

      confirm() {
      
        this.question = "";
        this.isOpen = false;

        switch(this.act_type) {

          case 'ship':
            this.goShipOrder();
            break;
          case 'canceled':
            this.goCancel();
            break;
          case 'item-received':
            this.goDeliver();
            break;
          case 'item-remove':
            this.goRemove();
            break;
        }

        this.act_type = "";
      },

      async goShipOrder() {

        const id_arr = this.selected_data.map(data => data.id);
        const data = JSON.stringify(id_arr);

        const store = useDataStore();
        const id = store.currentUser_info.id;

        const id_data = JSON.stringify({
          seller_id: this.selected_data[0].seller.id, 
          buyer_id: this.selected_data[0].buyer.id, 
          product_id: this.selected_data[0].product.id}
        )

        const res = await axios.post('/seller/ship-order', {id: id, data: data, 'id_data': id_data});

        console.log(res.data.message);
        if(res.data.message === "successful"){

          this.message = '';
          this.message = "SUCCESSFULLY FULFILLED ORDER";

          this.pendingOrders = res.data.pending;
          this.shippedOrders = res.data.shipped;
        }

        this.selected_data = [];
      },

      async goCancel() {

        const id_arr = this.selected_data.map(data => data.id);
        const data = JSON.stringify(id_arr);

        const store = useDataStore();
        const id = store.currentUser_info.id;

        const id_data = JSON.stringify({
          seller_id: this.selected_data[0].seller.id, 
          buyer_id: this.selected_data[0].buyer.id, 
          product_id: this.selected_data[0].product.id}
        )

        const res = await axios.post('/seller/cancel-order', {id: id, data: data, 'id_data': id_data});

        console.log(res.data.message);
        if(res.data.message === "successful"){

          this.message = '';
          this.message = "SUCCESSFULLY CANCELED ORDER";

          this.pendingOrders = res.data.pending;
          this.shippedOrders = res.data.shipped;
        }

        this.selected_data = [];
      },

      goCancelAsk() {

        this.act_type = "canceled";
        this.question = "DO YOU REALLY WANT TO CANCEL THIS ORDER/S";
        this.isOpen = true;
      },

      goShipOrderAsk() {

        console.log('hello po')

        this.act_type = "ship";
        this.question = "DO YOU REALLY WANT TO FULFILL THIS ORDER/S";
        this.isOpen = true;
      },

        selectChange(e, item){

          console.log(this.selected_data);
          console.log(item);

          if(e.target.checked){

            const d_data = [...this.selected_data, item];

            if(this.selected_data_check(d_data)) {

              this.selected_data = [...this.selected_data, item];
            } 
            else{

              e.target.checked = false;
            }
          }
          else{

            this.selected_data = this.selected_data.filter(data => data.id !== item.id);
          }

          console.log('selected item: ', this.selected_data);
        },
        selectAll(e) {

          if(e.target.checked){
            this.is_selectall = true;
            let data = this.pendingOrders;

            if(this.selected_data_check(data)) {

              this.selected_data = data;
            }
            else{

              this.is_selectall = false;
              this.selected_data = [];
            }
          }
          else{
            this.is_selectall = false;
            this.selected_data = [];
          }
        },

        async returnOrders() {

            const store = useDataStore();
            const id = store.currentUser_info.id;

            const res = await axios.post('/seller/return-all/orders', {id: id});
            
            console.log('kaneh: ',res.data.pending_orders);

            if(res.data.shipped_orders){

                this.shippedOrders = res.data.shipped_orders;
            }
            if(res.data.pending_orders) {

                this.pendingOrders = res.data.pending_orders;
            }

            this.products = res.data.products;
        },
    },
    mounted() {
      this.returnOrders();
    }
}
</script>

<style scoped>
.btn.ship.disable {
  background-color: gray;
}
.btn.cancel.disable {
  background-color: gray;
}
.order-container {
  width: 100%;
  padding: 30px;
  display: flex;
  flex-direction: column;
  gap: 30px;
  font-family: Arial, Helvetica, sans-serif;
}

/* Card */
.card {
  background: #fff;
  border-radius: 10px;
  padding: 20px;
  border: 1px solid #e4e4e4;
  box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}

/* Section Title */
.section-title {
  margin-bottom: 15px;
  font-size: 20px;
  font-weight: 600;
  color: #333;
}

/* Top Bar */
.top-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}

.select-all {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  position: relative;
}

/* Table wrapper */
.table-wrapper {
  width: 100%;
  height: 400px;
  overflow-y: auto;
  border: 1px solid #ddd;
  border-radius: 8px;
}

/* Table */
.order-table {
  width: 100%;
  border-collapse: collapse;
}

.order-table thead {
  position: sticky;
  top: 0;
  background: #fafafa;
  z-index: 10;
}

.order-table th,
.order-table td {
  padding: 10px;
  border-bottom: 1px solid #eee;
  font-size: 14px;
  text-align: left;
}

/* Hover row effect */
.order-table tbody tr:hover {
  background: #f5f6ff;
}

/* Summary */
.summary {
  display: flex;
  justify-content: flex-end;
  padding: 10px 0;
  font-size: 16px;
  font-weight: 600;
}

/* Buttons */
.actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}

.btn {
  padding: 8px 15px;
  border-radius: 6px;
  cursor: pointer;
  border: none;
  font-weight: 600;
  transition: 0.2s;
}

.ship {
  background: #4caf50;
  color: white;
}

.ship:hover {
  background: #3c9440;
}

.cancel {
  background: #e53935;
  color: white;
}

.cancel:hover {
  background: #c62828;
}

.view-btn {
  padding: 5px 12px;
  background: #2196f3;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 12px;
}

.delivered-btn{
  padding: 5px 12px;
  background: #169c49;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 12px;
}

.remove-btn{  
  padding: 5px 12px;
  background: #db6019;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 12px;
}

.delivered-btn:hover {

  background-color: rgb(13, 99, 46)
}

.view-btn:hover {
  background: #1976d2;
}
</style>
