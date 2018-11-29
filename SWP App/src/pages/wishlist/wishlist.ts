import { Component } from '@angular/core';
import {  NavController, NavParams, ToastController } from 'ionic-angular';
import {Storage} from '@ionic/Storage';
import {Http, Headers} from '@angular/http';
import { ProductDetailsPage } from '../product-details/product-details';
import { CartPage } from '../cart/cart';


@Component({
  selector: 'page-wishlist',
  templateUrl: 'wishlist.html',
})
export class WishlistPage {

  products: any[];
  cartItems: any[];

  constructor(public navCtrl: NavController, 
    public storage: Storage,
    public navParams: NavParams,
    public toastCtrl: ToastController) {
      
    this.products =[];
    this.cartItems = [];
  
this.storage.get('wishlist').then((data)=>{
 
    this.products=data;
})
console.log(this.products);

this.storage.get('cart').then((data)=>{
 
  this.cartItems=data;
})
console.log(this.cartItems);

     this.storage.forEach( (value, key, index) => {
      console.log("This is the value", value)
     console.log("from the key", key)
     console.log("Index is", index)
     })
  
    }

/** Go to product details page   */    
   swp_OpenProductDetailsPage(event,product_id){
    this.navCtrl.push(ProductDetailsPage,{product_id:product_id});
  }

  removeFromWishlist(event, i)
  {
    console.log (this.products);
    this.products.splice(i, 1); //to remove item
    console.log (this.products);

    this.storage.set("wishlist", this.products);
  }

addToCart(event, product,i){

  this.cartItems.push(
    {"product": product,
   "qty": 1,
  "amount": parseFloat(product.price)
});
  this.storage.set("cart", this.cartItems);

  this.removeFromWishlist(event,i);

   
   this.storage.forEach( (value, key, index) => {
   console.log("This is the value", value)
   console.log("from the key", key)
   console.log("Index is", index)
  })
  }

 /** Go to cart PAge */
 swp_OpenCartPage()
 {
   this.navCtrl.push(CartPage);
 }

}

