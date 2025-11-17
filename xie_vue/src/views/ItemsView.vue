<template>
  <div class="items">
    <h2>熱銷排行榜</h2>
    <div class="item-list">
      <div class="item-card" v-for="(item) in items" :key="item.name">
        <img class="item-img" :src="item.img" :alt="item.name" />
        <div class="item-name">{{ item.name }}</div>
        <div class="item-price">${{ item.price.toLocaleString() }}</div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ItemsView',
  data () {
    return {
      items: [
        {
          name: 'Apple蘋果 iPhone 17 Pro Max',
          price: 51900,
          img: 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-pro-model-unselect-gallery-1-202309?wid=512&hei=512&fmt=jpeg&qlt=95&.v=1692912410963'
        },
        {
          name: 'BOXMAN 超輕柔抽取式衛生紙',
          price: 799,
          img: 'https://img.pchome.com.tw/cs/items/DSAJ1GA9000A000/000001_1638932437.jpg'
        },
        {
          name: 'SONY索尼 Xperia 1 VII (12G/256G)',
          price: 38911,
          img: 'https://www.sony.com.tw/image/2b1e2b1e2b1e2b1e2b1e2b1e2b1e2b1e?fmt=jpeg&wid=512'
        },
        {
          name: 'SK-II 青春露330ml-專櫃公司貨',
          price: 4499,
          img: 'https://img.pchome.com.tw/cs/items/DEAJ1GA9000A000/000001_1638932437.jpg'
        },
        {
          name: 'Xiaomi小米 14 Ultra 經典版',
          price: 35999,
          img: 'https://i01.appmifile.com/webfile/globalimg/products/pc/xiaomi-14-ultra/specs-header.png'
        }
      ]
    }
  }
}
</script>

<style scoped>
/* 熱銷排行榜樣式 */
.items {
  margin-top: 2rem;
  text-align: center;
}
.items h2 {
  color: #2c3e50;
  margin-bottom: 1.5rem;
  font-size: 1.6rem;
}
.item-list {
  display: flex;
  gap: 2rem;
  justify-content: flex-start;
  align-items: flex-end;
  overflow-x: auto;
  padding: 1rem 0.5rem;
}
.item-card {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 8px #eee;
  padding: 1rem 1.2rem 1.2rem 1.2rem;
  min-width: 160px;
  max-width: 180px;
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
}
.item-img {
  width: 90px;
  height: 90px;
  object-fit: contain;
  margin-bottom: 0.7rem;
  z-index: 1;
}
.item-name {
  font-size: 1rem;
  color: #333;
  margin-bottom: 0.3rem;
  min-height: 2.5em;
  z-index: 1;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 140px;
}
.item-price {
  color: #e53935;
  font-size: 1.2rem;
  font-weight: bold;
  z-index: 1;
}
</style>
