import { Component, ErrorHandler, ViewChild } from '@angular/core';
import { NavController, Navbar, NavParams,AlertController, ToastController, Toast } from 'ionic-angular';
import {Storage} from '@ionic/Storage';
import { Http, Headers } from '@angular/http';

//service
import {AuthServiceProvider} from '../../providers/auth-service/auth-service';


//constants
import {WC_url} from '../../assets/Settings/settings';
import { CartPage } from '../cart/cart';
import { WishlistPage } from '../wishlist/wishlist';
import { MenuPage } from '../menu/menu';



@Component({
  selector: 'page-product-details',
  templateUrl: 'product-details.html',
})
export class ProductDetailsPage {
  @ViewChild(Navbar) navBar: Navbar;

  RestApi: any;
  product_id: any;
  product: any;
  similarProducts: any [] =[];
  reviews: any[] = [];
  productVariations: any;
  selectedVariationOptions: any ={};
  selectedVariationProduct: any ={};
  requireOptions: boolean = true;
  isVariation: boolean = false;
  token: any;
  cartProduct: any;
  wishlistPage: WishlistPage;
  menuPage: MenuPage;
  
  addToFav: boolean;
 EnteredQty: any;
  totalDisplayed: any;
  productCategory: any ;
 wishlistArray: any [];

 
  constructor(public navCtrl: NavController,
    public WcAuth: AuthServiceProvider,
    public navParams: NavParams,
    public storage: Storage,
    public http: Http,
    public toastCtrl: ToastController,
    public alertCtrl : AlertController) {
      this.product ={};
      this.productVariations={};
      this.EnteredQty = ""
      this.totalDisplayed ="";
      this.productCategory ="";
      this.cartProduct ={};
     
this.RestApi = this.WcAuth.init();

this.product_id = navParams.get('product_id');

// All the Products details based on id
this.RestApi.getAsync("products/"+ this.product_id).then (
  (data)=>{
      this.product= JSON.parse(data.body);
      this.productCategory = this.product.categories[0].id;
      console.log(this.product);
          },
  (err)=>{console.log(err)}
      ); 

      this.EnteredQty = 1;
      this.totalDisplayed = this.product.price * this.EnteredQty;

//simillar products
this.RestApi.getAsync('products?category='+ this.productCategory).then (
  (data)=>{
      this.similarProducts= JSON.parse(data.body);
      console.log(this.similarProducts);
          },
  (err)=>{console.log(err)}
      ); 


//Product reviews   
this.RestApi.getAsync('products/'+ this.product_id + '/reviews').then (
  (data)=>{
      this.reviews= JSON.parse(data.body);
      console.log(this.reviews);
          },
  (err)=>{console.log(err)}
      ); 

this.storage.get("wishlist").then((data)=>{
  console.log(data);
  this.wishlistArray = data;
  console.log(this.wishlistArray);

  if(data==null || data == undefined || data.lenght == 0)
    this.wishlistArray = [];
  else{
  for(let i=0; i<data.lenght ; i ++)
  {
    this.wishlistArray.push(data[i]);
  }
}
})
}

swp_addToFav(event,product)
{
  let inWishlist: boolean = false;
  let index: any;
  for(let i=0 ; i<this.wishlistArray.length ;i ++)
  {
    console.log(this.wishlistArray[i].id,product.id )
    if(this.wishlistArray[i].id == product.id )
    {
      inWishlist = true;
      index=i;
      break;
    }
    else inWishlist=false
  }

 if(inWishlist == true)
   {
    this.wishlistArray.splice(index, 1);
   
    this.storage.set("wishlist", this.wishlistArray).then(() => {
      console.log("removed");
      this.addToFav=false;
     
    });
   }
    else{
      let data :any=product;
      this.wishlistArray.push(data);
      console.log(this.wishlistArray);
      this.storage.set("wishlist", this.wishlistArray).then(() => {  
         console.log("added")
         this.addToFav = true;
       });


       this.storage.forEach( (value, key, index) => {
        console.log("This is the value", value)
        console.log("from the key", key)
        console.log("Index is", index)
      })
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

  if(totalOption_count != selectedOption_count){
    this.requireOptions = true;
    return;
  } else {
    this.requireOptions = false;
  }

  // All the Products variations
  this.RestApi.getAsync('products/'+ this.product_id + '/variations/').then (
    (data)=>{

      this.productVariations  = JSON.parse(data.body);
     
      console.log( this.productVariations)

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
             this.totalDisplayed = parseInt(this.selectedVariationProduct.price)*this.EnteredQty;
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
            this.selectedVariationProduct = this.productVariations[i];
            this.totalDisplayed = parseInt(this.selectedVariationProduct.price)*this.EnteredQty;
          
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
     this.totalDisplayed = parseInt(this.selectedVariationProduct.price)*this.EnteredQty;
      }  
    },
    (err)=>{console.log(err)}
        ); 


   
 // this.storage.get("AdminUser").then((admin)=>{
   //   this.token = admin.token;

     // let headers = new Headers();
       //headers.append('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
      // headers.append('Authorization', 'Bearer ' + this.token );
    
   //    let options ={headers:headers};

        // All the Products variations
        //this.http.get(WC_url + '/wp-json/wc/v2/products/'+ this.product_id + '/variations/',options).subscribe(res=>{
     //   this.productVariations = res.json(); 
          
       // let i = 0, matchFailed = false;

         // if (this.productVariations.length > 0) {
            
           // for (i = 0; i < this.productVariations.length; i++) {
             // matchFailed = false; 
             // let key: string = "";
      
     //         for (let j = 0; j < this.productVariations[i].attributes.length; j++) {
       //         key = this.productVariations[i].attributes[j].name;
         //       console.log(this.selectedVariationOptions[key].toLowerCase()+ " " + this.productVariations[i].attributes[j].option.toLowerCase())
      
           //     if (this.selectedVariationOptions[key].toLowerCase() == this.productVariations[i].attributes[j].option.toLowerCase()) {
                  //Do nothing 
             //     console.log("match found");
               //   this.totalDisplayed = parseInt(this.selectedVariationProduct.price);
                 // } 
                //else {
                 // console.log(matchFailed)
                //  matchFailed = true;
                //  break;
               // }
             // }
      
    //          if (matchFailed) {
      //          continue;
        //      } else {
          //      this.selectedVariationProduct = this.productVariations[i];
            //    this.totalDisplayed = parseInt(this.selectedVariationProduct.price);
              
              //  break;
      
              //}
      
   //         }
      
     //     if(matchFailed == true){
       //       this.toastCtrl.create({
         //       message: "No Such Product Found",
           //     duration: 3000
             // }).present().then(()=>{
            //    this.requireOptions = true;
             // })
           // }
         // } else {
           // console.log("product price")
  //       this.totalDisplayed = parseInt(this.selectedVariationProduct.price);
    //      }

      //  },
      //  (err)=>{console.log("err")})
     // })
    }


swp_increaseQty()
{

  this.EnteredQty = this.EnteredQty + 1;
  if(this.product.variations > 0)
  {
    if(this.selectedVariationProduct)
    {
      this.totalDisplayed = this.selectedVariationProduct.price * this.EnteredQty;
    }
  }
  else 
  this.totalDisplayed = this.product.price * this.EnteredQty;
  
}

swp_decreaseQty()
{
  let initTotal =this.totalDisplayed;
  if(this.EnteredQty != 1){
  this.EnteredQty = this.EnteredQty - 1;
  if(this.product.variations > 0)
  {
    if(this.selectedVariationProduct)
    {
      this.totalDisplayed = initTotal - this.selectedVariationProduct.price;
    }
  }
  else
  this.totalDisplayed = initTotal - this.product.price;
  
  }
}

swp_OpenCartPage(event)
{
  this.navCtrl.push(CartPage);
}

swp_goHome() {
  this.navCtrl.popToRoot();
}

swp_addToCart(event,product){

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
  this.totalDisplayed = 1;
  this.toastCtrl.create({
   message: "Select Product Options",
  duration: 1000,
  showCloseButton: true
 }).present();
 return; 
 }

