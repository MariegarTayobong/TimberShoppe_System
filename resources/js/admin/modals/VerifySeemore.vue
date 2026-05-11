<template>
  <div class="verify-overlay" @click="$emit('close')">

    <teleport to="body">
      <PicZoom 
        v-if="selected_path && show_zoom"
        :path="selected_path"
        @close="show_zoom = false; selected_path = null;"
      />
    </teleport>

    <div class="verify-container" @click.stop>
        
      <!-- Header -->
      <div class="header">
        <h2>Seller Verification Details</h2>
        <button class="close-btn" @click="$emit('close')">✕</button>
      </div>

      <!-- ACCOUNT INFORMATION -->
      <div class="section-card">
        <h3>Account Information</h3>

        <div class="profile-row">
          <img :src="'/' + account.seller.shop.profile_photo" class="profile-img">
          <div>
            <h4>{{ account.seller.firstname }} {{ account.seller.lastname }}</h4>
            <p class="username">@{{ account.seller.name }}</p>
          </div>
        </div>

        <div class="info-grid">
          <div>
            <label>Email</label>
            <p>{{ account.seller.email }}</p>
          </div>
          <div>
            <label>Gender</label>
            <p>{{ account.seller.gender }}</p>
          </div>
          <div>
            <label>Birthday</label>
            <p>{{ account.seller.birthday }}</p>
          </div>
          <div>
            <label>Age</label>
            <p>{{ account.seller.age }}</p>
          </div>
          <div>
            <label>Contact Number</label>
            <p>{{ account.seller.contact_no }}</p>
          </div>
        </div>
      </div>

      <!-- SHOP INFORMATION -->
      <div class="section-card">
        <h3>Shop Information</h3>

        <div class="info-grid">
          <div>
            <label>Shop Name</label>
            <p>{{ account.seller.shop.name }}</p>
          </div>
          <div>
            <label>Address</label>
            <p>{{ account.seller.shop.address }}</p>
          </div>
          <div>
            <label>Description</label>
            <p>{{ account.seller.shop.description }}</p>
          </div>
          <div>
            <label>Categories</label>
            <div class="category-badges">
              <span v-for="cat in JSON.parse(account.seller.shop.category)" :key="cat" class="badge">
                {{ cat }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- VERIFICATION REQUIREMENTS -->
      <div class="section-card">
        <h3>Verification Requirements</h3>

        <div class="info-grid">
          <div>
            <label>Valid ID Type</label>
            <p>{{ account.validID_type }}</p>
          </div>
        </div>

        <div class="pic-grid">

          <div class="pic-box" @click="goZoom(account.selfie_pic)">
            <img :src="'/' + account.selfie_pic">
            <label>Selfie with Valid ID</label>
          </div>

          <!-- <div class="pic-box" @click="goZoom(account.birth_cert_pic)">
            <img :src="'/' + account.birth_cert_pic">
            <label>Birth Certificate</label>
          </div> -->

          <div class="pic-box" @click="goZoom(account.dti_pic)">
            <img :src="'/' + account.dti_pic">
            <label>DTI</label>
          </div>

          <div class="pic-box" @click="goZoom(account.business_permit)">
            <img :src="'/' + account.business_permit">
            <label>BIR CERTIFICATE OF REGISTRATION (COR)</label>
          </div>

        </div>
      </div>

    </div>
  </div>
</template>


<script>
  import PicZoom from './PicZoom.vue';
export default {
  components: {PicZoom},
  props: ['account'],
  data() {

    return{
      selected_path: null,
      show_zoom: false,
    }
  },
  methods: {

    goZoom(path) {

      this.selected_path = path;
      this.show_zoom = true;
    }
  }
}
</script>

<style scoped>

.verify-overlay {
  position: fixed;
  inset: 0;
  background-color: rgba(0, 0, 0, 0.65);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 999999;
}

.verify-container {
  width: 75%;
  max-height: 90vh;
  background: #fff;
  border-radius: 12px;
  padding: 25px;
  overflow-y: auto;
  box-shadow: 0 8px 30px rgba(0,0,0,0.25);
  position: relative;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 25px;
}

.header h2 {
  margin: 0;
  font-weight: 600;
}

.close-btn {
  background: none;
  border: none;
  font-size: 22px;
  cursor: pointer;
  padding: 5px;
  transition: 0.2s;
}
.close-btn:hover {
  color: red;
}

.section-card {
  background: #fafafa;
  padding: 18px 20px;
  border-radius: 10px;
  margin-bottom: 25px;
  border: 1px solid #ddd;
}

.section-card h3 {
  margin-bottom: 15px;
  font-size: 18px;
  border-left: 4px solid #007bff;
  padding-left: 10px;
  font-weight: 600;
}

.profile-row {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 20px;
}

.profile-img {
  width: 65px;
  height: 65px;
  border-radius: 50%;
  border: 2px solid #ddd;
  background: white;
}

.username {
  font-size: 14px;
  color: gray;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 15px 20px;
}

.info-grid label {
  font-size: 12px;
  font-weight: 600;
  color: gray;
}

.info-grid p {
  margin: 3px 0 0;
  font-size: 14px;
}

.category-badges {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
}

.badge {
  background: #e8f1ff;
  padding: 3px 8px;
  border-radius: 5px;
  font-size: 12px;
  color: #005fcc;
}

.pic-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 25px;
  margin-top: 10px;
}

.pic-box {
  background: #fff;
  border: 1px solid #ccc;
  border-radius: 8px;
  padding: 10px;
  cursor: pointer;
  transition: 0.25s;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.pic-box img {
  width: 100%;
  height: 230px;
  object-fit: cover;
  border-radius: 6px;
}

.pic-box:hover {
  transform: scale(1.03);
  border-color: #007bff;
  box-shadow: 0 3px 10px rgba(0,0,0,0.15);
}

.pic-box label {
  margin-top: 10px;
  font-size: 14px;
  font-weight: 600;
}

</style>
