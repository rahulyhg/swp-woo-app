import { Component } from '@angular/core';
import {  NavController, NavParams, ViewController, ModalController } from 'ionic-angular';
import {Storage} from '@ionic/Storage';
import { CheckoutPage } from '../checkout/checkout';
//import { LoginPage } from '../login/login';

@Component({
  selector: 'page-cart',
  templateUrl: 'cart.html',
})

export class CartPage {


  cartItems: any [];
  cartTotal: any;
  showEmptyCartMsg: boolean = false;

  constructor(public navCtrl: NavController, public navParams: NavParams,
    public storage: Storage,
    public viewCtrl: ViewController,
    public modalCtrl: ModalController) {
      
        
   this.storage.forEach( (value, key, index) => {
    console.log("This is the value", value)
    console.log("from the key", key)
    console.log("Index is", index)
   })
   
    this.cartTotal = 0.0;
    this.cartItems =[];

    this.storage.ready().then(()=>{
      this.storage.get("cart").then((data)=>{
        this.cartItems = data;
        console.log(this.cartItems);
        console.log(this.cartItems[0].product.image.src)

        if(this.cartItems.length > 0){

          this.cartItems.forEach( (item, index)=> {
            this.cartTotal = this.cartTotal + (item.product.price * item.qty) ;
            console.log(this.cartTotal);
          })
        }
        else 
        {
          this.showEmptyCartMsg = true;
        }

      })
    })

  }

  swp_removeFromCart(item,i){

    let price = item.product.price;
    let qty = item.qty;

    console.log(this.cartTotal);

    console.log (this.cartItems);
    this.cartItems.splice(i, 1); //to remove item
    console.log (this.cartItems);

    this.storage.set("cart", this.cartItems).then( ()=> {
      this.cartTotal = this.cartTotal - (price * qty);
    });
    console.log (this.cartItems);

    if (this.cartItems.length == 0){
      this.showEmptyCartMsg = true;
    }
  }

swp_increaseQty(item,i){

  let price = item.product.price;
  console.log(price);

    console.log("product added");
    //return this.removeFromCart(i,1);

    this.cartItems.splice(i,item);
    console.log(this.cartItems);

    this.storage.set("cart", this.cartItems).then( ()=> {
     
      item.amount = Number(item.amount) + Number(price);
      this.cartTotal = Number(this.cartTotal) + Number(price);
      item.qty ++;
     
      console.log(item.amount);
      console.log(this.cartTotal);

    });   
  }

swp_decreaseQty(item,i){
    console.log("product removed");
    //return this.removeFromCart(i,-1);

    this.cartItems.splice(i, item);

    console.log (this.cartItems);

    this.storage.set("cart", this.cartItems).then( ()=> {
     
      this.cartTotal = this.cartTotal - item.product.price;
      item.qty--;
      item.amount= item.amount- item.product.price;
      
      if (item.qty <= 0)
    {
      console.log ("this is the last item");
   //   this.removeFromCart(item,i);
    }
    
    });
  }

  swp_Modalclose(){
    this.viewCtrl.dismiss();
  }

  swp_checkout(){
    this.storage.get('UserLoginInfo').then((data)=>{
      if(data != null)
      {
        this.navCtrl.push(CheckoutPage);
      }
      else{ 
      //  this.navCtrl.push(LoginPage,{next: CheckoutPage}); // here after login user will be taken to checkout page directly with the help of next paramenter
      }
    })
    
  }

  


}