 this.storage.get( "cart" ).then( data => {

   if(data == undefined || data == null || data.lenght==0)
   { 
    
   // data = [{}];
     data = [];
  if(product.variations == 0)
    {
    data.push({
      'product': product,
      'qty': this.EnteredQty,
      'amount': parseInt(product.price) * Number(this.EnteredQty),
     });
    }     
     else
      {
      if(this.selectedVariationProduct){
       data.push({
      'product': this.selectedVariationProduct,
      'qty': this.EnteredQty,
      'amount': parseInt(this.selectedVariationProduct.price)* Number(this.EnteredQty),
      'parentProduct': product,
      
     });   
    }
  }
}
   else{
     
      let AlreadyAdded: boolean = false;
      
        for(let i=0 ;i < data.length ; i ++ )
        { 
          
          if(data[i].product.id == product.id)
          {
            console.log("product already in cart Do you want to update");

            this.alertCtrl.create({
              title: "Update Cart?",
             message: "Product is already in cart.Do you want to update it?",
             buttons: [
              {
                text: 'Update',
                handler: () => {
                data[i].qty =this.EnteredQty;
                data[i].amount= parseInt(product.price)* Number(this.EnteredQty);
                }
              },
              {
                text: 'Cancel',
                role: 'cancel',
              }
            ]
            }).present();
      
            //data[i].qty =this.EnteredQty;
            // data[i].amount= parseInt(product.price)* Number(this.EnteredQty);
        
            AlreadyAdded = true;
            break;
          }
          else if(this.productVariations)
          {
            
            if(data[i].product.id == this.selectedVariationProduct.id)
            {
           
              console.log("Product variation already in cart Do you want to update");

              this.alertCtrl.create({
                title: "Update Cart?",
               message: "Product is already in cart.Do you want to update it?",
               buttons: [
                {
                  text: 'Update',
                  handler: () => {
                    data[i].qty =this.EnteredQty;
                    data[i].amount= parseInt(this.selectedVariationProduct.price)* Number(this.EnteredQty)  
                   }
                },
                {
                  text: 'Cancel',
                  role: 'cancel',
                }
              ]
              }).present();


              //data[i].qty =this.EnteredQty;
              //data[i].amount= Number(this.selectedVariationProduct.price)* Number(this.EnteredQty)  
             
              AlreadyAdded = true;
              break;
            }
          }
        
         }
          if(AlreadyAdded == false)
        {            
          if(product.variations == 0)
          {
            data.push({
              'product': product,
              'qty': this.EnteredQty,
              'amount':parseInt(product.price) * Number(this.EnteredQty),
              
             });
          }
        else{            
              if(this.selectedVariationProduct)
              {
              data.push({
                'product': this.selectedVariationProduct,
                'qty': this.EnteredQty,
                'amount': parseInt(this.selectedVariationProduct.price)* Number(this.EnteredQty),
                'parentProduct': product,
               });     
          }
        }
          }
   }
       this.storage.set("cart",data).then(()=>{
    console.log("cart updated");
    console.log(data);
       this.toastCtrl.create({
       message :"Product added to cart",
        duration: 5000
       }).present();
      })
})
}

ionViewWillEnter()
{
  console.log("will enter")
  this.storage.ready().then(()=>{
  this.storage.get("wishlist").then((data)=>{  
    if(data==null || data == undefined || data.lenght == 0)
     this.addToFav = false;
    else{
    for(let i=0; i<data.lenght ; i ++)
    {
      if(data.id == this.product.id)
      {
        this.addToFav = true;
      }
      else this.addToFav = false;
    }
  }
  })
})
}

swp_goBack()
{
  if(this.navCtrl.getPrevious() && this.navCtrl.getPrevious().component == this.wishlistPage)
  this.navCtrl.push(MenuPage);
		else 
this.navCtrl.pop();
}


ionViewDidLoad() {
  this.setBackButtonAction()
}

setBackButtonAction(){
 this.navBar.backButtonClick = () => {
  if(this.navCtrl.getPrevious() && this.navCtrl.getPrevious().component == this.wishlistPage)
 // this.navCtrl.push(MenuPage);
 this.navCtrl.popToRoot();
		else 
  this.navCtrl.pop();
  };
}

}

