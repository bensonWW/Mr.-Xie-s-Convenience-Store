export const ORDER_STATUS = {
    ALL: 'all',
    PENDING_PAYMENT: 'pending_payment',
    PROCESSING: 'processing',
    SHIPPED: 'shipped',
    DELIVERED: 'delivered',
    COMPLETED: 'completed',
    CANCELLED: 'cancelled',
    RETURNED: 'returned',
    REFUNDED: 'refunded'
}

export const ORDER_STATUS_LABELS = {
    [ORDER_STATUS.ALL]: '全部',
    [ORDER_STATUS.PENDING_PAYMENT]: '待付款',
    [ORDER_STATUS.PROCESSING]: '處理中',
    [ORDER_STATUS.SHIPPED]: '已出貨',
    [ORDER_STATUS.DELIVERED]: '已送達',
    [ORDER_STATUS.COMPLETED]: '已完成',
    [ORDER_STATUS.CANCELLED]: '已取消',
    [ORDER_STATUS.RETURNED]: '已退貨',
    [ORDER_STATUS.REFUNDED]: '已退款'
}

export const LOGISTICS_STATUS_LABELS = {
    [ORDER_STATUS.PENDING_PAYMENT]: '待付款',
    [ORDER_STATUS.PROCESSING]: '備貨中',
    [ORDER_STATUS.SHIPPED]: '已出貨',
    [ORDER_STATUS.COMPLETED]: '已送達',
    [ORDER_STATUS.CANCELLED]: '已取消',
    default: '處理中'
}
