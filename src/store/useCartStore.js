import { defineStore } from 'pinia'
import axios from 'axios'
import { useToast } from 'vue-toastification'
const toast = useToast()


export const useCartStore = defineStore('cart', {
    state: () => {
        return {
            cartItems: [],
            products: []
        }
    },
    getters: {
        countCartItems(state) {
            return state.cartItems.length
        },
        getCartItems(state) {
            return state.cartItems
        },
        getProducts(state) {
            return state.products
        },
    },
    actions: {
        addToCart(item) {
            let index = this.cartItems.findIndex(product => product.id === item.id)
            if(index !== -1) {
                this.cartItems[index].quantity += 1
                toast.success('Product quantity increased', {
                    timeout: 2000
                })
            }else {
                item.quantity = 1
                this.cartItems.push(item)
                toast.success('Product added to the cart', {
                    timeout: 2000
                })
            }
        },
        incrementQ(item) {
            let index = this.cartItems.findIndex(product => product.id === item.id)
            if(index !== -1) {
                this.cartItems[index].quantity += 1
                toast.success('Product quantity increased', {
                    timeout: 2000
                })
            }else {
                toast.error('Product not found', {
                    timeout: 2000
                })
            }
        },
        decrementQ(item) {
            let index = this.cartItems.findIndex(product => product.id === item.id)
            if(index !== -1) {
                this.cartItems[index].quantity -= 1
                if(this.cartItems[index].quantity === 0) {
                    this.cartItems = this.cartItems.filter(product => product.id !== item.id)
                }
                toast.success('Product quantity decreased', {
                    timeout: 2000
                })
            }else {
                toast.error('Product not found', {
                    timeout: 2000
                })
            }
        },
        removeFromCart(item) {
            this.cartItems = this.cartItems.filter(product => product.id !== item.id)
            toast.success('Product removed from the cart', {
                timeout: 2000
            })
        },
     async updateProduct(product) {
      const toast = useToast()

      const { id, product_image, ...data } = product

      let requestData = data
      let config = {}

      if (product_image instanceof File) {
        const formData = new FormData()
        Object.keys(data).forEach(key => {
          formData.append(key, data[key])
        })
        formData.append('product_image', product_image)
        requestData = formData
        config = { headers: { 'Content-Type': 'multipart/form-data' } }
      }

      try {
        const response = await axios.put(`http://127.0.0.1:8000/api/products/${id}`, requestData, config)

        // update in state with full response data
        const updatedProduct = response.data.data || response.data
        const index = this.products.findIndex(p => p.id === id)
        if (index !== -1) {
          this.products[index] = updatedProduct
        }

        toast.success('Product updated successfully', {
          timeout: 2000
        })
        
        return updatedProduct
      } catch (error) {
        toast.error('Error updating product', {
          timeout: 2000
        })
        console.error(error)
      }
    },

    async uploadProductImages(productId, files) {
      const toast = useToast()
      const formData = new FormData()

      for (let i = 0; i < files.length; i++) {
        formData.append('product_images[]', files[i])
      }

      try {
        const response = await axios.post(`http://127.0.0.1:8000/api/products/${productId}/images`, formData, {
          headers: {'Content-Type': 'multipart/form-data'}
        })

        toast.success('Product images uploaded successfully', {
          timeout: 2000
        })

        if (response.data.data) {
          // update product in local state too
          const updatedProduct = response.data.data
          const index = this.products.findIndex(p => p.id === productId)
          if (index !== -1) {
            this.products[index] = updatedProduct
          }
          return updatedProduct
        }

        return null
      } catch (error) {
        toast.error('Error uploading images', {
          timeout: 2000
        })
        console.error(error)
      }
    },
     async deleteProduct(id) {
      const toast = useToast()

      if (!confirm('Delete this product?')) return

      try {
        await axios.delete(`http://127.0.0.1:8000/api/products/${id}`)

        // remove from state instantly
        this.products = this.products.filter(p => p.id !== id)

        toast.success('Product deleted successfully', {
          timeout: 2000
        })
      } catch (error) {
        toast.error('Error deleting product', {
          timeout: 2000
        })
        console.error(error)
      }
    }
    }
})