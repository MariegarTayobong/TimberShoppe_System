<template>
  <div class="cart-container">
    <h2 style="margin-bottom: 30px;">YOUR CART</h2>

    <NotifyWithXButton :message="message" v-if="message != ''" @close="message = ''"/>

    <teleport to="body">
      <ChooseReceive v-show="show_choose" @close="show_choose = false" @select_method="select_method"/>
    </teleport>

    <teleport to="body">
      <Question_first :question="question" :isOpen="isOpen" @close="isOpen=false; act_type='';" @confirm="confirm()"/>
    </teleport>


    <div style="width: 100%; height: auto; display: flex; flex-direction: row; align-items: center; justify-content: space-between; gap: 10px;">
      <label>{{ cart.length }} total rows</label>
      <div style="display: flex; flex-direction: row; align-items: center; gap: 10px;">
        <input type="checkbox" @change="selectAll($event)"> 
        <label>Select all</label>
      </div>
    </div>

    <!-- If empty -->
    <div v-if="cart.length === 0" class="empty">Your cart is empty.</div>

    <!-- Cart list -->
    <table v-else class="cart-table">
      <thead>
        <tr>
          <th></th>
          <th>Product</th>
          <th>Stock</th>
          <th>Price</th>
          <th>Order Quantity</th>
          <th>Total</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in cart" :key="item.id">
          <td>
            <input type="checkbox" @change="selectRow($event, item)" :checked="isSelectAll">
          </td>
          <td data-label="Product">{{ item.product.name }}</td>
          <td data-label="Stock">{{ item.product.quantity }}</td>
          <td data-label="Price">₱{{ item.product.price }}</td>
          <td data-label="Order-Quantity">{{ item.quantity }}</td>
          <td data-label="Total">₱{{ item.quantity * item.product.price }}</td>
          <td style="display: flex; flex-direction: row; align-items: center; justify-content: center; gap: 10px;">
            <button class="btn-single d" @click="askgoDelete(item)">Delete</button>
            <button class="btn-single v" @click="goProduct(item)">View</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Overall Checkout -->
    <div v-if="cart.length > 0" class="overall">
      <h3>Total Amount: ₱{{ overallTotal }}</h3>
      <button 
      class="btn-overall" 
      @click="show_choose = true" 
      :disabled="is_disabled" 
      :style="{backgroundColor: is_disabled ? 'gray' : '#0984e3', cursor: is_disabled ? 'default' : 'pointer'}"
      >Checkout</button>
    </div>

    <div class="box">
      <h3>ORDERED ITEMS</h3>
      <label style="color: gray; font-style: italic;">
        WARNING: Order can be cancel only in delivery processing otherwise you may contact the seller for order cancellation.
      </label>

      <div style="margin-top: 20px;">
        <select v-model="method_select_att" style="padding: 5px; width: 150px;">
          <option value="">ALL</option>
          <option value="delivery">DELIVERY</option>
          <option value="pickup">PICK UP</option>
        </select>
      </div>
      <table class="cart-table">
        <thead>
          <tr>
            <th>Delivery Address</th>
            <th>Status</th>
            <th>Method</th>
            <th>Product</th>
            <th>Stock</th>
            <th>Price</th>
            <th>Order Quantity</th>
            <th>Total</th>
            <th></th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="item of filteredOrdered_items" :key="item" :class="item.status === 'Delivery Processing' ? 'process' : item.status === 'Shipped' ? 'ship' : 'cancel'">
            <td>{{ item.delivery_loc }}</td>
            <td style="font-weight: bolder;">
              {{ item.status === 'Delivery Processing' ? 'Processing' : item.status === "Canceled" ? 'Canceled' : 'Fulfilled' }}
            </td>
            <td>{{ item.receive_method ? item.receive_method.toUpperCase() : '' }}</td>
            <td>{{ item.product.name }}</td>
            <td>{{ item.product.quantity }}</td>
            <td>{{ item.product.price }}</td>
            <td>{{ item.quantity }}</td>
            <td>₱{{ item.quantity * item.product.price }}</td>
            <td style="display: flex; flex-direction: row; gap: 10px;">
              <button class="btn-single v" @click="goProduct(item)">View</button>
              <button class="btn-single" @click="goMessage(item)">Message</button>
              <button v-if="item.status === 'Delivery Processing'" class="btn-single d" @click="goDeleteOrderAsk(item)">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { useDataStore } from '../../stores/dataStore';
import Question_first from '../../modal_global/Question_first.vue';
import NotifyWithXButton from '../notify-modal/NotifyWithXButton.vue';
import ChooseReceive from '../modals/ChooseReceive.vue';

