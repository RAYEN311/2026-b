<template>
  <div class="product-card-container">
    <div class="product-card">
      
      <img 
        :src="product.product_image + '?v=' + Date.now()" 
        alt="Product Image" 
        class="product-card-image"
      >

      <div v-if="product.images && product.images.length" class="extra-images">
        <img
          v-for="(imgUrl, index) in product.images"
          :key="index"
          :src="imgUrl + '?v=' + Date.now()"
          class="product-thumbnail"
          alt="Extra image"
        />
      </div>

      <input
        ref="multiImageInput"
        type="file"
        multiple
        accept="image/*"
        @change="handleMultiFiles"
        class="hidden-file-input"
      />

      <button
        class="product-card-btn"
        @click="triggerMultiSelect"
      >
        Add Photos
      </button>

      <div class="product-card-body">
        
        <template v-if="!isEditing">
          <h3 class="product-card-title">
            {{ product.product_name }}
          </h3>

          <p class="product-card-desc">
            {{ product.product_desc }}
          </p>

          <p class="product-card-price">
            {{ product.product_price }} DT
          </p>

          <p class="product-card-category">
            Category: {{ product.category }}
          </p>
        </template>

        <template v-else>
          <input v-model="editedProduct.product_name" class="product-input" placeholder="Product Name">
          <textarea v-model="editedProduct.product_desc" class="product-input product-textarea" placeholder="Product Description"></textarea>
          <input v-model.number="editedProduct.product_price" type="number" class="product-input" placeholder="Product Price">
          <select v-model="editedProduct.category" class="product-input">
            <option value="">Select Category</option>
            <option value="motherboard">Motherboard</option>
            <option value="screen">Screen</option>
            <option value="battery">Battery</option>
            <option value="camera">Camera</option>
            <option value="sub housing">Sub Housing</option>
            <option value="speaker">Speaker</option>
            <option value="secondphone">Secondphone</option>
          </select>
          <input type="file" @change="handleImageChange" accept="image/*" class="product-input">
        </template>

        <!-- <button 
          class="product-card-btn"
          @click="store.addToCart(product)"
        >
          🛒 Add to cart
        </button> -->

        <button 
          class="product-card-btn"
          @click="store.deleteProduct(product.id)"
        >
          delete
          
        </button>

        <template v-if="!isEditing">
          <button 
            class="product-card-btn"
            @click="startEdit"
          >
            Edit
          </button>
        </template>

        <template v-else>
          <button 
            class="product-card-btn"
            @click="saveEdit"
          >
            Save
          </button>
          <button 
            class="product-card-btn"
            @click="cancelEdit"
          >
            Cancel
          </button>
        </template>

      </div>
    </div>
  </div>
</template>

<script setup>
    import { useCartStore } from '../../store/useCartStore.js'
    import { useToast } from 'vue-toastification'
    import { ref, reactive } from 'vue'

    const store = useCartStore()

    const props = defineProps({
        product: {
            type: Object,
            required: true
        }
    })

    const isEditing = ref(false)
    const editedProduct = reactive({ ...props.product })
    const newImage = ref(null)
    const multiImageInput = ref(null)

    const startEdit = () => {
        isEditing.value = true
        Object.assign(editedProduct, props.product)
        newImage.value = null
    }

    const handleImageChange = (event) => {
        newImage.value = event.target.files[0]
    }

    const triggerMultiSelect = () => {
        if (multiImageInput.value) {
            multiImageInput.value.click()
        }
    }

    const handleMultiFiles = async (event) => {
        const files = Array.from(event.target.files || [])
        if (!files.length) {
            return
        }

        const updatedProduct = await store.uploadProductImages(props.product.id, files)
        if (updatedProduct) {
            Object.assign(props.product, updatedProduct)
            Object.assign(editedProduct, updatedProduct)
        }

        if (multiImageInput.value) {
            multiImageInput.value.value = null
        }
    }

    const saveEdit = async () => {
        const updateData = { ...editedProduct }
        if (newImage.value) {
            updateData.product_image = newImage.value
        }
        const updatedProduct = await store.updateProduct(updateData)
        if (updatedProduct) {
            Object.assign(props.product, updatedProduct)
            Object.assign(editedProduct, updatedProduct)
        }
        isEditing.value = false
    }

    const cancelEdit = () => {
        isEditing.value = false
        Object.assign(editedProduct, props.product)
    }
</script>

<style>
</style>
<style scoped>

/* Container (grid item) */
.product-card-container {
  width: 100%;
}

/* Card */
.product-card {
  background: #111;
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 10px 25px rgba(0,0,0,0.4);
  transition: 0.3s;
  display: flex;
  flex-direction: column;
  height: 100%;
}

/* Hover effect */
.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 35px rgba(255, 46, 46, 0.2);
}

/* Image */
.product-card-image {
  width: 100%;
  height: 200px;
  object-fit: cover;
}

/* Body */
.product-card-body {
  padding: 15px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  color: white;
}

/* Title */
.product-card-title {
  font-size: 18px;
  font-weight: bold;
}

/* Description */
.product-card-desc {
  font-size: 14px;
  color: #aaa;
  min-height: 40px;
}

/* Price */
.product-card-price {
  font-size: 18px;
  font-weight: bold;
  color: #ff2e2e;
}

/* Button */
.product-card-btn {
  margin-top: auto;
  padding: 10px;
  border: none;
  border-radius: 10px;
  background: #ff2e2e;
  color: white;
  font-weight: bold;
  cursor: pointer;
  transition: 0.3s;
}

/* Input */
.product-input {
  width: 100%;
  padding: 8px;
  margin: 5px 0;
  border: 1px solid #ccc;
  border-radius: 5px;
  font-size: 14px;
}

.product-textarea {
  resize: vertical;
  min-height: 60px;
}

.extra-images {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 8px;
}

.product-thumbnail {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 8px;
  border: 1px solid #444;
}

.hidden-file-input {
  display: none;
}

/* Hover */
.product-card-btn:hover {
  background: #cc0000;
}

/* Responsive grid helper (optional parent use) */
@media (min-width: 768px) {
  .product-card-container {
    width: 48%;
  }
}

@media (min-width: 1024px) {
  .product-card-container {
    width: 30%;
  }
}

</style>