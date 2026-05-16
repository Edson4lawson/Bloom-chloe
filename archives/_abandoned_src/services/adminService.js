import api from './api'

export const adminService = {
  // Récupérer les statistiques générales du dashboard
  async getStats() {
    const response = await api.get('/admin/analytics/summary.php')
    return {
      success: true,
      stats: {
        totalProducts: response.data.total_products || 0,
        totalOrders: response.data.orders_count || 0,
        totalClients: response.data.customers_count || 0,
        totalRevenue: response.data.revenue_total || 0,
        recentOrders: response.data.recent_orders || [],
        monthlySales: response.data.monthly_sales || []
      }
    }
  },

  // Récupérer tous les produits
  async getProducts() {
    const response = await api.get('/products/get_all.php')
    return {
      success: true,
      products: response.data.products || []
    }
  },

  // Récupérer toutes les catégories
  async getCategories() {
    const response = await api.get('/categories/get_all.php')
    return {
      success: true,
      categories: response.data.categories || []
    }
  },

  // Récupérer toutes les commandes
  async getOrders() {
    const response = await api.get('/orders/get.php')
    return {
      success: true,
      orders: response.data.orders || []
    }
  },

  // Récupérer tous les clients
  async getCustomers() {
    const response = await api.get('/users/get_all.php')
    return {
      success: true,
      customers: response.data.customers || []
    }
  }
}

export default adminService;
