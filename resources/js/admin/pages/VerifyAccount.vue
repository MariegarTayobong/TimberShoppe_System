<template>
  <div class="verify-container">

    <NotifyWithXButton :message="message_notify" v-if="message_notify !== ''" @close="message_notify = ''"/>

    <!-- Modal -->
    <teleport to="body">
      <VerifySeemore 
        v-if="showModal && selected_account" 
        :account="selected_account"
        @close="showModal = false; selected_account = null;"
      />
    </teleport>

    <teleport to="body">
      <TakeAction 
      @close="show_take_action = false;" 
      @success="goSuccess"
      v-if="show_take_action"
      :account="selected_account"
      />
    </teleport>

    <h1>Account Verification</h1>

    <!-- FILTER / SEARCH -->
    <div class="filter-card">
      <h3>Filter Search</h3>

      <div class="filter-grid">
        <select v-model="search.search_by">
          <option value="" disabled>Search by</option>
          <option value="username">Username</option>
          <option value="sellername">Seller name</option>
          <option value="shopname">Shop name</option>
        </select>

        <input 
          type="text" 
          v-model="search.text"
          placeholder="Search an account verification..."
        />

        <button class="search-btn">Search</button>
      </div>
    </div>

    <p class="total-label">
      {{ data ? data.length : 0 }} account/s waiting for verification
    </p>

    <!-- TABLE CARD -->
    <div class="table-card">
      <table class="verify-table">
        <thead>
          <tr>
            <th>Seller</th>
            <th>Username</th>
            <th>Email</th>
            <th>Shop</th>
            <th style="width: 180px;">Action</th>
          </tr>
        </thead>

        <tbody v-if="data && data.length > 0">
          <tr v-for="account in filterData" :key="account.id">
            <td>{{ account.seller.firstname }} {{ account.seller.mname }} {{ account.seller.lastname }}</td>
            <td>{{ account.seller.name }}</td>
            <td>{{ account.seller.email }}</td>
            <td>{{ account.seller.shop.name }}</td>

            <td class="action-col">
              <button class="view-btn" @click="goView(account)">
                View
              </button>

              <button class="action-btn" @click="goTakeAction(account)">
                Take Action
              </button>
            </td>
          </tr>
        </tbody>

        <tbody v-else>
          <tr style="text-align: center;">
            <td colspan="10">
              <label>NO RESULT</label>
            </td>
          </tr>
        </tbody>

      </table>
    </div>

  </div>
</template>


<script>
import VerifySeemore from '../modals/VerifySeemore.vue';
import NotifyWithXButton from '../../buyer/notify-modal/NotifyWithXButton.vue';
import TakeAction from '../modals/TakeAction.vue';
import axios from 'axios';
export default {
    components: {
        VerifySeemore, TakeAction, NotifyWithXButton
    },
    computed: {

        filterData() {

            switch(this.search.search_by) {

                case 'sellername':
                    return this.data.filter(d => {

                        const name = `${d.seller.firstname} ${d.seller.lastname}`;

                        return name.toLowerCase().includes(this.search.text.toLowerCase());
                    })
                    break;
                case 'username':
                    return this.data.filter(d => {

                        return d.seller.name.toLowerCase().includes(this.search.text.toLowerCase());
                    })
                    break;
                case 'shopname':
                    return this.data.filter(d => {

                        return d.seller.shop.name.toLowerCase().includes(this.search.text.toLowerCase());
                    })
                    break;
                default: 
                    return this.data;
            }
        }
    },
    data() {

        return{
            show_take_action: false,
            search: {
                search_by: '',
                text: '',
            },
            showModal: false,
            data: null,
            selected_account: null,
            message_notify: '',
        }
    },
    methods: {

        async goSuccess(action) {

          await this.returnVerifyAccounts();

          if(action === 'verify'){
            this.message_notify = 'SUCCESSFULLY VERIFIED AN ACCOUNT.';
            this.show_take_action = false;
          }
          else {

            this.message_notify = 'SUCCESSFULLY REJECTED AN ACCOUNT FOR VERIFICATION.';
            this.show_take_action = false;
          }
        },

        goTakeAction(account) {

          this.selected_account = account;
          this.show_take_action = true;
        },

        goView(account) {

            this.selected_account = account;
            this.showModal = true;
        },

        async returnVerifyAccounts() {

            const res = await axios.get('/admin/return-verify/accounts');

            console.log('data: ',res.data.data);
            this.data = res.data.data;
        }
    },
    mounted() {

        this.returnVerifyAccounts();
    }
}
</script>

<style scoped>
.verify-container {
  width: 100%;
  padding: 30px 60px;
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
  gap: 25px;
  background-color: #f4f6fa;
}

/* --- Page Title --- */
h1 {
  font-size: 28px;
  font-weight: 700;
  color: #2f3542;
  letter-spacing: 0.5px;
}

/* --- Filter Card --- */
.filter-card {
  background: #fff;
  padding: 20px 25px;
  border-radius: 10px;
  border: 1px solid #e3e3e3;
  box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.filter-card h3 {
  margin: 0 0 15px 0;
  font-size: 17px;
  font-weight: 600;
}

.filter-grid {
  display: grid;
  grid-template-columns: 200px 1fr 150px;
  gap: 15px;
}

.filter-grid select,
.filter-grid input {
  padding: 10px;
  border: 1px solid #d4d4d4;
  border-radius: 6px;
  font-size: 14px;
}

.search-btn {
  background-color: #0066ff;
  color: white;
  border: none;
  padding: 10px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  transition: 0.2s;
}
.search-btn:hover {
  background-color: #0052cc;
}

/* --- Total Count Label --- */
.total-label {
  font-size: 14px;
  color: #555;
  margin-top: -10px;
}

/* --- Table Card --- */
.table-card {
  background: white;
  padding: 0;
  border-radius: 10px;
  border: 1px solid #e0e0e0;
  overflow: hidden;
  box-shadow: 0 2px 6px rgba(0,0,0,0.06);
}

/* --- Table Styling --- */
.verify-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
}

.verify-table thead {
  background-color: #f1f3f7;
}

.verify-table th {
  text-align: left;
  padding: 12px 15px;
  font-weight: 600;
  color: #444;
  border-bottom: 1px solid #ddd;
}

.verify-table td {
  padding: 12px 15px;
  border-bottom: 1px solid #eee;
  color: #333;
}

.verify-table tr:hover {
  background-color: #f8faff;
}

/* --- Action Buttons --- */
.action-col {
  display: flex;
  gap: 10px;
}

.view-btn {
  padding: 6px 12px;
  font-size: 13px;
  background: #007bff;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: 0.2s;
}
.view-btn:hover {
  background: #0062cc;
}

.action-btn {
  padding: 6px 12px;
  font-size: 13px;
  background: #f1c40f;
  color: black;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: 0.2s;
}
.action-btn:hover {
  background: #d4ac0d;
}

</style>