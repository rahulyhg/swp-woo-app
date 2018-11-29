import { Component } from '@angular/core';
import {  NavController, NavParams, LoadingController, ToastController, ModalController } from 'ionic-angular';
import {Storage} from '@ionic/Storage';
import { Http, Headers } from '@angular/http';

//constants
import {WC_url} from '..';
import { WishlistPage } from '../wishlist/wishlist';


@Component({
  selector: 'page-product-details',
  templateUrl: 'product-details.html',
})
export class ProductDetailsPage {
  product_id: any;
  product: any;
  similarProducts: any [];
  reviews: any[] = [];
  productVariations: any;
  selectedVariationOptions: any ={};
  selectedVariationProduct: any ={};
  requireOptions: boolean = true;
  isVariation: boolean = false;
  token: any;
  productPrice: any;
  addToFav: boolean = false;
  EnteredQty: any;

  constructor(public navCtrl: NavController, public navParams: NavParams,
    public storage: Storage,
    public http: Http,
    public toastCtrl: ToastController) {
      this.product ={};
      this.productVariations={};
      this.EnteredQty ="";
      
      this.product_id = navParams.get('product_id');
      console.log(this.product_id);
    
    this.storage.get("AdminUser").then((admin)=>{
      this.token = admin.token;

      let headers = new Headers();
       headers.append('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
       headers.append('Authorization', 'Bearer ' + this.token );
    
       let options ={headers:headers};

        // All the Products details based on id
        this.http.get(WC_url + '/wp-json/wc/v2/products/'+ this.product_id,options).subscribe(res=>{
          this.product= res.json();
          console.log(this.product.price);             
        },
          (err)=>{console.log("err")})


        //Product reviews
        this.http.get(WC_url + '/wp-json/wc/v2/products/'+ this.product_id + '/reviews',options).subscribe(res=>{
          this.reviews= res.json();
          console.log(this.reviews);                     
        },
          (err)=>{console.log("err")})
    })
    
  }

  swp_addToWishlist(event,product)
  {
  
    this.addToFav =true
    this.storage.get("wishlist").then((data)=> {

      if(data == null || data.length == 0)
      {
       data = [];
       data.push(product);
        this.storage.set("wishlist", data);
      }   
     else {  
       for (let i = 0 ; i < data.length; i++ )
       {
        if(product.id == data[i].id && this.addToFav == true)
         {
         // data.pop();
          //this.addToFav = false;
         }
         else{
           data.push(product);
         }
       }
       this.storage.set("wishlist", data);
      
     }
   
     })

  }  

  
addtoW(event,product) {
  this.addToFav = !this.addToFav;
  if(this.addToFav)
  {
    this.storage.get("wishlist").then((data)=> {

      if(data == null || data.length == 0)
      {
       data = [];
       data.push(product);
        this.storage.set("wishlist", data);
      }   
     else {  
       for (let i = 0 ; i < data.length; i++ )
       {
        if(product.id == data[i].id)
         {
         // data.pop();
          //this.addToFav = false;
          console.log("already added")
         }
         else{
           data.push(product);
         }
       }
       this.storage.set("wishlist", data); 
     }
   
     })
  }
  else 
  if(!this.addToFav)
  {
    this.storage.get("wishlist").then((data)=> {
      if(data == null || data.lenght ==0)
      {
        console.log("no product")
      }
      else{
       for (let i = 0 ; i < data.length; i++ )
       {
        if(product.id == data[i].id)
         {
          data.pop();
          this.addToFav =false;
         }
       }
       this.storage.set("wishlist", data);   
      }
     })
  }
}

swp_removeFromWishlist($event,product)
  {   
    {
      if(this.addToFav == true)
      {
      this.storage.get("wishlist").then((data)=> {
        if(data == null || data.lenght ==0)
        {
          console.log("no product")
        }
        else{
         for (let i = 0 ; i < data.length; i++ )
         {
          if(product.id == data[i].id)
           {
            data.pop();
            this.addToFav =false;
           }
         }
         this.storage.set("wishlist", data);   
        }
       })
    }
  
  else{
    this.toastCtrl.create({
      message: "removed",
      duration: 3000
    }).present()

  }
}

  }


swp_checkProductVariations(justSelectedAttribute) {
 
  let selectedOption_count = 0;
    for (let k in this.selectedVariationOptions) 
      if (this.selectedVariationOptions.hasOwnProperty(k)) 
      selectedOption_count++;  

    let totalOption_count= 0;
    for (var index = 0; index < this.product.attributes.length; index++) 
    {
      if(this.product.attributes[index].variation)
      totalOption_count++; 
    }

  //checking if user selected all the variation options
  if(totalOption_count != selectedOption_count){
    this.requireOptions = true;
    return;
  } else {
    this.requireOptions = false;
  }
   
  this.storage.get("AdminUser").then((admin)=>{
      this.token = admin.token;

      let headers = new Headers();
       headers.append('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
       headers.append('Authorization', 'Bearer ' + this.token );
    
       let options ={headers:headers};

        // All the Products variations
        this.http.get(WC_url + '/wp-json/wc/v2/products/'+ this.product_id + '/variations/',options).subscribe(res=>{
        this.productVariations = res.json(); 
          
        let i = 0, matchFailed = false;

          if (this.productVariations.length > 0) {
            
            for (i = 0; i < this.productVariations.length; i++) {
              matchFailed = false; 
              let key: string = "";
      
              for (let j = 0; j < this.productVariations[i].attributes.length; j++) {
                key = this.productVariations[i].attributes[j].name;
                console.log(this.selectedVariationOptions[key].toLowerCase()+ " " + this.productVariations[i].attributes[j].option.toLowerCase())
      
                if (this.selectedVariationOptions[key].toLowerCase() == this.productVariations[i].attributes[j].option.toLowerCase()) {
                  //Do nothing 
                  console.log("match found");
                  } 
                else {
                 // console.log(matchFailed)
                  matchFailed = true;
                  break;
                }
              }
      
              if (matchFailed) {
                continue;
              } else {
                //found the matching variation
                //console.log(this.productVariations[i])
                this.productPrice = this.productVariations[i].price;
                this.selectedVariationProduct = this.productVariations[i];
                console.log(this.selectedVariationProduct.price)
                break;
      
              }
      
            }
      
          if(matchFailed == true){
              this.toastCtrl.create({
                message: "No Such Product Found",
                duration: 3000
              }).present().then(()=>{
                this.requireOptions = true;
              })
            }
          } else {
            console.log("product price")
            this.productPrice = this.product.price;
          }

        },
        (err)=>{console.log("err")})
      })
    }


swp_addToCart(event,product)
{
  let selectedOption_count = 0;
  for (let k in this.selectedVariationOptions) 
    if (this.selectedVariationOptions.hasOwnProperty(k)) 
    selectedOption_count++;  

  let totalOption_count= 0;
  for (var index = 0; index < this.product.attributes.length; index++) 
  {
    if(this.product.attributes[index].variation)
    totalOption_count++; 
  }

if(totalOption_count != selectedOption_count && this.requireOptions )
  {
  this.toastCtrl.create({
   message: "Select Product Options",
  duration: 1000,
  showCloseButton: true
 }).present();
 return; 
 }

this.storage.get("cart").then((cartData) => { 

  if (cartData == null || cartData.length == 0) {
    
    cartData =[];

     cartData.push({
       "product": product,
       "qty": 1,
      "amount": product.price,
           });
        console.log(cartData);

        if(this.selectedVariationProduct){
          cartData[0].variation = this.selectedVariationProduct;
          cartData[0].amount = parseFloat(this.selectedVariationProduct.price);
        }
      }

  else {

        let alreadyAdded = false;
        let alreadyAddedIndex = -1;

        for (let i = 0; i < cartData.length; i++){
          if(cartData[i].product.id == product.id){ //Product ID matched
            if(this.productVariations.length > 0){ //Now match variation ID also if it exists
              if(cartData[i].variation.id == this.selectedVariationProduct.id){
                alreadyAdded = true;
                alreadyAddedIndex = i;
                break;
              }
            } else { //product is simple product so variation does not  matter
              alreadyAdded = true;
              alreadyAddedIndex = i;
              break;
            }
          }
        }

        if(alreadyAdded == true){

          if(this.selectedVariationProduct){
            cartData[alreadyAddedIndex].qty = parseFloat(cartData[alreadyAddedIndex].qty) + 1;
            cartData[alreadyAddedIndex].amount = parseFloat(cartData[alreadyAddedIndex].amount) + parseFloat(this.selectedVariationProduct.price);
            cartData[alreadyAddedIndex].variation = this.selectedVariationProduct;
          } else {
            cartData[alreadyAddedIndex].qty = parseFloat(cartData[alreadyAddedIndex].qty) + 1;
            cartData[alreadyAddedIndex].amount = parseFloat(cartData[alreadyAddedIndex].amount) + parseFloat(cartData[alreadyAddedIndex].product.price);
          
          } 
        } else {

          if(this.selectedVariationProduct){
            cartData.push({
              product: product,
              qty: 1,
              amount: parseFloat(this.selectedVariationProduct.price),
              variation: this.selectedVariationProduct
            })
          } else {
            cartData.push({
              product: product,
              qty: 1,
              amount: parseFloat(product.price),          
            })
          }
        }

      }

      this.storage.set("cart", cartData).then(() => {
        console.log("Cart Updated");
        console.log(cartData);
        this.toastCtrl.create({
          message: "Cart Updated",
          duration: 3000
        }).present();

      })

    })
    
    }


addToCart(event,product)
{
  let selectedOption_count = 0;
  for (let k in this.selectedVariationOptions) 
    if (this.selectedVariationOptions.hasOwnProperty(k)) 
    selectedOption_count++;  

  let totalOption_count= 0;
  for (var index = 0; index < this.product.attributes.length; index++) 
  {
    if(this.product.attributes[index].variation)
    totalOption_count++; 
  }

if(totalOption_count != selectedOption_count && this.requireOptions )
  {
  this.toastCtrl.create({
   message: "Select Product Options",
  duration: 1000,
  showCloseButton: true
 }).present();
 return; 
 }

 
 this.storage.get("cart").then((data)=> {

  if(data == null || data.length == 0)
  {
   data = [];

   data.push({   
     "product": product,
     "qty": this.EnteredQty,
     "amount": parseFloat(product.price)
   });

   if(this.selectedVariationProduct){
    data[0].product = this.selectedVariationProduct;
    data[0].amount = parseFloat(this.selectedVariationProduct.price);
  }
  }   
 else {
   let added = 0;

   for (let i = 0 ; i < data.length; i++ )
   {
     if(product.id == data[i].id)
     {
       console.log("product already in cart");

       let qty = data[i].qty;
       data[i].qty = qty + this.EnteredQty ;
       console.log(data[i].amount)
       data[i].amount= parseFloat(product.price) * data[i].qty;
      //data[i].amount = parseFloat(data[i].amount + data[i].price);
       console.log(data[i].amount);
       added = 1;
     }

   }

   if (added == 0)
   {
     console.log("product not found");
     data.push({   //json obj is pushed on storage
       "product": product,
       "qty": 1,
       "amount": parseFloat(product.price)
     });

   }
 }

 this.storage.set("cart", data).then(()=>{
   
   console.log("cart updated");
   console.log(data);
   this.toastCtrl.create({
     message :"Product added to cart",
     duration: 5000
   }).present();
 })

 })


}

swp_increaseQty()
{
  this.EnteredQty =this.EnteredQty + 1;
}

swp_decreaseQty()
{
  this.EnteredQty =this.EnteredQty - 1;
}

}
