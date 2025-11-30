<template>
  <div class="py-8">

    <!-- 未登入：登入 / 註冊區 -->
    <div v-if="!isLoggedIn" class="auth-wrapper">
      <div class="auth-card">
        <div class="auth-header">
          <h2>會員登入 / 註冊</h2>
          <p>登入後即可查看購物紀錄與專屬會員優惠。</p>
        </div>

        <div class="auth-panels">
          <!-- 登入 -->
          <div class="auth-box">
            <h3>已經有帳號</h3>
            <p class="hint">請輸入 Email 與密碼登入。</p>
            <input
              v-model="loginEmail"
              type="email"
              placeholder="Email"
            >
            <input
              v-model="loginPassword"
              type="password"
              placeholder="密碼"
            >
            <button class="primary-btn" @click="handleLogin">登入</button>
          </div>

          <!-- 註冊 -->
          <div class="auth-box">
            <h3>還沒有帳號？</h3>
            <p class="hint">註冊一個簡單的會員帳號吧！</p>
            <input
              v-model="registerName"
              type="text"
              placeholder="姓名"
            >
            <input
              v-model="registerEmail"
              type="email"
              placeholder="Email"
            >
            <input
              v-model="registerPassword"
              type="password"
              placeholder="設定密碼"
            >
            <input
              v-model="registerPasswordConfirm"
              type="password"
              placeholder="再次輸入密碼"
            >
            <button class="secondary-btn" @click="handleRegister">註冊</button>
          </div>
        </div>
      </div>
    </div>

    <!-- 已登入：會員中心 -->
    <div v-else class="container mx-auto px-4 pb-12 grid grid-cols-1 md:grid-cols-4 gap-6">

        <aside class="col-span-1 hidden md:block">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-6 text-center border-b border-gray-100 bg-gradient-to-b from-blue-50 to-white">
                    <div class="w-20 h-20 mx-auto bg-gray-200 rounded-full mb-3 border-4 border-white shadow-md overflow-hidden relative">
                        <img :src="avatarUrl" alt="Avatar" class="w-full h-full object-cover">
                        <button class="absolute bottom-0 right-0 bg-xieOrange text-white rounded-full p-1 text-xs w-6 h-6 flex items-center justify-center cursor-pointer" @click="triggerFileInput">
                          <i class="fas fa-camera"></i>
                        </button>
                        <input
                          type="file"
                          accept="image/*"
                          @change="onFileChange"
                          id="avatarInput"
                          style="display:none"
                        >
                    </div>
                    <h3 class="font-bold text-gray-800">{{ user.name }}</h3>
                    <span class="inline-block bg-xieOrange text-white text-xs px-2 py-0.5 rounded-full mt-1">一般會員</span>
                </div>

                <nav class="p-2">
                    <ul class="space-y-1 text-sm">
                        <li>
                            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg transition"
                               :class="currentView === 'dashboard' ? 'bg-orange-50 text-xieOrange font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-xieOrange'"
                               @click.prevent="currentView = 'dashboard'">
                                <i class="fas fa-user w-5 text-center"></i> 會員首頁
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-xieOrange rounded-lg transition" @click.prevent>
                                <i class="fas fa-file-invoice-dollar w-5 text-center"></i> 我的訂單
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg transition"
                               :class="currentView === 'coupons' ? 'bg-orange-50 text-xieOrange font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-xieOrange'"
                               @click.prevent="currentView = 'coupons'; fetchCoupons()">
                                <i class="fas fa-ticket-alt w-5 text-center"></i> 我的折價券
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg transition"
                               :class="currentView === 'wishlist' ? 'bg-orange-50 text-xieOrange font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-xieOrange'"
                               @click.prevent="currentView = 'wishlist'">
                                <i class="far fa-heart w-5 text-center"></i> 追蹤清單
                            </a>
                        </li>
                         <li>
                            <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-xieOrange rounded-lg transition" @click.prevent>
                                <i class="fas fa-map-marker-alt w-5 text-center"></i> 收貨地址管理
                            </a>
                        </li>
                        <li class="border-t border-gray-100 mt-2 pt-2">
                            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg transition"
                               :class="currentView === 'editProfile' ? 'bg-orange-50 text-xieOrange font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-xieOrange'"
                               @click.prevent="currentView = 'editProfile'">
                                <i class="fas fa-user-cog w-5 text-center"></i> 修改個人資料
                            </a>
                        </li>

                    </ul>
                </nav>
            </div>
        </aside>

        <main class="col-span-1 md:col-span-3 space-y-6">

            <div v-if="currentView === 'dashboard'" class="space-y-6">
                <div class="bg-white rounded-lg shadow-sm p-6 flex flex-col md:flex-row items-center justify-between gap-6 border-l-4 border-xieBlue">
                    <div class="flex items-center gap-4">
                        <div class="text-gray-700">
                            <h2 class="text-xl font-bold">早安，{{ user.name }}！</h2>
                            <p class="text-sm text-gray-500 mt-1">累積消費金額：<span class="text-xieOrange font-bold text-lg">NT$ 0</span></p>
                        </div>
                    </div>
                    <div class="flex gap-8 text-center">
                        <div>
                            <div class="text-2xl font-bold text-xieBlue">0</div>
                            <div class="text-xs text-gray-500">紅利點數</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-xieBlue">{{ coupons.length }}</div>
                            <div class="text-xs text-gray-500">可用折價券</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-xieBlue">{{ orders.length }}</div>
                            <div class="text-xs text-gray-500">本月下單</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">訂單狀態</h3>
                    <div class="grid grid-cols-5 gap-2 text-center">
                        <a href="#" class="group relative py-2" @click.prevent>
                            <div class="text-3xl text-gray-400 group-hover:text-xieOrange mb-2 transition relative inline-block">
                                <i class="fas fa-wallet"></i>
                                <span v-if="countStatus('pending_payment') > 0" class="absolute -top-2 -right-3 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center border-2 border-white">{{ countStatus('pending_payment') }}</span>
                            </div>
                            <div class="text-sm text-gray-600 group-hover:text-xieOrange">待付款</div>
                        </a>

                        <a href="#" class="group relative py-2" @click.prevent>
                            <div class="text-3xl text-gray-400 group-hover:text-xieOrange mb-2 transition relative inline-block">
                                <i class="fas fa-box-open"></i>
                                <span v-if="countStatus('processing') > 0" class="absolute -top-2 -right-3 bg-gray-400 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center border-2 border-white">{{ countStatus('processing') }}</span>
                            </div>
                            <div class="text-sm text-gray-600 group-hover:text-xieOrange">待出貨</div>
                        </a>

                        <a href="#" class="group relative py-2" @click.prevent>
                            <div class="text-3xl text-gray-400 group-hover:text-xieOrange mb-2 transition relative inline-block">
                                <i class="fas fa-truck"></i>
                                <span v-if="countStatus('shipped') > 0" class="absolute -top-2 -right-3 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center border-2 border-white">{{ countStatus('shipped') }}</span>
                            </div>
                            <div class="text-sm text-gray-600 group-hover:text-xieOrange">待收貨</div>
                        </a>

                        <a href="#" class="group relative py-2" @click.prevent>
                            <div class="text-3xl text-gray-400 group-hover:text-xieOrange mb-2 transition">
                                <i class="fas fa-clipboard-check"></i>
                                <span v-if="countStatus('completed') > 0" class="absolute -top-2 -right-3 bg-green-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center border-2 border-white">{{ countStatus('completed') }}</span>
                            </div>
                            <div class="text-sm text-gray-600 group-hover:text-xieOrange">已完成</div>
                        </a>

                        <a href="#" class="group relative py-2" @click.prevent>
                            <div class="text-3xl text-gray-400 group-hover:text-xieOrange mb-2 transition">
                                <i class="fas fa-undo-alt"></i>
                            </div>
                            <div class="text-sm text-gray-600 group-hover:text-xieOrange">退貨/取消</div>
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800">最近購物紀錄</h3>
                        <a href="#" class="text-sm text-xieOrange hover:underline" @click.prevent>查看所有訂單 &rarr;</a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left" v-if="orders.length > 0">
                            <thead class="bg-gray-50 text-gray-600">
                                <tr>
                                    <th class="px-6 py-3">訂單編號</th>
                                    <th class="px-6 py-3">下單日期</th>
                                    <th class="px-6 py-3">總金額</th>
                                    <th class="px-6 py-3">狀態</th>
                                    <th class="px-6 py-3 text-right">操作</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="order in orders" :key="order.id" class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-bold text-xieBlue">#{{ order.id }}</td>
                                    <td class="px-6 py-4 text-gray-500">{{ formatDate(order.created_at) }}</td>
                                    <td class="px-6 py-4 font-bold">NT$ {{ order.total_amount }}</td>
                                    <td class="px-6 py-4">
                                        <span class="py-1 px-3 rounded-full text-xs font-bold"
                                            :class="{
                                                'bg-red-100 text-red-600': order.status === 'pending_payment',
                                                'bg-blue-100 text-blue-600': order.status === 'shipped',
                                                'bg-green-100 text-green-600': order.status === 'completed',
                                                'bg-gray-100 text-gray-600': order.status === 'processing'
                                            }">
                                            {{ getStatusText(order.status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button v-if="order.status === 'pending_payment'" class="bg-xieOrange text-white px-3 py-1 rounded text-xs hover:bg-orange-600 transition mr-2" @click="openOrderDetails(order.id)">去付款</button>
                                        <button class="text-gray-400 hover:text-gray-600 text-xs" @click="openOrderDetails(order.id)">詳情</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-else class="p-8 text-center text-gray-400">
                            尚無訂單紀錄
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <h3 class="font-bold text-gray-800 mb-4">猜你喜歡</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-white rounded-lg shadow-sm p-3 hover:shadow-md transition cursor-pointer">
                            <div class="bg-gray-100 h-24 rounded mb-2 flex items-center justify-center text-gray-300"><i class="fas fa-image"></i></div>
                            <div class="text-xs text-gray-800 font-bold line-clamp-1">Apple Watch S9 GPS</div>
                            <div class="text-xieOrange font-bold text-sm mt-1">NT$ 13,500</div>
                        </div>
                        <div class="bg-white rounded-lg shadow-sm p-3 hover:shadow-md transition cursor-pointer">
                            <div class="bg-gray-100 h-24 rounded mb-2 flex items-center justify-center text-gray-300"><i class="fas fa-image"></i></div>
                            <div class="text-xs text-gray-800 font-bold line-clamp-1">AirPods Pro 2 USB-C</div>
                            <div class="text-xieOrange font-bold text-sm mt-1">NT$ 7,490</div>
                        </div>
                        <div class="bg-white rounded-lg shadow-sm p-3 hover:shadow-md transition cursor-pointer">
                            <div class="bg-gray-100 h-24 rounded mb-2 flex items-center justify-center text-gray-300"><i class="fas fa-image"></i></div>
                            <div class="text-xs text-gray-800 font-bold line-clamp-1">Switch OLED 主機</div>
                            <div class="text-xieOrange font-bold text-sm mt-1">NT$ 9,980</div>
                        </div>
                        <div class="bg-white rounded-lg shadow-sm p-3 hover:shadow-md transition cursor-pointer">
                            <div class="bg-gray-100 h-24 rounded mb-2 flex items-center justify-center text-gray-300"><i class="fas fa-image"></i></div>
                            <div class="text-xs text-gray-800 font-bold line-clamp-1">Sony WH-1000XM5</div>
                            <div class="text-xieOrange font-bold text-sm mt-1">NT$ 9,900</div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="currentView === 'coupons'" class="space-y-6">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">新增優惠券 / 儲值折扣碼</label>
                    <div class="flex gap-3">
                        <input type="text" placeholder="請輸入折扣代碼 (例如: XIE2025)" class="flex-1 border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-xieOrange focus:ring-1 focus:ring-xieOrange">
                        <button class="bg-xieBlue text-white px-6 py-2 rounded font-bold hover:bg-gray-700 transition">領取</button>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm min-h-[500px] overflow-hidden">

                    <div class="flex border-b border-gray-200">
                        <button class="flex-1 py-4 text-center font-bold transition focus:outline-none border-0 border-b-2"
                                :class="couponTab === 'available' ? 'text-xieOrange bg-orange-50 border-xieOrange' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50 border-transparent'"
                                @click="couponTab = 'available'">
                            可使用 ({{ coupons.length }})
                        </button>
                        <button class="flex-1 py-4 text-center font-bold transition focus:outline-none border-0 border-b-2"
                                :class="couponTab === 'used' ? 'text-xieOrange bg-orange-50 border-xieOrange' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50 border-transparent'"
                                @click="couponTab = 'used'">
                            已使用 (0)
                        </button>
                        <button class="flex-1 py-4 text-center font-bold transition focus:outline-none border-0 border-b-2"
                                :class="couponTab === 'expired' ? 'text-xieOrange bg-orange-50 border-xieOrange' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50 border-transparent'"
                                @click="couponTab = 'expired'">
                            已失效 (0)
                        </button>
                    </div>

                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4" v-if="couponTab === 'available'">

                        <div v-for="coupon in coupons" :key="coupon.id" class="bg-white border border-gray-200 rounded-lg flex overflow-hidden hover:shadow-md transition group">
                            <div class="w-32 bg-gradient-to-br from-orange-400 to-xieOrange text-white flex flex-col items-center justify-center p-2 relative border-r-2 border-dashed border-white">
                                <span class="text-xs font-bold opacity-80" v-if="coupon.limit_price">滿${{ coupon.limit_price }}</span>
                                <div class="font-bold text-2xl">
                                    {{ coupon.type === 'fixed' ? '折$' + coupon.discount_amount : (100 - coupon.discount_amount) + '折' }}
                                </div>
                                <div class="absolute -right-3 top-1/2 -translate-y-1/2 w-6 h-6 bg-white rounded-full"></div> <div class="absolute -left-3 top-1/2 -translate-y-1/2 w-6 h-6 bg-white rounded-full"></div>
                            </div>
                            <div class="flex-1 p-4 flex flex-col justify-between">
                                <div>
                                    <h4 class="font-bold text-gray-800 group-hover:text-xieOrange">{{ coupon.code }}</h4>
                                    <p class="text-xs text-gray-500 mt-1">全館商品適用</p>
                                </div>
                                <div class="flex justify-between items-end mt-3">
                                    <span class="text-xs text-gray-400">有效期限: 2025/12/31</span>
                                    <button class="text-xieOrange border border-xieOrange px-3 py-1 rounded text-xs hover:bg-xieOrange hover:text-white transition" @click="copyCode(coupon.code)">複製代碼</button>
                                </div>
                            </div>
                        </div>

                        <div v-if="coupons.length === 0" class="col-span-2 text-center py-8 text-gray-400">
                            暫無可用的優惠券
                        </div>

                    </div>

                    <div class="p-6 text-center text-gray-400" v-else>
                        暫無資料
                    </div>

                    <div class="px-6 pb-6 text-xs text-gray-400 mt-auto">
                        * 優惠券使用規則請參閱 <a href="#" class="underline hover:text-xieOrange">服務條款</a>。
                    </div>
                </div>
            </div>

            <div v-if="currentView === 'editProfile'">
                <form class="bg-white rounded-lg shadow-sm overflow-hidden" @submit.prevent="updateProfile">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                        <h2 class="font-bold text-gray-800">基本資料設定</h2>
                    </div>

                    <div class="p-8">
                        <div class="flex items-center gap-6 mb-8">
                            <div class="relative w-24 h-24">
                                <img :src="avatarUrl || 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png'" class="w-full h-full rounded-full object-cover border-4 border-gray-100 shadow">
                                <button type="button" class="absolute bottom-0 right-0 bg-xieOrange text-white rounded-full p-2 border-2 border-white hover:bg-orange-600 transition shadow-sm" @click="triggerFileInput">
                                    <i class="fas fa-camera"></i>
                                </button>
                                <input type="file" id="avatarInput" class="hidden" accept="image/*" @change="onFileChange">
                            </div>
                            <div>
                                <div class="text-sm font-bold text-gray-700 mb-1">個人頭像</div>
                                <div class="text-xs text-gray-500 mb-2">支援 JPG, PNG 格式，檔案大小不超過 2MB</div>
                                <button type="button" class="text-sm border border-gray-300 px-3 py-1 rounded hover:bg-gray-50 transition" @click="triggerFileInput">選擇圖片</button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">會員帳號 / Email</label>
                                <input type="text" :value="user.email" disabled class="w-full bg-gray-100 border border-gray-300 rounded px-4 py-2 text-gray-500 cursor-not-allowed">
                                <p class="text-xs text-gray-400 mt-1">帳號設定後無法修改</p>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">手機號碼</label>
                                <input type="tel" v-model="editForm.phone" class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-xieOrange focus:ring-1 focus:ring-xieOrange">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">顯示名稱</label>
                                <input type="text" v-model="editForm.name" class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-xieOrange focus:ring-1 focus:ring-xieOrange">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">生日</label>
                                <div class="relative">
                                    <input type="date" v-model="editForm.birthday" class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-xieOrange focus:ring-1 focus:ring-xieOrange">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">性別</label>
                                <div class="flex gap-4 mt-2">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="gender" value="male" v-model="editForm.gender" class="text-xieOrange focus:ring-xieOrange">
                                        <span class="ml-2 text-sm">男</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="gender" value="female" v-model="editForm.gender" class="text-xieOrange focus:ring-xieOrange">
                                        <span class="ml-2 text-sm">女</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="gender" value="other" v-model="editForm.gender" class="text-xieOrange focus:ring-xieOrange">
                                        <span class="ml-2 text-sm">不透露</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t border-b border-gray-100 bg-gray-50 mt-4">
                        <h2 class="font-bold text-gray-800">變更密碼</h2>
                    </div>

                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                             <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">目前密碼</label>
                                <input type="password" v-model="passwordForm.currentPassword" placeholder="若不修改密碼請留空" class="w-full md:w-1/2 border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-xieOrange focus:ring-1 focus:ring-xieOrange">
                            </div>
                             <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">新密碼</label>
                                <input type="password" v-model="passwordForm.newPassword" class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-xieOrange focus:ring-1 focus:ring-xieOrange">
                            </div>
                             <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">確認新密碼</label>
                                <input type="password" v-model="passwordForm.confirmPassword" class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-xieOrange focus:ring-1 focus:ring-xieOrange">
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-8 py-4 text-right border-t border-gray-200">
                        <button type="button" class="text-gray-500 px-6 py-2 rounded hover:bg-gray-200 transition mr-2" @click="currentView = 'dashboard'">取消</button>
                        <button type="submit" class="bg-xieOrange text-white px-8 py-2 rounded font-bold hover:bg-orange-600 transition shadow-md">儲存變更</button>
                    </div>

                </form>
            </div>

            <div v-if="currentView === 'wishlist'">
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center">
                            我的收藏 <span class="bg-gray-200 text-gray-600 text-xs px-2 py-1 rounded-full ml-2">{{ wishlist.length }}</span>
                        </h2>
                        <button class="text-sm text-red-500 hover:underline" @click="clearInvalidWishlistItems"><i class="fas fa-trash-alt mr-1"></i> 清空失效商品</button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                        <div v-for="item in wishlist" :key="item.id"
                             class="border border-gray-200 rounded-lg p-4 relative hover:shadow-lg transition group"
                             :class="item.status === 'available' ? 'bg-white' : 'bg-gray-50 opacity-90'">

                            <button class="absolute top-3 right-3 text-gray-300 hover:text-red-500 z-10 p-1" @click="removeFromWishlist(item.id)">
                                <i class="fas" :class="item.status === 'available' ? 'fa-times text-lg' : 'fa-trash-alt'"></i>
                            </button>

                            <a href="#" class="block relative mb-3">
                                <div class="w-full h-40 bg-gray-100 rounded flex items-center justify-center text-gray-300 overflow-hidden relative">
                                    <i :class="[item.icon_class + ' text-5xl', {'opacity-50': item.status !== 'available'}]"></i>
                                    <div v-if="item.status !== 'available'" class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center">
                                        <span class="bg-gray-800 text-white px-3 py-1 text-sm rounded">補貨中</span>
                                    </div>
                                </div>
                                <div v-if="item.tag" class="absolute bottom-0 left-0 bg-red-500 text-white text-xs px-2 py-1 rounded-tr">
                                    <i class="fas fa-arrow-down mr-1"></i>{{ item.tag }}
                                </div>
                            </a>

                            <h3 class="font-bold text-sm line-clamp-2 h-10 mb-2" :class="item.status === 'available' ? 'text-gray-800 group-hover:text-xieOrange' : 'text-gray-500'">
                                <a href="#">{{ item.name }}</a>
                            </h3>
                            <div class="flex items-end gap-2 mb-4">
                                <span class="text-xl font-bold" :class="item.status === 'available' ? 'text-xieOrange' : 'text-gray-500'">NT$ {{ item.price.toLocaleString() }}</span>
                                <span v-if="item.original_price" class="text-xs text-gray-400 line-through mb-1">NT$ {{ item.original_price.toLocaleString() }}</span>
                            </div>

                            <button v-if="item.status === 'available'" class="w-full bg-xieOrange text-white py-2 rounded font-bold hover:bg-orange-600 transition flex items-center justify-center gap-2" @click="addToCartFromWishlist(item)">
                                <i class="fas fa-cart-plus"></i> 加入購物車
                            </button>
                            <button v-else class="w-full border border-gray-400 text-gray-600 py-2 rounded font-bold hover:bg-gray-200 transition flex items-center justify-center gap-2">
                                <i class="far fa-bell"></i> 到貨通知
                            </button>
                        </div>

                    </div>

                    <div class="mt-8 flex justify-center space-x-2">
                        <a href="#" class="px-3 py-2 bg-white border border-gray-300 text-gray-500 rounded hover:bg-gray-100"><i class="fas fa-chevron-left"></i></a>
                        <a href="#" class="px-3 py-2 bg-xieOrange border border-xieOrange text-white rounded font-bold">1</a>
                        <a href="#" class="px-3 py-2 bg-white border border-gray-300 text-gray-500 rounded hover:bg-gray-100"><i class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- Order Details Modal -->
    <div v-if="showOrderDetails && selectedOrder" class="modal-overlay" @click.self="showOrderDetails = false">
      <div class="modal-content">
        <h3>訂單詳情 #{{ selectedOrder.id }}</h3>
        <div class="order-meta">
          <p><strong>下單日期：</strong> {{ new Date(selectedOrder.created_at).toLocaleString() }}</p>
          <p><strong>訂單狀態：</strong> {{ getStatusText(selectedOrder.status) }}</p>
          <p><strong>物流狀態：</strong> <span class="highlight">{{ getLogisticsStatus(selectedOrder.status) }}</span></p>
          <p><strong>總金額：</strong> ${{ selectedOrder.total_amount }}</p>
        </div>

        <h4>商品列表</h4>
        <ul class="order-items-list">
          <li v-for="item in selectedOrder.items" :key="item.id" class="order-item-row">
            <span>{{ item.product.name }}</span>
            <span>x{{ item.quantity }}</span>
            <span>${{ item.price * item.quantity }}</span>
          </li>
        </ul>

        <div class="modal-actions">
          <button class="outline-btn" @click="showOrderDetails = false">關閉</button>
          <button v-if="selectedOrder.status === 'pending_payment'" class="primary-btn" @click="payOrder">立即付款</button>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import api from '../services/api'

export default {
  name: 'ProfileView',
  data () {
    return {
      avatarUrl: 'https://img.icons8.com/color/96/000000/user-male-circle--v2.png',
      // 登入
      loginEmail: '',
      loginPassword: '',
      // 註冊
      registerName: '',
      registerEmail: '',
      registerPassword: '',
      registerPasswordConfirm: '',
      user: null,
      orders: [],
      // Modals
      showOrderDetails: false,
      selectedOrder: null,
      // Edit Profile Form
      editForm: {
        name: '',
        phone: '',
        address: '',
        birthday: '',
        gender: ''
      },
      passwordForm: {
        currentPassword: '',
        newPassword: '',
        confirmPassword: ''
      },
      // Coupons
      coupons: [],
      // Wishlist
      wishlist: [
        {
          id: 1,
          name: 'Apple iPhone 15 Pro Max (256G) - 原色鈦金屬',
          price: 44900,
          original_price: 48900,
          image: 'https://cdn-icons-png.flaticon.com/512/644/644458.png', // Placeholder icon
          icon_class: 'fas fa-mobile-alt',
          status: 'available'
        },
        {
          id: 2,
          name: 'Sony WH-1000XM5 無線降噪耳機',
          price: 9900,
          original_price: 11900,
          image: 'https://cdn-icons-png.flaticon.com/512/2304/2304226.png', // Placeholder icon
          icon_class: 'fas fa-headphones',
          status: 'available',
          tag: '比加入時降 $500'
        },
        {
          id: 3,
          name: 'Switch OLED 主機 (白色) - 台灣公司貨',
          price: 9980,
          original_price: null,
          image: 'https://cdn-icons-png.flaticon.com/512/686/686589.png', // Placeholder icon
          icon_class: 'fas fa-gamepad',
          status: 'out_of_stock'
        },
        {
          id: 4,
          name: 'Philips 飛利浦 高速破壁機',
          price: 5680,
          original_price: null,
          image: 'https://cdn-icons-png.flaticon.com/512/911/911409.png', // Placeholder icon
          icon_class: 'fas fa-blender',
          status: 'available'
        }
      ],
      currentView: 'dashboard',
      couponTab: 'available'
    }
  },
  computed: {
    isLoggedIn () {
      return !!this.user
    }
  },
  watch: {
    currentView (newVal) {
      if (newVal === 'editProfile' && this.user) {
        this.editForm = {
          name: this.user.name,
          phone: this.user.phone || '',
          address: this.user.address || '',
          birthday: this.user.birthday ? this.user.birthday.split('T')[0] : '',
          gender: this.user.gender || 'male'
        }
      }
    }
  },
  created () {
    this.fetchUser()
    this.fetchOrders()
  },
  methods: {
    async fetchUser () {
      const token = localStorage.getItem('token')
      if (token) {
        try {
          const response = await api.get('/user')
          this.user = response.data
          this.fetchOrders()
        } catch (error) {
          console.error('Error fetching user:', error)
          localStorage.removeItem('token')
          this.user = null
        }
      }
    },
    async fetchOrders () {
      if (!this.user) return
      try {
        const response = await api.get('/orders')
        this.orders = response.data
      } catch (error) {
        console.error('Error fetching orders:', error)
      }
    },
    async handleLogin () {
      try {
        const response = await api.post('/login', {
          email: this.loginEmail,
          password: this.loginPassword
        })
        const { access_token: accessToken, user } = response.data
        localStorage.setItem('token', accessToken)
        localStorage.setItem('user_role', user.role)
        this.user = user
        this.loginPassword = ''
        this.fetchOrders()
        alert('登入成功！')
        window.location.reload()
      } catch (error) {
        console.error('Login error:', error)
        alert('登入失敗，請檢查帳號密碼。')
      }
    },
    async handleRegister () {
      if (!this.registerName || !this.registerEmail || !this.registerPassword || !this.registerPasswordConfirm) {
        alert('請把註冊資料填寫完整。')
        return
      }
      if (this.registerPassword !== this.registerPasswordConfirm) {
        alert('兩次輸入的密碼不一致。')
        return
      }

      try {
        const response = await api.post('/register', {
          name: this.registerName,
          email: this.registerEmail,
          password: this.registerPassword,
          password_confirmation: this.registerPasswordConfirm
        })
        const { access_token: accessToken, user } = response.data
        localStorage.setItem('token', accessToken)
        this.user = user

        // Clear form
        this.registerName = ''
        this.registerEmail = ''
        this.registerPassword = ''
        this.registerPasswordConfirm = ''
        this.fetchOrders()

        alert('註冊成功！')
      } catch (error) {
        console.error('Register error:', error)
        if (error.response && error.response.data) {
          const msg = error.response.data.message || JSON.stringify(error.response.data)
          alert('註冊失敗：' + msg)
        } else {
          alert('註冊失敗，請稍後再試。')
        }
      }
    },
    async logout () {
      try {
        await api.post('/logout')
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        localStorage.removeItem('token')
        localStorage.removeItem('user_role')
        this.user = null
        this.orders = []
        alert('已登出')
        window.location.reload()
      }
    },
    triggerFileInput () {
      this.$el.querySelector('#avatarInput').click()
    },
    onFileChange (e) {
      const file = e.target.files[0]
      if (file) {
        const reader = new FileReader()
        reader.onload = (e2) => {
          this.avatarUrl = e2.target.result
        }
        reader.readAsDataURL(file)
      }
    },
    getStatusClass (status) {
      const map = {
        pending_payment: 'waiting',
        processing: 'waiting',
        shipped: 'waiting',
        delivered: 'received',
        completed: 'received',
        cancelled: 'waiting', // or error color
        returned: 'waiting'
      }
      return map[status] || ''
    },
    getStatusText (status) {
      const map = {
        pending_payment: '待付款',
        processing: '處理中',
        shipped: '已出貨',
        delivered: '已送達',
        completed: '已完成',
        cancelled: '已取消',
        returned: '已退貨'
      }
      return map[status] || status
    },
    formatDate (dateString) {
      return new Date(dateString).toLocaleDateString()
    },
    countStatus (status) {
      return this.orders.filter(o => o.status === status).length
    },
    async updateProfile () {
      try {
        const payload = { ...this.editForm }

        if (this.passwordForm.newPassword) {
          if (this.passwordForm.newPassword !== this.passwordForm.confirmPassword) {
            alert('新密碼與確認密碼不符')
            return
          }
          // Assuming backend accepts these fields for password change
          payload.password = this.passwordForm.newPassword
          payload.current_password = this.passwordForm.currentPassword
        }

        const response = await api.put('/profile', payload)
        this.user = response.data.user
        alert('個人資料更新成功！')

        // Clear password form
        this.passwordForm.currentPassword = ''
        this.passwordForm.newPassword = ''
        this.passwordForm.confirmPassword = ''
      } catch (error) {
        console.error('Update profile error:', error)
        alert('更新失敗，請稍後再試。')
      }
    },
    async fetchCoupons () {
      try {
        const response = await api.get('/coupons')
        this.coupons = response.data
      } catch (error) {
        console.error('Fetch coupons error:', error)
      }
    },
    copyCode (code) {
      navigator.clipboard.writeText(code).then(() => {
        alert('優惠碼已複製：' + code)
      })
    },
    async openOrderDetails (orderId) {
      try {
        const response = await api.get(`/orders/${orderId}`)
        this.selectedOrder = response.data
        this.showOrderDetails = true
      } catch (error) {
        console.error('Fetch order details error:', error)
        alert('無法取得訂單詳情')
      }
    },
    getLogisticsStatus (status) {
      const map = {
        pending_payment: '待付款',
        processing: '備貨中',
        shipped: '已出貨',
        completed: '已送達',
        cancelled: '已取消'
      }
      return map[status] || status
    },
    async payOrder () {
      if (!this.selectedOrder) return
      try {
        await api.post(`/orders/${this.selectedOrder.id}/pay`)
        alert('付款成功！')
        this.showOrderDetails = false
        this.fetchOrders() // Refresh list
      } catch (error) {
        console.error('Payment error:', error)
        alert('付款失敗，請稍後再試。')
      }
    },
    removeFromWishlist (id) {
      if (confirm('確定要將此商品移出追蹤清單嗎？')) {
        this.wishlist = this.wishlist.filter(item => item.id !== id)
        alert('已移除商品')
      }
    },
    addToCartFromWishlist (item) {
      alert(`已將 ${item.name} 加入購物車！`)
      // In a real app, you would call an API or store action here
    },
    clearInvalidWishlistItems () {
      if (confirm('確定要清空所有失效商品嗎？')) {
        this.wishlist = this.wishlist.filter(item => item.status !== 'out_of_stock')
        alert('已清空失效商品')
      }
    }
  }
}
</script>

<style scoped>
/* ========== 全域修正 (解決 Tailwind Preflight 關閉後的排版問題) ========== */
*, *::before, *::after {
  box-sizing: border-box; /* 關鍵：確保 padding 不會撐開寬度 */
}

/* 強制統一所有輸入框的高度與邊框，避免 date 與 text 長得不一樣 */
input[type="text"],
input[type="email"],
input[type="password"],
input[type="tel"],
input[type="date"],
select {
  height: 42px; /* 固定高度，確保對齊 */
  line-height: 1.5;
  border-width: 1px; /* 確保邊框出現 */
  border-style: solid;
  appearance: none; /* 移除 iOS/瀏覽器預設圓角與陰影 */
  -webkit-appearance: none;
}

/* 修正 Date Picker 在某些瀏覽器的 icon 位置 */
input[type="date"] {
  padding-right: 10px;
  display: block;
  width: 100%;
}

/* ========== 以下維持您原本的樣式 ========== */

.auth-wrapper {
  display: flex;
  justify-content: center;
  margin-top: 2rem;
}

.auth-card {
  width: 100%;
  max-width: 780px;
  padding: 2rem 2.5rem;
  border-radius: 16px;
  background: #ffffff;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
  text-align: left;
}

.auth-header h2 {
  margin: 0 0 0.5rem;
}

.auth-header p {
  margin: 0;
  color: #888;
  font-size: 0.95rem;
}

.auth-panels {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
  margin-top: 1.8rem;
}

.auth-box {
  flex: 1 1 260px;
  padding: 1rem 1.3rem;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.7);
  border: 1px solid #eee;
}

.auth-box h3 {
  margin: 0 0 0.3rem;
}

.auth-box .hint {
  margin: 0 0 0.8rem;
  font-size: 0.9rem;
  color: #999;
}

/* 登入註冊頁面的輸入框樣式 */
.auth-box input {
  width: 100%;
  padding: 0.45rem 0.6rem;
  margin-bottom: 0.6rem;
  border-radius: 6px;
  border-color: #ccc;
  font-size: 0.95rem;
}

.primary-btn,
.secondary-btn {
  display: inline-block;
  padding: 0.45rem 1.4rem;
  border-radius: 999px;
  border: none;
  cursor: pointer;
  font-size: 0.95rem;
  font-weight: 600;
  color: #fff;
  transition: opacity 0.2s;
}

.primary-btn:hover, .secondary-btn:hover {
  opacity: 0.9;
}

.primary-btn {
  background: #3498db;
}

.secondary-btn {
  background: #e67e22;
}

/* 解決 RWD 手機版 Table 破版問題 (選用) */
@media (max-width: 768px) {
  .overflow-x-auto {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }
}
</style>