export default {
  name: "BuyerCart",
  components: {Question_first, NotifyWithXButton, ChooseReceive},
  data() {
    return {
      cart: [
        // Example structure; replace with API
        // { id: 1, name: "Product A", price: 200, quantity: 1 },
      ],
      selected_data: [],
      isSelectAll: false,
      question: '',
      isOpen: false,
      is_disabled: false,
      order_items: [],
      message: '',
      action: '',
      selected_order_delete: null,
      receive_method: '',
      show_choose: false,
      method_select_att: '',
      delivery_location: '',
    };
  },
  computed: {
    filteredOrdered_items() {

      return this.order_items.filter(i => i.receive_method.includes(this.method_select_att));
    },
    overallTotal() {
      
      if(this.selected_data.length > 0){
        this.is_disabled = false;
        return this.selected_data.reduce((sum, item) => sum + item.quantity * item.product.price, 0);
      }
      else{
        this.is_disabled = true;
        return 0;
      }
    },
  },
  methods: {

    select_method(data) {

      this.receive_method = data.method
      this.delivery_location = data.location;

      this.askFirst();
      this.show_choose = false;
    },

    goMessage(item){
      console.log("go message: ", item);
      
      this.$router.push({name: "BuyerConversation", params: {"id": item.seller.id}});
    },

    goDeleteOrderAsk(item) {

      this.action = 'delete-order';
      this.question = "DO YOU REALLY WANT TO DELETE THIS ORDER ITEM?";
      this.isOpen = true;
      this.selected_order_delete = item;
    },

    async goDeleteOrder() {

      const res = await axios.post('/buyer/delete/cart', {id: this.selected_order_delete.id, action: 'order'});

      console.log(res.data.message);

      if(res.data.message === 'successful'){

        this.message = "";
        this.message = "DELETED SUCCESSFULLY";

        this.order_items = this.order_items.filter(item => item.id !== this.selected_order_delete.id);
      }

      this.isOpen = false;
    },

    goProduct(item) {
      const store = useDataStore();
      store.setSelectedProduct(item.product);
      this.$router.push({name: 'BuyerProduct', params: {id: item.product.id}});
    },

    async proceedToDelete() {

      const res = await axios.post('/buyer/delete/cart', {id: this.selected_order_delete.id, action: 'cart'});

      console.log(res.data.message);

      if(res.data.message === 'successful'){

        this.message = "";
        this.message = "DELETED SUCCESSFULLY";

        this.cart = this.cart.filter(cart => cart.id !== this.selected_order_delete.id);
      }

      this.isOpen = false;
    },  

    askgoDelete(item) {

      this.action = 'delete';
      this.question = "DO YOU REALLY WANT TO DELETE THIS CART ITEM?";
      this.isOpen = true;

      this.selected_order_delete = item;
    },

    confirm() {

      if(this.action === 'checkout'){
        this.proceedToCheckout();
      }
      else if(this.action === 'delete'){

        this.proceedToDelete();
      }
      else if(this.action === 'delete-order') {
        
        this.goDeleteOrder();
      }

      this.action = "";
    },

    askFirst() {

      this.action = 'checkout';
      this.question = "AFTER COMPLETING YOUR CHECKOUT, PLEASE CONTACT THE SELLER FOR SMOOTH TRANSACTION.";
      this.isOpen = true;
    },
    
    selectAll(event) {

      if(event.target.checked){

        this.isSelectAll = true;

        this.cart.map(c => this.selected_data.push(c));
      }
      else {

        this.isSelectAll = false;
        this.selected_data = [];
      }
      

      console.log('c: ', this.selected_data);
    },
    selectRow(event, item){

      if(event.target.checked){

        this.selected_data.push(item)
      }
      else{

        this.selected_data = this.selected_data.filter(data => data.id !== item.id);
      }

      console.log('bilin: ', this.selected_data);
    },
    async fetchCart() {

        const store = useDataStore();
        const id = store.currentUser_info.id;
      
        const res = await axios.post('/buyer/return/add-tocart', {'id': id});

        if(res.data.message){

            this.cart = res.data.message;
            this.order_items = res.data['order-items'];
        }
    },
    updateQuantity(item) {
      // API call to update quantity
      // axios.patch(`/api/cart/${item.id}`, { quantity: item.quantity })
    },
    checkoutSingle(item) {
      // API checkout for individual item
      // axios.post('/api/checkout/single', { product_id: item.id })
      alert(`Checkout: ${item.name}`);
    },
    deleteItemAfterSuccess(id_arr) {
      
      this.cart = this.cart.filter(item => !id_arr.includes(item.id));

      this.selected_data = [];
    },
    async proceedToCheckout(data = null) {

      if(!data){

        data = this.selected_data.map(data => data.id);
      }

      const s_data = JSON.stringify(data);

      const seller_id = this.selected_data[0].seller.id;
      const buyer_id = this.selected_data[0].buyer.id;
      const product_id = this.selected_data[0].product.id;
      
      const res = await axios.post('/buyer/checkout', {
                                                        'data': s_data, 
                                                        'seller_id': seller_id, 
                                                        'buyer_id': buyer_id, 
                                                        'product_id': product_id, 
                                                        'method': this.receive_method,
                                                        'address': this.delivery_location
                                                      });

      console.log(res.data.message);

      if(res.data.message === 'successful') {

        this.selected_data.forEach(data => {

          data.status = "Delivery Processing";
          data.action = "Checkout";
          data.receive_method = this.receive_method;
          data.delivery_loc = this.delivery_location;
          
          this.order_items.unshift(data);
        });

        this.isOpen = false;
        this.message = "ORDERED AN ITEM SUCCESSFULLY!";

        this.deleteItemAfterSuccess(data);
      }
    },
  },
  mounted() {
    this.fetchCart();
  },
};
</script>

