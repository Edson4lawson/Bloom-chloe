<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-purple-50/30 to-slate-50 pt-32 pb-20">
    <div class="container mx-auto px-4 md:px-6 max-w-7xl">
      
      <!-- Top Action: Back to Home -->
      <div class="flex justify-end mb-8">
        <router-link to="/" class="group flex items-center gap-2 px-6 py-3 bg-white text-purple-600 font-bold uppercase tracking-widest text-xs rounded-2xl hover:bg-purple-600 hover:text-white transition-all duration-300 shadow-sm border border-purple-100 hover:border-purple-600 hover:shadow-lg hover:shadow-purple-200/50">
          <Icon icon="solar:home-bold-duotone" class="w-5 h-5 transition-transform group-hover:-translate-x-1" />
          Retour à l'accueil
        </router-link>
      </div>

      <div class="flex flex-col lg:flex-row gap-8">
        
        <!-- Sidebar Navigation -->
        <aside class="w-full lg:w-80 shrink-0" :class="sidebarVisible ? 'animate-slide-in-left' : ''">
          <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 p-8 border border-slate-100 sticky top-28">
            <!-- User Profile Card -->
            <div class="flex items-center gap-4 mb-8">
              <div class="relative group">
                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-purple-600 to-indigo-600 flex items-center justify-center text-white text-2xl font-black uppercase ring-4 ring-purple-50 transition-transform group-hover:scale-110 duration-300">
                  {{ user?.first_name?.charAt(0) || 'U' }}
                </div>
                <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 rounded-full border-2 border-white"></div>
              </div>
              <div>
                <h2 class="text-xl font-black text-slate-900 truncate">{{ user?.first_name }} {{ user?.last_name }}</h2>
                <div class="flex items-center gap-2 mt-1">
                  <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest"
                    :class="user?.role === 'admin' ? 'bg-amber-100 text-amber-700' : 'bg-purple-100 text-purple-700'">
                    <Icon :icon="user?.role === 'admin' ? 'solar:shield-star-bold' : 'solar:crown-bold'" class="w-3 h-3" />
                    {{ user?.role === 'admin' ? 'Admin' : 'Client VIP' }}
                  </span>
                  <span class="text-xs text-slate-400">•</span>
                  <span class="text-xs font-bold text-slate-400">{{ loyaltyTier }}</span>
                </div>
              </div>
            </div>

            <!-- Loyalty Mini Card -->
            <div class="mb-8 p-4 rounded-2xl bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-100/80">
              <div class="flex items-center justify-between text-xs font-black uppercase tracking-wider mb-2">
                <span class="text-purple-900 flex items-center gap-1.5">
                  <Icon icon="solar:star-bold-duotone" class="w-4 h-4 text-purple-600" />
                  Points Fidélité
                </span>
                <span class="text-purple-600 font-black">{{ loyaltyPoints }} pts</span>
              </div>
              <div class="w-full h-2 bg-purple-200/50 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-purple-600 to-indigo-600 rounded-full transition-all duration-1000 ease-out" :style="{ width: loyaltyProgress + '%' }"></div>
              </div>
              <p class="text-[9px] text-purple-400 mt-1.5 font-bold">{{ loyaltyNextTier }}</p>
            </div>

            <nav class="space-y-2">
              <button 
                v-for="(item, index) in menuItems" 
                :key="item.id"
                @click="activeTab = item.id"
                class="w-full flex items-center justify-between px-6 py-4 rounded-2xl transition-all duration-300 font-black uppercase tracking-widest text-[10px]"
                :class="activeTab === item.id ? 'bg-slate-900 text-white shadow-lg shadow-slate-900/20 scale-[1.02]' : 'text-slate-400 hover:bg-slate-50 hover:text-slate-600'"
                :style="{ animationDelay: (index * 50) + 'ms' }">
                <div class="flex items-center gap-3">
                  <Icon :icon="item.icon" class="w-5 h-5" />
                  {{ item.label }}
                </div>
                <div class="flex items-center gap-2">
                  <span v-if="item.badge" class="px-2 py-0.5 rounded-full text-[8px] font-black" 
                    :class="activeTab === item.id ? 'bg-white/20 text-white' : 'bg-purple-100 text-purple-600'">
                    {{ item.badge }}
                  </span>
                  <Icon v-if="activeTab === item.id" icon="solar:arrow-right-linear" class="w-4 h-4" />
                </div>
              </button>

              <div class="pt-6 mt-6 border-t border-slate-100">
                <button @click="handleLogout" class="w-full flex items-center gap-3 px-6 py-4 rounded-2xl text-rose-500 font-black uppercase tracking-widest text-[10px] hover:bg-rose-50 transition-all duration-300 group">
                  <Icon icon="solar:logout-linear" class="w-5 h-5 transition-transform group-hover:-translate-x-1" />
                  Déconnexion
                </button>
              </div>
            </nav>
          </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 min-w-0">
          <transition name="tab-fade" mode="out-in">
            <div :key="activeTab" class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 p-8 md:p-12 border border-slate-100 min-h-[600px]">
            
              <!-- ===== Dashboard Overview ===== -->
              <div v-if="activeTab === 'overview'" class="space-y-10">
                <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
                  <div>
                    <h1 class="text-3xl font-black text-slate-900 italic uppercase">Tableau de <span class="text-purple-600">bord</span></h1>
                    <p class="text-slate-400 font-bold mt-1">Bienvenue, {{ user?.first_name }} ! 👋</p>
                  </div>
                  <div class="text-right">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Membre depuis</p>
                    <p class="text-sm font-bold text-slate-600">{{ memberSince }}</p>
                  </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                  <div v-for="(stat, i) in statsCards" :key="stat.label" 
                    class="relative overflow-hidden p-8 rounded-[2rem] border transition-all duration-500 hover:shadow-xl hover:-translate-y-1 cursor-default group"
                    :class="stat.bgClass"
                    :style="{ animationDelay: (i * 100) + 'ms' }">
                    <div class="absolute top-4 right-4 opacity-10 group-hover:opacity-20 transition-opacity">
                      <Icon :icon="stat.icon" class="w-20 h-20" />
                    </div>
                    <Icon :icon="stat.icon" class="w-10 h-10 mb-4 transition-transform group-hover:scale-110 duration-300" :class="stat.iconColor" />
                    <p class="text-sm font-black uppercase tracking-widest opacity-60" :class="stat.textColor">{{ stat.label }}</p>
                    <p class="text-3xl font-black mt-1" :class="stat.textColor">
                      <span ref="counterRefs">{{ stat.value }}</span>
                      <span v-if="stat.suffix" class="text-xs ml-1">{{ stat.suffix }}</span>
                    </p>
                  </div>
                </div>

                <!-- Recent Activity -->
                <div>
                  <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight">Activité Récente</h3>
                    <button v-if="orders.length > 0" @click="activeTab = 'orders'" class="text-[10px] font-black text-purple-600 uppercase tracking-widest hover:text-purple-800 transition-colors">
                      Voir tout →
                    </button>
                  </div>

                  <!-- Loading Skeleton -->
                  <div v-if="loadingOrders" class="space-y-4">
                    <div v-for="n in 3" :key="n" class="flex items-center gap-4 p-6 bg-slate-50 rounded-2xl animate-pulse">
                      <div class="w-12 h-12 bg-slate-200 rounded-xl"></div>
                      <div class="flex-1 space-y-2">
                        <div class="h-3 bg-slate-200 rounded w-48"></div>
                        <div class="h-2 bg-slate-200 rounded w-32"></div>
                      </div>
                      <div class="h-8 w-20 bg-slate-200 rounded-xl"></div>
                    </div>
                  </div>

                  <!-- Empty State -->
                  <div v-else-if="orders.length === 0" class="text-center py-16 bg-gradient-to-br from-slate-50 to-purple-50/30 rounded-[2rem]">
                    <Icon icon="solar:bag-bold-duotone" class="w-16 h-16 text-slate-300 mx-auto mb-4" />
                    <p class="text-slate-400 font-bold text-lg">Vous n'avez pas encore passé de commande.</p>
                    <p class="text-slate-300 font-medium text-sm mt-2">Explorez notre collection pour trouver votre bonheur !</p>
                    <router-link to="/" class="mt-6 inline-flex items-center gap-2 px-8 py-4 bg-purple-600 text-white font-black uppercase tracking-widest text-[10px] rounded-2xl hover:bg-purple-700 transition-all shadow-xl shadow-purple-200/50 hover:shadow-purple-300/50">
                      <Icon icon="solar:shop-bold" class="w-4 h-4" />
                      Commencer mes achats
                    </router-link>
                  </div>

                  <!-- Orders List -->
                  <div v-else class="space-y-4">
                    <div v-for="(order, i) in orders.slice(0, 3)" :key="order.id" 
                      class="flex items-center justify-between p-6 bg-slate-50 rounded-2xl border border-slate-100 hover:bg-purple-50/30 hover:border-purple-100 transition-all duration-300 group"
                      :style="{ animationDelay: (i * 100) + 'ms' }">
                      <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center font-black text-slate-900 shadow-sm border border-slate-100 group-hover:shadow-md group-hover:border-purple-200 transition-all">
                          #{{ order.id }}
                        </div>
                        <div>
                          <p class="text-sm font-black text-slate-900 uppercase">Commande du {{ formatDate(order.created_at) }}</p>
                          <div class="flex items-center gap-3 mt-1">
                            <span class="text-xs text-slate-400 font-bold">{{ formatAmount(order.total_amount) }} FCFA</span>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider" :class="getStatusClass(order.status)">
                              {{ getStatusLabel(order.status) }}
                            </span>
                          </div>
                        </div>
                      </div>
                      <button @click="viewOrderDetail(order)" class="px-4 py-2 bg-white rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-slate-900 hover:text-white transition-all shadow-sm">
                        Détails
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- ===== Orders Tab ===== -->
              <div v-if="activeTab === 'orders'" class="space-y-8">
                <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
                  <h1 class="text-3xl font-black text-slate-900 italic uppercase">Mes <span class="text-purple-600">Commandes</span></h1>
                  <div class="flex gap-2">
                    <button v-for="filter in orderFilters" :key="filter.value" 
                      @click="orderFilter = filter.value"
                      class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300"
                      :class="orderFilter === filter.value ? 'bg-slate-900 text-white shadow-md' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'">
                      {{ filter.label }}
                    </button>
                  </div>
                </div>

                <div v-if="loadingOrders" class="space-y-4">
                  <div v-for="n in 4" :key="n" class="p-6 bg-slate-50 rounded-2xl animate-pulse h-24"></div>
                </div>

                <div v-else-if="filteredOrders.length === 0" class="text-center py-16 bg-slate-50 rounded-[2rem]">
                  <Icon icon="solar:box-minimalistic-linear" class="w-16 h-16 text-slate-300 mx-auto mb-4" />
                  <p class="text-slate-400 font-bold">Aucune commande trouvée.</p>
                </div>

                <div v-else class="space-y-6">
                  <div v-for="order in filteredOrders" :key="order.id" 
                    class="p-6 md:p-8 bg-slate-50 rounded-[2rem] border border-slate-100 hover:border-purple-200 transition-all duration-300 space-y-6">
                    
                    <!-- Order Header -->
                    <div class="flex flex-col md:flex-row justify-between md:items-center gap-4 pb-4 border-b border-slate-200/60">
                      <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-purple-600 text-white rounded-2xl flex items-center justify-center font-black">
                          #{{ order.id }}
                        </div>
                        <div>
                          <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Passée le {{ formatDate(order.created_at) }}</p>
                          <p class="text-lg font-black text-slate-900 mt-0.5">{{ formatAmount(order.total_amount) }} FCFA</p>
                        </div>
                      </div>
                      <div class="flex items-center gap-3">
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest" :class="getStatusClass(order.status)">
                          {{ getStatusLabel(order.status) }}
                        </span>
                      </div>
                    </div>

                    <!-- Order Timeline -->
                    <div v-if="order.status !== 'cancelled'" class="py-2">
                      <div class="relative">
                        <div class="h-1 bg-slate-200 rounded-full w-full">
                          <div class="h-1 bg-purple-600 rounded-full transition-all duration-500" :style="{ width: getTimelineWidth(order.status) }"></div>
                        </div>
                        <div class="flex justify-between -mt-2">
                          <div v-for="step in timelineSteps" :key="step.key" class="flex flex-col items-center">
                            <div class="w-4 h-4 rounded-full border-2 transition-colors duration-300"
                              :class="isStepCompleted(order.status, step.key) ? 'bg-purple-600 border-purple-600' : 'bg-white border-slate-300'"></div>
                            <span class="text-[9px] font-bold mt-2 uppercase tracking-wider"
                              :class="isStepCompleted(order.status, step.key) ? 'text-purple-600' : 'text-slate-400'">
                              {{ step.label }}
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- ===== Profile Tab ===== -->
              <div v-if="activeTab === 'profile'" class="space-y-10">
                <div class="flex justify-between items-center">
                  <h1 class="text-3xl font-black text-slate-900 italic uppercase">Mon <span class="text-purple-600">Profil</span></h1>
                  <transition name="fade">
                    <span v-if="profileMessage" class="text-xs font-bold px-4 py-2 rounded-xl"
                      :class="profileMessageType === 'success' ? 'bg-green-50 text-green-600' : 'bg-rose-50 text-rose-600'">
                      {{ profileMessage }}
                    </span>
                  </transition>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                  <div class="space-y-6">
                    <div class="space-y-2 group">
                      <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Prénom</label>
                      <input type="text" v-model="userForm.first_name" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-300 font-bold text-slate-900 transition-all">
                    </div>
                    <div class="space-y-2 group">
                      <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nom</label>
                      <input type="text" v-model="userForm.last_name" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-300 font-bold text-slate-900 transition-all">
                    </div>
                  </div>
                  <div class="space-y-6">
                    <div class="space-y-2">
                      <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Email (non modifiable)</label>
                      <input type="email" :value="user?.email" disabled class="w-full px-6 py-4 bg-slate-100 border border-slate-200 rounded-2xl focus:outline-none font-bold text-slate-400 cursor-not-allowed">
                    </div>
                    <div class="space-y-2 group">
                      <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Téléphone</label>
                      <input type="text" v-model="userForm.phone" placeholder="+229 XX XX XX XX" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-300 font-bold text-slate-900 transition-all">
                    </div>
                  </div>
                  <!-- Address Field -->
                  <div class="space-y-2 md:col-span-2 group">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Adresse de livraison par défaut</label>
                    <textarea v-model="userForm.address" rows="3" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-300 font-bold text-slate-900 transition-all" placeholder="Votre adresse complète pour la livraison..."></textarea>
                  </div>
                </div>

                <div class="flex justify-end pt-6">
                  <button @click="updateProfile" :disabled="savingProfile" class="group px-10 py-5 bg-slate-900 text-white font-black uppercase tracking-widest text-xs rounded-2xl hover:bg-purple-600 transition-all duration-300 shadow-xl shadow-slate-900/10 hover:shadow-purple-200 hover:scale-[1.02] active:scale-95 disabled:opacity-50">
                    <span v-if="!savingProfile" class="flex items-center gap-2">
                      <Icon icon="solar:check-read-linear" class="w-4 h-4" />
                      Sauvegarder les modifications
                    </span>
                    <span v-else class="flex items-center gap-2">
                      <div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                      Enregistrement...
                    </span>
                  </button>
                </div>
              </div>

              <!-- ===== Addresses Tab ===== -->
              <div v-if="activeTab === 'addresses'" class="space-y-10">
                <div class="flex items-center justify-between">
                  <h1 class="text-3xl font-black text-slate-900 italic uppercase">Mes <span class="text-purple-600">Adresses</span></h1>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div v-if="user?.address" class="p-8 border border-purple-100 bg-gradient-to-br from-purple-50/50 to-indigo-50/30 rounded-[2rem] relative group w-full hover:border-purple-200 transition-all duration-300 hover:shadow-lg hover:shadow-purple-100">
                    <div class="flex items-center justify-between mb-4">
                      <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center">
                          <Icon icon="solar:home-bold-duotone" class="w-5 h-5 text-purple-600" />
                        </div>
                        <div>
                          <h4 class="font-black text-slate-900 uppercase text-xs tracking-widest">Adresse Principale</h4>
                          <span class="text-[9px] font-bold text-purple-600 uppercase">Par défaut</span>
                        </div>
                      </div>
                    </div>
                    <p class="text-slate-600 font-bold leading-relaxed whitespace-pre-line text-sm mt-2">{{ user.address }}</p>
                    <div class="mt-6 flex gap-4">
                      <button @click="activeTab = 'profile'" class="flex items-center gap-1 text-[10px] font-black text-purple-600 uppercase tracking-widest hover:text-purple-800 transition-colors">
                        <Icon icon="solar:pen-bold" class="w-3 h-3" />
                        Modifier
                      </button>
                    </div>
                  </div>
                  <div v-if="!user?.address" class="p-8 border-2 border-dashed border-slate-200 bg-slate-50/50 rounded-[2rem] text-center w-full md:col-span-2 hover:border-purple-300 transition-colors">
                    <Icon icon="solar:map-point-bold-duotone" class="w-16 h-16 text-slate-300 mx-auto mb-4" />
                    <p class="text-slate-400 font-bold mb-4">Aucune adresse enregistrée pour le moment.</p>
                    <button @click="activeTab = 'profile'" class="px-6 py-3 bg-purple-600 text-white font-black uppercase tracking-widest text-[10px] rounded-2xl hover:bg-purple-700 transition-all shadow-lg shadow-purple-200/50">
                      Ajouter une adresse
                    </button>
                  </div>
                </div>
              </div>

              <!-- ===== Security Tab ===== -->
              <div v-if="activeTab === 'security'" class="space-y-10">
                <h1 class="text-3xl font-black text-slate-900 italic uppercase">Sécurité <span class="text-purple-600">& Confidentialité</span></h1>
                
                <!-- Change Password -->
                <div class="p-8 bg-slate-50 rounded-[2rem] border border-slate-100">
                  <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center">
                      <Icon icon="solar:lock-password-bold-duotone" class="w-5 h-5 text-purple-600" />
                    </div>
                    <div>
                      <h3 class="font-black text-slate-900 uppercase text-xs tracking-widest">Changer le mot de passe</h3>
                      <p class="text-[10px] text-slate-400 font-bold mt-0.5">Protégez votre compte avec un mot de passe fort</p>
                    </div>
                  </div>

                  <transition name="fade">
                    <div v-if="passwordMessage" class="p-4 rounded-2xl text-sm font-bold text-center mb-6" 
                      :class="passwordMessageType === 'success' ? 'bg-green-50 text-green-600 border border-green-200' : 'bg-rose-50 text-rose-600 border border-rose-200'">
                      {{ passwordMessage }}
                    </div>
                  </transition>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2 group">
                      <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Mot de passe actuel</label>
                      <div class="relative">
                        <input :type="showCurrentPw ? 'text' : 'password'" v-model="passwordForm.current" placeholder="••••••••" class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-300 font-bold text-slate-900 transition-all pr-12">
                        <button @click="showCurrentPw = !showCurrentPw" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                          <Icon :icon="showCurrentPw ? 'solar:eye-bold' : 'solar:eye-closed-bold'" class="w-5 h-5" />
                        </button>
                      </div>
                    </div>
                    <div></div>
                    <div class="space-y-2 group">
                      <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nouveau mot de passe</label>
                      <div class="relative">
                        <input :type="showNewPw ? 'text' : 'password'" v-model="passwordForm.new_password" placeholder="••••••••" class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-300 font-bold text-slate-900 transition-all pr-12">
                        <button @click="showNewPw = !showNewPw" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                          <Icon :icon="showNewPw ? 'solar:eye-bold' : 'solar:eye-closed-bold'" class="w-5 h-5" />
                        </button>
                      </div>
                      <!-- Password Strength -->
                      <div v-if="passwordForm.new_password" class="space-y-2 mt-2">
                        <div class="flex gap-1">
                          <div v-for="n in 4" :key="n" class="h-1 flex-1 rounded-full transition-colors duration-300"
                            :class="passwordStrength >= n ? strengthColors[passwordStrength - 1] : 'bg-slate-200'"></div>
                        </div>
                        <p class="text-[9px] font-bold" :class="strengthTextColors[passwordStrength - 1] || 'text-slate-400'">
                          {{ strengthLabels[passwordStrength - 1] || 'Trop faible' }}
                        </p>
                      </div>
                    </div>
                    <div class="space-y-2 group">
                      <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Confirmer le mot de passe</label>
                      <div class="relative">
                        <input :type="showConfirmPw ? 'text' : 'password'" v-model="passwordForm.confirm" placeholder="••••••••" class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-300 font-bold text-slate-900 transition-all pr-12">
                        <button @click="showConfirmPw = !showConfirmPw" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                          <Icon :icon="showConfirmPw ? 'solar:eye-bold' : 'solar:eye-closed-bold'" class="w-5 h-5" />
                        </button>
                      </div>
                      <p v-if="passwordForm.confirm && passwordForm.new_password !== passwordForm.confirm" class="text-rose-500 text-[10px] font-bold">
                        Les mots de passe ne correspondent pas
                      </p>
                    </div>
                  </div>
                  <div class="flex justify-end pt-6">
                    <button @click="changePassword" :disabled="changingPassword || !canChangePassword" class="group px-8 py-4 bg-slate-900 text-white font-black uppercase tracking-widest text-[10px] rounded-2xl hover:bg-purple-600 transition-all duration-300 shadow-lg disabled:opacity-40 disabled:cursor-not-allowed">
                      <span v-if="!changingPassword" class="flex items-center gap-2">
                        <Icon icon="solar:shield-check-bold" class="w-4 h-4" />
                        Modifier le mot de passe
                      </span>
                      <span v-else class="flex items-center gap-2">
                        <div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                        Modification...
                      </span>
                    </button>
                  </div>
                </div>

                <!-- Session Info -->
                <div class="p-8 bg-slate-50 rounded-[2rem] border border-slate-100">
                  <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                      <Icon icon="solar:devices-bold-duotone" class="w-5 h-5 text-blue-600" />
                    </div>
                    <div>
                      <h3 class="font-black text-slate-900 uppercase text-xs tracking-widest">Sessions & Appareils</h3>
                      <p class="text-[10px] text-slate-400 font-bold mt-0.5">Gérez vos connexions actives</p>
                    </div>
                  </div>

                  <div class="flex items-center justify-between p-4 bg-white rounded-2xl border border-slate-100">
                    <div class="flex items-center gap-3">
                      <Icon icon="solar:monitor-bold" class="w-8 h-8 text-slate-400" />
                      <div>
                        <p class="font-bold text-slate-900 text-sm">Cet appareil</p>
                        <p class="text-[10px] text-green-600 font-bold flex items-center gap-1">
                          <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                          Session active
                        </p>
                      </div>
                    </div>
                    <button @click="handleLogout" class="text-[10px] font-black text-rose-500 uppercase tracking-widest hover:text-rose-700 transition-colors">
                      Se déconnecter
                    </button>
                  </div>
                </div>
              </div>

            </div>
          </transition>
        </main>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch, nextTick } from 'vue';
