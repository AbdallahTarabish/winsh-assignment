<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const orders = ref([]);
const loading = ref(false);
const loadError = ref(null);
const assigningId = ref(null);
const rowFeedback = ref({}); // orderId -> { type: 'success' | 'error', text }

async function fetchOrders() {
    loading.value = true;
    loadError.value = null;
    try {
        const { data } = await axios.get('/orders', { params: { status: 'pending' } });
        orders.value = data.data;
    } catch (e) {
        loadError.value = 'Could not load orders. Is the API running and the token valid?';
    } finally {
        loading.value = false;
    }
}

async function assign(order) {
    assigningId.value = order.id;
    delete rowFeedback.value[order.id];
    try {
        const { data } = await axios.post(`/orders/${order.id}/assign`);
        const distance = data.distance_km != null ? ` (${data.distance_km} km)` : '';
        rowFeedback.value[order.id] = {
            type: 'success',
            text: `Assigned to ${data.driver_name ?? 'driver #' + data.driver_id}${distance}`,
        };
        // It is no longer pending — drop it from the list after a short beat.
        setTimeout(() => {
            orders.value = orders.value.filter((o) => o.id !== order.id);
            delete rowFeedback.value[order.id];
        }, 1500);
    } catch (e) {
        const status = e.response?.status;
        const text =
            status === 422
                ? 'No available driver right now.'
                : e.response?.data?.message ?? 'Assignment failed.';
        rowFeedback.value[order.id] = { type: 'error', text };
    } finally {
        assigningId.value = null;
    }
}

onMounted(fetchOrders);
</script>

<template>
    <div class="mx-auto max-w-3xl p-6">
        <header class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Active orders</h1>
                <p class="text-sm text-gray-500">Pending pickups awaiting a driver.</p>
            </div>
            <button
                class="rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50"
                :disabled="loading"
                @click="fetchOrders"
            >
                {{ loading ? 'Refreshing…' : 'Refresh' }}
            </button>
        </header>

        <p v-if="loadError" class="rounded-md bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ loadError }}
        </p>

        <p v-else-if="loading" class="px-1 py-8 text-center text-sm text-gray-500">Loading…</p>

        <p
            v-else-if="orders.length === 0"
            class="rounded-md bg-gray-50 px-4 py-8 text-center text-sm text-gray-500"
        >
            No pending orders. Everything is assigned.
        </p>

        <ul v-else class="divide-y divide-gray-100 overflow-hidden rounded-lg border border-gray-200">
            <li v-for="order in orders" :key="order.id" class="flex items-center justify-between gap-4 p-4">
                <div class="min-w-0">
                    <p class="truncate font-medium text-gray-900">
                        #{{ order.id }} · {{ order.customer_name }}
                    </p>
                    <p class="text-xs text-gray-500">
                        Pickup {{ order.pickup.lat.toFixed(4) }}, {{ order.pickup.lng.toFixed(4) }}
                    </p>
                    <p
                        v-if="rowFeedback[order.id]"
                        class="mt-1 text-xs"
                        :class="rowFeedback[order.id].type === 'success' ? 'text-green-600' : 'text-red-600'"
                    >
                        {{ rowFeedback[order.id].text }}
                    </p>
                </div>
                <button
                    class="shrink-0 rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 disabled:opacity-50"
                    :disabled="assigningId === order.id"
                    @click="assign(order)"
                >
                    {{ assigningId === order.id ? 'Assigning…' : 'Assign' }}
                </button>
            </li>
        </ul>
    </div>
</template>
