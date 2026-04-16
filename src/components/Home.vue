<template>
  <div class="product-form-container">
    <h2 class="product-form-title">
      {{ data.editMode ? "Edit Product" : "Add Product" }}
    </h2>

    <form class="product-form" @submit.prevent="handleSubmit" enctype="multipart/form-data">
      
      <input class="product-form-input" v-model="form.title" type="text" placeholder="Title" required />
      
      <textarea class="product-form-textarea" v-model="form.description" placeholder="Description"></textarea>

      <input class="product-form-input" v-model="form.price" type="number" placeholder="Price (DT)" required />
      
      <select class="product-form-input" v-model="form.category">
        <option value="">Select Category</option>
        <option value="motherboard">Motherboard</option>
        <option value="screen">Screen</option>
        <option value="battery">Battery</option>
        <option value="camera">Camera</option>
        <option value="sub">Sub</option>
        <option value="housing">Housing</option>
        <option value="speaker">Speaker</option>
        <option value="secondphone">Secondphone</option>
      </select>
      
      <input class="product-form-file" type="file" @change="handleImage" />

      <div class="product-form-buttons">
        <button class="product-form-btn product-form-btn-primary" type="submit">
          {{ data.editMode ? "Update" : "Add" }}
        </button>

        <button 
          v-if="data.editMode" 
          class="product-form-btn product-form-btn-cancel" 
          type="button" 
          @click="resetForm"
        >
          Cancel
        </button>
      </div>

    </form>
  </div>
      <ProductList 
        :products="data.products" 
        @delete-product="deleteProduct"
        @edit-product="selectProduct"
      />
</template>

<script setup>
import ProductList from '../components/products/ProductList.vue'
import { onMounted, reactive } from "vue"
import axios from "axios"

const data = reactive({
  products: [],
  editMode: false,
  selectedId: null
})

const form = reactive({
  title: '',
  description: '',
  price: '',
  category: '',
  image: null
})

/* ================= FETCH ================= */
const fetchAllProducts = async () => {
  const res = await axios.get('http://127.0.0.1:8000/api/products/1/20')
  data.products = res.data.data
}

/* ================= IMAGE ================= */
const handleImage = (e) => {
  form.image = e.target.files[0]
}

/* ================= FORM DATA ================= */
const buildFormData = () => {
  const fd = new FormData()
  fd.append('product_name', form.title)
  fd.append('product_desc', form.description)
  fd.append('product_price', form.price)
  fd.append('category', form.category)
  if (form.image) { fd.append('product_image', form.image)}
  return fd
}

/* ================= ADD ================= */
const addProduct = async () => {
  await axios.post('http://127.0.0.1:8000/api/products', buildFormData(), {
    headers: { 'Content-Type': 'multipart/form-data' }
  })
  fetchAllProducts()
  resetForm()
}

/* ================= UPDATE ================= */
const updateProduct = async () => {
  const fd = buildFormData()
  fd.append('_method', 'PUT') // Laravel fix

  await axios.post(`http://127.0.0.1:8000/api/products/${data.selectedId}`, fd, {
    headers: { 'Content-Type': 'multipart/form-data' }
  })

  fetchAllProducts()
  resetForm()
}

/* ================= DELETE ================= */
const deleteProduct = async (id) => {
  await axios.delete(`http://127.0.0.1:8000/api/products/${id}`)
  fetchAllProducts()
}

/* ================= EDIT ================= */
const selectProduct = (product) => {
  data.editMode = true
  data.selectedId = product.id

  form.title = product.product_name
  form.description = product.product_desc
  form.price = product.product_price
  form.category = product.category
  form.image = null
}

/* ================= HANDLE ================= */
const handleSubmit = () => {
  data.editMode ? updateProduct() : addProduct()
}

/* ================= RESET ================= */
const resetForm = () => {
  form.title = ''
  form.description = ''
  form.price = ''
  form.category = ''
  form.image = null

  data.editMode = false
  data.selectedId = null
}

onMounted(fetchAllProducts)
</script>
<style scoped>
/* Container */
.product-form-container {
  max-width: 500px;
  margin: 40px auto;
  padding: 25px;
  background: #111;
  border-radius: 15px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.5);
  color: white;
  font-family: Arial, sans-serif;
}

/* Title */
.product-form-title {
  text-align: center;
  margin-bottom: 20px;
  color: #ff2e2e;
}

/* Form */
.product-form {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

/* Inputs */
.product-form-input,
.product-form-textarea,
.product-form-file {
  padding: 12px;
  border-radius: 10px;
  border: none;
  outline: none;
  background: #1e1e1e;
  color: white;
  transition: 0.3s;
}

/* Focus */
.product-form-input:focus,
.product-form-textarea:focus {
  border: 1px solid #ff2e2e;
  box-shadow: 0 0 5px #ff2e2e;
}

/* Textarea */
.product-form-textarea {
  min-height: 100px;
  resize: none;
}

/* File */
.product-form-file {
  cursor: pointer;
}

/* Buttons container */
.product-form-buttons {
  display: flex;
  gap: 10px;
}

/* Buttons */
.product-form-btn {
  flex: 1;
  padding: 12px;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  font-weight: bold;
  transition: 0.3s;
}

/* Primary */
.product-form-btn-primary {
  background: #ff2e2e;
  color: white;
}

.product-form-btn-primary:hover {
  background: #cc0000;
}

/* Cancel */
.product-form-btn-cancel {
  background: #333;
  color: white;
}

.product-form-btn-cancel:hover {
  background: #555;
}

/* Responsive */
@media (max-width: 600px) {
  .product-form-container {
    margin: 20px;
    padding: 15px;
  }
}
</style>