import { Icon } from '@iconify/vue';
import { useAuthStore } from '../stores/auth';
import { useWishlistStore } from '../stores/wishlist';
import api from '../services/api';
import Swal from 'sweetalert2';

const authStore = useAuthStore();
const wishlistStore = useWishlistStore();
const user = computed(() => authStore.user);
const wishlistCount = computed(() => wishlistStore.totalItems);

const activeTab = ref('overview');
const orders = ref([]);
const totalSpent = ref(0);
const loadingOrders = ref(true);
const savingProfile = ref(false);
const sidebarVisible = ref(false);
const profileMessage = ref('');
const profileMessageType = ref('success');

// Password change state
const passwordForm = ref({ current: '', new_password: '', confirm: '' });
const changingPassword = ref(false);
const passwordMessage = ref('');
const passwordMessageType = ref('success');
const showCurrentPw = ref(false);
const showNewPw = ref(false);
const showConfirmPw = ref(false);

// Order filter
const orderFilter = ref('all');
const orderFilters = [
  { value: 'all', label: 'Toutes' },
  { value: 'pending', label: 'En attente' },
  { value: 'completed', label: 'Livrées' },
];

// Password strength
const strengthColors = ['bg-rose-500', 'bg-amber-500', 'bg-blue-500', 'bg-green-500'];
const strengthTextColors = ['text-rose-500', 'text-amber-500', 'text-blue-500', 'text-green-500'];
const strengthLabels = ['Faible', 'Moyen', 'Bon', 'Excellent'];