<style scoped>
.process {
  background-color: rgba(234, 255, 48, 0.137);
}
.ship {
  background-color: rgba(48, 179, 255, 0.267);
}
.cancel {

  background-color: rgba(255, 48, 48, 0.267);
}
.box{
  margin-top: 50px;
  border-top: 1px solid rgb(182, 182, 182);
  height: 800px;
  overflow-y: scroll;
}
.cart-container {
  width: 92%;
  margin-left: 80px;
  margin-right: 80px;
  padding: 25px;
  font-family: 'Inter', Arial, sans-serif;
  margin-top: 50px;
  box-sizing: border-box;
}

h2 {
  font-size: 24px;
  font-weight: 600;
  margin-bottom: 20px;
}

.cart-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.cart-table th {
  background: #f5f5f5;
  padding: 14px;
  font-weight: 600;
  font-size: 14px;
  color: #333;
}

.cart-table td {
  padding: 16px;
  border-top: 1px solid #eee;
  font-size: 14px;
}

.cart-table tbody tr {
  text-align: center;
}

input[type="number"] {
  width: 70px;
  padding: 6px;
  border: 1px solid #ddd;
  border-radius: 6px;
}

.btn-single {
  padding: 8px 16px;
  cursor: pointer;
  background: #00b894;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 13px;
  transition: 0.2s;
}

.btn-single.v {
  background: #00a2bf;
}

.btn-single.d {
  background: #d85e06;
}

.btn-single:hover {
  opacity: 0.85;
}

.btn-overall {
  padding: 12px 20px;
  background: #0984e3;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 15px;
  font-weight: 600;
  transition: 0.2s;
}

.btn-overall:hover {
  background: #066ec2;
}

.overall {
  margin-top: 25px;
  padding: 20px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  text-align: right;
}

.empty {
  margin-top: 30px;
  font-size: 17px;
  text-align: center;
  color: #777;
}

/* --------------------------------
   RESPONSIVENESS STARTS HERE
---------------------------------- */

/* Tablet */
@media (max-width: 1024px) {
  .cart-container {
    margin-left: 20px;
    margin-right: 20px;
    width: 95%;
  }

  .cart-table th,
  .cart-table td {
    padding: 12px;
    font-size: 13px;
  }
}

/* Mobile Screens */
@media (max-width: 768px) {
  .cart-table thead {
    display: none; /* Hide header */
  }

  .cart-table,
  .cart-table tbody,
  .cart-table tr,
  .cart-table td {
    display: block;
    width: 95%;
  }

  .cart-table tr {
    margin-bottom: 15px;
    border: 1px solid #eee;
    border-radius: 12px;
    padding: 10px;
    background: #fff;
  }

  .cart-table td {
    text-align: right;
    padding: 10px 15px;
    position: relative;
  }

  .cart-table td::before {
    content: attr(data-label);
    position: absolute;
    left: 15px;
    font-weight: bold;
    font-size: 13px;
    color: #555;
  }

  .btn-single {
    width: 100%;
    margin-top: 8px;
  }

  .overall {
    text-align: center;
  }
}

/* Extra Small Phones */
@media (max-width: 480px) {
  h2 {
    font-size: 20px;
    text-align: center;
  }

  .btn-overall {
    width: 100%;
  }
}

</style>

