import { ref, computed, watch } from 'vue';

const STORAGE_KEY = 'farmlokal_cart_v1';

const defaultItems = [
  {
    id: 'cart-1',
    name: 'Fresh Tomatoes',
    subtitle: 'Farm Fresh • 1 kg',
    unitLabel: 'kg',
    price: 32,
    oldPrice: 40,
    off: '20% OFF',
    qty: 1,
    image: '/assets/fruits and veggies.png',
  },
  {
    id: 'cart-2',
    name: 'Potatoes',
    subtitle: 'Farm Fresh • 1 kg',
    unitLabel: 'kg',
    price: 28,
    oldPrice: 32,
    off: '12% OFF',
    qty: 2,
    image: '/assets/grocery.PNG',
  },
  {
    id: 'cart-3',
    name: 'Onions',
    subtitle: 'Farm Fresh • 1 kg',
    unitLabel: 'kg',
    price: 24,
    oldPrice: 30,
    off: '20% OFF',
    qty: 1,
    image: '/assets/store-categories-products.png',
  },
  {
    id: 'cart-4',
    name: 'Carrots',
    subtitle: 'Farm Fresh • 1 kg',
    unitLabel: 'kg',
    price: 36,
    oldPrice: 45,
    off: '20% OFF',
    qty: 1,
    image: '/assets/kitcvhen.png',
  },
];

function loadInitialCart() {
  if (typeof window === 'undefined') return defaultItems;
  try {
    const raw = window.localStorage.getItem(STORAGE_KEY);
    if (!raw) return defaultItems;
    const parsed = JSON.parse(raw);
    return Array.isArray(parsed) ? parsed : defaultItems;
  } catch {
    return defaultItems;
  }
}

const cartItems = ref(loadInitialCart());

watch(
  cartItems,
  (val) => {
    if (typeof window !== 'undefined') {
      window.localStorage.setItem(STORAGE_KEY, JSON.stringify(val));
    }
  },
  { deep: true },
);

export function useCart() {
  const totalCount = computed(() =>
    cartItems.value.reduce((sum, item) => sum + (item.qty || 1), 0),
  );

  const mrpTotal = computed(() =>
    cartItems.value.reduce((sum, item) => sum + (item.oldPrice || item.price) * item.qty, 0),
  );

  const subtotal = computed(() =>
    cartItems.value.reduce((sum, item) => sum + item.price * item.qty, 0),
  );

  const discountTotal = computed(() => Math.max(0, mrpTotal.value - subtotal.value));

  const deliveryFee = computed(() => (subtotal.value === 0 ? 0 : 20));

  function addItem(product) {
    const existing = cartItems.value.find((i) => i.id === product.id);
    if (existing) {
      existing.qty += product.qty || 1;
      return;
    }
    cartItems.value.push({
      id: product.id,
      name: product.name,
      subtitle: product.subtitle || `Farm Fresh • ${product.unit || '1 pack'}`,
      unitLabel: product.unitLabel || '',
      price: Number(product.price) || 99,
      oldPrice: Number(product.oldPrice) || Number(product.price) || 120,
      off: product.off || '15% OFF',
      qty: product.qty || 1,
      image: product.image || '/assets/fruits and veggies.png',
    });
  }

  function updateQty(id, delta) {
    const item = cartItems.value.find((i) => i.id === id);
    if (!item) return;
    const next = item.qty + delta;
    if (next <= 0) {
      removeItem(id);
    } else {
      item.qty = next;
    }
  }

  function removeItem(id) {
    cartItems.value = cartItems.value.filter((i) => i.id !== id);
  }

  function clearCart() {
    cartItems.value = [];
  }

  return {
    cartItems,
    totalCount,
    mrpTotal,
    subtotal,
    discountTotal,
    deliveryFee,
    addItem,
    updateQty,
    removeItem,
    clearCart,
  };
}