const passwordStrength = computed(() => {
  const pw = passwordForm.value.new_password;
  if (!pw) return 0;
  let score = 0;
  if (pw.length >= 8) score++;
  if (/[A-Z]/.test(pw) && /[a-z]/.test(pw)) score++;
  if (/[0-9]/.test(pw)) score++;
  if (/[^A-Za-z0-9]/.test(pw)) score++;
  return score;
});

const canChangePassword = computed(() => {
  return passwordForm.value.current && 
         passwordForm.value.new_password && 
         passwordForm.value.confirm &&
         passwordForm.value.new_password === passwordForm.value.confirm &&
         passwordStrength.value >= 2;
});

// Menu items
const menuItems = computed(() => [
  { id: 'overview', label: 'Vue d\'ensemble', icon: 'solar:widget-bold-duotone' },
  { id: 'orders', label: 'Commandes', icon: 'solar:bag-bold-duotone', badge: orders.value.length || null },
  { id: 'profile', label: 'Profil', icon: 'solar:user-bold-duotone' },
  { id: 'addresses', label: 'Adresses', icon: 'solar:map-point-bold-duotone' },
  { id: 'security', label: 'Sécurité', icon: 'solar:shield-keyhole-bold-duotone' },
]);

// Stats cards
const statsCards = computed(() => [
  { 
    label: 'Commandes', 
    value: orders.value.length, 
    icon: 'solar:bag-bold-duotone', 
    bgClass: 'bg-purple-50 border-purple-100', 
    iconColor: 'text-purple-600', 
    textColor: 'text-purple-900' 
  },
  { 
    label: 'Favoris', 
    value: wishlistCount.value, 
    icon: 'solar:heart-bold-duotone', 
    bgClass: 'bg-rose-50 border-rose-100', 
    iconColor: 'text-rose-500', 
    textColor: 'text-rose-900' 
  },
  { 
    label: 'Dépenses', 
    value: totalSpent.value.toLocaleString(), 
    suffix: 'FCFA', 
    icon: 'solar:wallet-bold-duotone', 
    bgClass: 'bg-blue-50 border-blue-100', 
    iconColor: 'text-blue-600', 
    textColor: 'text-blue-900' 
  },
]);

