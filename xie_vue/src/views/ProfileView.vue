<template>
  <div class="profile">
    <div class="profile-header">
      <img class="avatar" :src="avatarUrl" alt="avatar" />
      <div>
        <h2>沒良心先生</h2>
        <p>會員等級：黑心VIP</p>
        <p>累積消費：$99999</p>
        <div class="upload-btn">
          <input type="file" accept="image/*" @change="onFileChange" id="avatarInput" style="display:none" />
          <button @click="triggerFileInput">上傳大頭照</button>
          <!--
          // 這裡應該呼叫 API 上傳圖片到後端並儲存
          // 例如：
          // const formData = new FormData();
          // formData.append('avatar', file);
          // axios.post('/api/upload-avatar', formData)
          -->
        </div>
      </div>
    </div>
    <div class="section">
      <h3>購物紀錄</h3>
      <ul class="orders">
        <li>
          <div class="order-title">Whey Protein多口味乳清蛋白飲 (500/1kg裝)</div>
          <div class="order-detail">數量：1 | 狀態：<span class="status received">已收貨</span></div>
        </li>
        <li>
          <div class="order-title">隔日配玩偶 濃魚娃娃</div>
          <div class="order-detail">數量：1 | 狀態：<span class="status waiting">待收貨</span></div>
        </li>
      </ul>
    </div>
    <div class="section">
      <h3>確認狀態清單</h3>
      <ul class="status-list">
        <li><span class="dot all"></span> 全部</li>
        <li><span class="dot pay"></span> 待付款</li>
        <li><span class="dot ship"></span> 待出貨</li>
        <li><span class="dot receive"></span> 待收貨</li>
        <li><span class="dot done"></span> 已完成</li>
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ProfileView',
  data () {
    return {
      avatarUrl: 'https://img.icons8.com/color/96/000000/user-male-circle--v2.png'
    }
  },
  methods: {
    triggerFileInput () {
      this.$el.querySelector('#avatarInput').click()
    },
    onFileChange (e) {
      const file = e.target.files[0]
      if (file) {
        // 預覽圖片
        const reader = new FileReader()
        reader.onload = e2 => {
          this.avatarUrl = e2.target.result
        }
        reader.readAsDataURL(file)
        // 實際上傳功能需串接後端，以下為範例註解：
        /*
        const formData = new FormData()
        formData.append('avatar', file)
        axios.post('/api/upload-avatar', formData)
          .then(res => { ... })
        */
      }
    }
  }
}
</script>
.avatar {
  width: 96px;
  height: 96px;
  border-radius: 50%;
  border: 2px solid #e67e22;
  object-fit: cover;
}
.upload-btn {
  margin-top: 0.5rem;
}
.upload-btn button {
  background: #e67e22;
  color: #fff;
  border: none;
  border-radius: 4px;
  padding: 0.3rem 1rem;
  cursor: pointer;
  font-size: 0.95rem;
  transition: background 0.2s;
}
.upload-btn button:hover {
  background: #cf711f;
}

<style scoped>
/* 基本樣式 */
.profile {
  margin: 2rem auto;
  max-width: 600px;
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 2px 8px #eee;
  padding: 2rem;
}
.profile-header {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  margin-bottom: 2rem;
}
.avatar {
  width: 96px;
  height: 96px;
  border-radius: 50%;
  border: 2px solid #e67e22;
}
.profile-header h2 {
  color: #2c3e50;
  margin: 0 0 0.5rem 0;
}
.profile-header p {
  font-size: 1.1rem;
  margin: 0.2rem 0;
}
.section {
  margin-top: 2rem;
}
.section h3 {
  color: #e67e22;
  margin-bottom: 1rem;
  font-size: 1.1rem;
}
.orders {
  list-style: none;
  padding: 0;
}
.orders li {
  background: #fafafa;
  border-radius: 6px;
  margin-bottom: 1rem;
  padding: 1rem;
  box-shadow: 0 1px 3px #eee;
}
.order-title {
  font-weight: bold;
  margin-bottom: 0.3rem;
}
.order-detail {
  font-size: 0.95rem;
  color: #666;
}
.status {
  font-weight: bold;
}
.status.received {
  color: #27ae60;
}
.status.waiting {
  color: #e67e22;
}
.status-list {
  display: flex;
  gap: 1.2rem;
  list-style: none;
  padding: 0;
  margin: 0.5rem 0 0 0;
}
.status-list li {
  display: flex;
  align-items: center;
  font-size: 1rem;
}
.dot {
  display: inline-block;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  margin-right: 0.4rem;
}
.dot.all {
  background: #e67e22;
}
.dot.pay {
  background: #f39c12;
}
.dot.ship {
  background: #3498db;
}
.dot.receive {
  background: #e67e22;
}
.dot.done {
  background: #27ae60;
}
</style>
