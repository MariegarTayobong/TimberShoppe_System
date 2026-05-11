<template>
  <div class="overlay" @click="$emit('exit_addtocart_modal')">

    <teleport to="body">
      <ChooseReceive v-show="show_choose" @close="show_choose = false" @select_method="select_method"/>
    </teleport>

    <teleport to="body">
      <Question_first :question="question" :isOpen="isOpen" @close="isOpen=false; act_type='';" @confirm="confirm()"/>
    </teleport>

    <Notify :message="message" v-if="message != ''"/>

    <teleport to="body">
        <div class="is-loading" v-if="is_loading">
            <img src="../../../images/kOnzy.gif" style="width: 100px; height: 100px;">
        </div>
    </teleport>

    <div class="add-to-cart-container" @click.stop>
    <div style="margin-bottom: 20px; margin-left: 20px;">
        <label>CHECKOUT THIS PRODUCT?</label>
    </div>
      <div class="content-wrapper">


        <!-- Product Image -->
        <div class="image-box">
          <img :src="'/' + product.photos[0].filename" alt="Product Image" />
        </div>

        <!-- Product Details -->
        <div class="info-box">
          <h2 class="title">{{ product.name }}</h2>
          <p class="desc">{{ product.description }}</p>

          <div class="price-stock">
            <span class="price">₱{{ product.price }}</span>
            <span class="stock">Stock: {{ product.quantity }}</span>
          </div>

          <!-- Quantity -->
          <div class="qty-control">
            <button @click="decreaseQty">-</button>
            <input type="number" v-model="quantity" />
            <button @click="increaseQty">+</button>
          </div>

          <button class="add-btn" @click="is_exist ? addToCart() : show_choose = true">Checkout</button>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { useDataStore } from '../../stores/dataStore';
import Notify from '../notify-modal/Notify.vue';
import ChooseReceive from './ChooseReceive.vue';
import Question_first from '../../modal_global/Question_first.vue';

export default {
    components: {Notify, ChooseReceive, Question_first},
  props: ['product', 'is_exist'],
  data() {
    return {
      quantity: 1,
      is_loading: false,
      message: "",
      receive_method: '',
      delivery_loc: '',
      show_choose: false,
      question: '',
      isOpen: false,
    };
  },
  methods: {
    askFirst() {

      this.question = "AFTER COMPLETING YOUR CHECKOUT, PLEASE CONTACT THE SELLER FOR SMOOTH TRANSACTION.";
      this.isOpen = true;
    },

    confirm() {

      this.addToCart();
      this.isOpen = false;
      this.question = "";
    },
    select_method(data) {

      this.receive_method = data.method;
      this.delivery_loc = data.location;
      this.show_choose = false;


      this.askFirst();
    },
    decreaseQty() {
      if (this.quantity > 1) this.quantity--;
    },
    increaseQty() {
      if (this.quantity < this.product.quantity) this.quantity++;
    },
    async addToCart() {

      try{
        this.is_loading = true;
        const store = useDataStore();

        const buyer = store.currentUser_info;

        console.log('buyer: ', buyer);
        console.log('product: ', this.product);
            
        const data = new FormData();
        data.append('quantity', this.quantity);
        data.append('product_id', this.product.id);
        data.append('seller_id', this.product.shop.user_id);
        data.append('buyer_id', buyer.id);
        data.append('method', this.receive_method);
        data.append('address', this.delivery_loc)

        const res = await axios.post('/buyer/direct-checkout', data);

        console.log(res.data.message);

        if(res.data.message === 'successful'){

            this.message = "CHECKED OUT SUCCESSFULLY";
            this.quantity = 1;

            this.$emit('success_method');
        }

      }
      catch(e){
        
        console.log(e);
      }
      finally{
        this.is_loading = false;
      }
    },
  },
};
</script>

<style scoped>
.is-loading {
    width: 100%;
    height: 100vh;
    background-color: rgba(0, 0, 0, 0.613);
    z-index: 999999;
    position: fixed;
    display: flex; flex-direction: row; align-items: center; justify-content: center;
    left: 0;
    top: 0;
}
.overlay {
  position: fixed;
  height: 100vh;
  width: 100%;
  background-color: rgba(0, 0, 0, 0.726);
  top: 0;
  left: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 10px;
}

.add-to-cart-container {
  width: 70%;
  background-color: white;
  height: auto;
  padding: 20px;
  border-radius: 10px;
}

.content-wrapper {
  display: flex;
  flex-direction: row;
  gap: 20px;
}

.image-box img {
  width: 250px;
  height: 250px;
  object-fit: cover;
  border-radius: 10px;
}

.info-box {
  display: flex;
  flex-direction: column;
  flex: 1;
}

/* Title + text */
.title {
  font-size: 1.4rem;
  font-weight: bold;
  margin-bottom: 8px;
}

.desc {
  color: gray;
  margin-bottom: 10px;
}

.price-stock {
  display: flex;
  justify-content: space-between;
  margin-bottom: 15px;
}

.price {
  font-size: 1.4rem;
  font-weight: bold;
}

.stock {
  color: gray;
  font-size: 20px;
}

/* Quantity */
.qty-control {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 15px;
}

.qty-control input {
  width: 60px;
  text-align: center;
  padding: 5px;
  border: 1px solid #ccc;
  border-radius: 5px;
}

.qty-control button {
  padding: 5px 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
  background: white;
  cursor: pointer;
}

/* Add button */
.add-btn {
  background-color: #1a73e8;
  color: white;
  padding: 12px;
  width: 200px;
  border-radius: 8px;
  cursor: pointer;
  border: none;
  font-size: 1rem;
}

/* RESPONSIVENESS */
@media (max-width: 768px) {
  .content-wrapper {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }

  .image-box img {
    width: 200px;
    height: 200px;
  }

  .price-stock {
    flex-direction: column;
    gap: 5px;
  }

  .add-btn {
    max-width: 300px;
  }
      .info-box {
        align-items: center;
    }
}

@media (max-width: 480px) {
    .info-box {
        align-items: center;
    }
  .add-to-cart-container {
    padding: 10px;
  }

  .image-box img {
    width: 150px;
    height: 150px;
  }

  .title {
    font-size: 1.2rem;
  }

  .price {
    font-size: 1.2rem;
  }

  .qty-control input {
    width: 45px;
  }

  .qty-control button {
    padding: 5px 8px;
  }
}
</style>