// Loyalty system
const loyaltyPoints = computed(() => Math.floor(totalSpent.value / 100));
const loyaltyTier = computed(() => {
  const pts = loyaltyPoints.value;
  if (pts >= 10000) return 'Diamant';
  if (pts >= 5000) return 'Or';
  if (pts >= 1000) return 'Argent';
  return 'Bronze';
});
const loyaltyProgress = computed(() => {
  const pts = loyaltyPoints.value;
  if (pts >= 10000) return 100;
  if (pts >= 5000) return (pts / 10000) * 100;
  if (pts >= 1000) return (pts / 5000) * 100;
  return (pts / 1000) * 100;
});
const loyaltyNextTier = computed(() => {
  const pts = loyaltyPoints.value;
  if (pts >= 10000) return '🏆 Niveau maximum atteint !';
  if (pts >= 5000) return `${10000 - pts} pts avant Diamant`;
  if (pts >= 1000) return `${5000 - pts} pts avant Or`;
  return `${1000 - pts} pts avant Argent`;
});

// Member since
const memberSince = computed(() => {
  if (!user.value?.created_at) return 'Récemment';
  return new Date(user.value.created_at).toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' });
});

// Filtered orders
const filteredOrders = computed(() => {
  if (orderFilter.value === 'all') return orders.value;
  return orders.value.filter(o => o.status === orderFilter.value);
});

// Timeline
const timelineSteps = [
  { key: 'pending', label: 'Confirmée' },
  { key: 'processing', label: 'Préparation' },
  { key: 'shipped', label: 'Expédiée' },
  { key: 'completed', label: 'Livrée' },
];

const statusOrder = { pending: 0, processing: 1, shipped: 2, completed: 3, cancelled: -1 };

const isStepCompleted = (status, stepKey) => {
  if (status === 'cancelled') return false;
  return statusOrder[status] >= statusOrder[stepKey];
};

const getTimelineWidth = (status) => {
  const widths = { pending: '0%', processing: '33%', shipped: '66%', completed: '100%', cancelled: '0%' };
  return widths[status] || '0%';
};

const userForm = ref({
  first_name: '',
  last_name: '',
  phone: '',
  address: '',
});

// Watch user info updates to sync with form
watch(user, (newUser) => {
  if (newUser) {
    userForm.value.first_name = newUser.first_name || '';
    userForm.value.last_name = newUser.last_name || '';
    userForm.value.phone = newUser.phone || '';
    userForm.value.address = newUser.address || '';
  }
}, { immediate: true });

const formatDate = (dateStr) => {
  if (!dateStr) return '';
  return new Date(dateStr).toLocaleDateString('fr-FR', {
    day: 'numeric', month: 'long', year: 'numeric'
  });
};

const formatAmount = (amount) => {
  return parseFloat(amount).toLocaleString('fr-FR');
};

const getStatusClass = (status) => {
  switch (status?.toLowerCase()) {
    case 'completed': return 'bg-green-100 text-green-700';
    case 'livré': return 'bg-green-100 text-green-700';
    case 'processing': return 'bg-blue-100 text-blue-700';
    case 'en cours': return 'bg-blue-100 text-blue-700';
    case 'shipped': return 'bg-indigo-100 text-indigo-700';
    case 'pending': return 'bg-amber-100 text-amber-700';
    case 'cancelled': return 'bg-rose-100 text-rose-700';
    case 'annulé': return 'bg-rose-100 text-rose-700';
    default: return 'bg-slate-100 text-slate-700';
  }
};

const getStatusLabel = (status) => {
  const labels = {
    pending: 'En attente',
    processing: 'En préparation',
    shipped: 'Expédiée',
    completed: 'Livrée',
    cancelled: 'Annulée'
  };
  return labels[status] || status;
};

const viewOrderDetail = (order) => {
  Swal.fire({
    title: `Commande #${order.id}`,
    html: `
      <div style="text-align:left; font-family: inherit;">
        <p><strong>Date:</strong> ${formatDate(order.created_at)}</p>
        <p><strong>Statut:</strong> ${getStatusLabel(order.status)}</p>
        <p><strong>Total:</strong> ${formatAmount(order.total_amount)} FCFA</p>
      </div>
    `,
    confirmButtonColor: '#9333ea',
    confirmButtonText: 'Fermer'
  });
};

const fetchOrders = async () => {
  loadingOrders.value = true;
  try {
    const response = await api.get('/orders/get_user_orders.php');
    orders.value = response.data.data || [];
    totalSpent.value = orders.value.reduce((acc, order) => acc + parseFloat(order.total_amount || 0), 0);
  } catch (err) {
    console.warn('Orders fetch failed:', err);
  } finally {
    loadingOrders.value = false;
  }
};

const handleLogout = async () => {
  const result = await Swal.fire({
    title: 'Déconnexion ?',
    text: 'Voulez-vous vraiment vous déconnecter ?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#9333ea',
    confirmButtonText: 'Oui, déconnexion',
    cancelButtonText: 'Annuler'
  });

  if (result.isConfirmed) {
    await authStore.logout();
    window.location.href = '/';
  }
};

const updateProfile = async () => {
  savingProfile.value = true;
  profileMessage.value = '';
  try {
    await authStore.updateProfile(userForm.value);
    profileMessage.value = '✅ Profil mis à jour avec succès !';
    profileMessageType.value = 'success';
    setTimeout(() => { profileMessage.value = ''; }, 4000);
  } catch (err) {
    profileMessage.value = typeof err === 'string' ? err : 'Erreur lors de la mise à jour';
    profileMessageType.value = 'error';
  } finally {
    savingProfile.value = false;
  }
};

const changePassword = async () => {
  if (!canChangePassword.value) return;
  
  changingPassword.value = true;
  passwordMessage.value = '';
  try {
    await api.post('/auth/change_password.php', {
      current_password: passwordForm.value.current,
      new_password: passwordForm.value.new_password
    });
    passwordMessage.value = '✅ Mot de passe modifié avec succès !';
    passwordMessageType.value = 'success';
    passwordForm.value = { current: '', new_password: '', confirm: '' };
    setTimeout(() => { passwordMessage.value = ''; }, 4000);
  } catch (err) {
    passwordMessage.value = err.response?.data?.error || 'Erreur lors du changement de mot de passe';
    passwordMessageType.value = 'error';
  } finally {
    changingPassword.value = false;
  }
};

onMounted(async () => {
  await nextTick();
  sidebarVisible.value = true;
  
  if (authStore.isAuthenticated) {
    fetchOrders();
  }
});
</script>

<style scoped>
/* Tab transition */
.tab-fade-enter-active,
.tab-fade-leave-active {
  transition: opacity 0.25s ease, transform 0.25s ease;
}
.tab-fade-enter-from {
  opacity: 0;
  transform: translateY(12px);
}
.tab-fade-leave-to {
  opacity: 0;
  transform: translateY(-12px);
}

/* Fade transition */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Slide in */
.animate-slide-in-left {
  animation: slideInLeft 0.5s ease-out;
}

@keyframes slideInLeft {
  from {
    opacity: 0;
    transform: translateX(-24px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}
</style>
