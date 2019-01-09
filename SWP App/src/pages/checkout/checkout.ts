import { Component, Input, ViewChild, ElementRef, Renderer } from '@angular/core';
import {  NavController, NavParams, Platform} from 'ionic-angular';
import {Storage} from '@ionic/Storage'; 
import { InAppBrowser , InAppBrowserOptions } from '@ionic-native/in-app-browser';
import { Http, Headers} from '@angular/http'
import {HttpParams} from "@angular/common/http";



//providers
import {AuthServiceProvider} from '../../providers/auth-service/auth-service';
import {ObjectToUrlProvider} from '../../providers/object-to-url/object-to-url'

//pages
import{ WC_url} from '../../assets/Settings/settings'
import {ExpandableComponent} from '../../components/expandable/expandable'
import { CartPage } from '../cart/cart';
import {ThankyouPage} from '../thankyou/thankyou'
import { AddressPage } from '../address/address';




@Component({
  selector: 'page-checkout',
  templateUrl: 'checkout.html',
})
export class CheckoutPage {
    RestApi: any;
    AddressPage = AddressPage;
    items: any = [];
    itemExpandHeight: number = 100;
    user: any;
    newOrder: any;
   
    shippingMethods: any [];
    shippingMethod: any;
   

    
    paymentMethods: any [];
    paymentMethod: any;
   
    cartItems: any [] = [];
    total: any;
    postOrderData: any;


    options : InAppBrowserOptions = {
      location : 'yes',//Or 'no' 
      hidden : 'no', //Or  'yes'
      clearcache : 'yes',
      clearsessioncache : 'yes',
      zoom : 'yes',//Android only ,shows browser zoom controls 
      hardwareback : 'yes',
      mediaPlaybackRequiresUserAction : 'no',
      shouldPauseOnSuspend : 'no', //Android only 
      closebuttoncaption : 'Close', //iOS only
      disallowoverscroll : 'no', //iOS only 
      toolbar : 'yes', //iOS only 
      enableViewportScale : 'no', //iOS only 
      allowInlineMediaPlayback : 'no',//iOS only 
      presentationstyle : 'pagesheet',//iOS only 
      fullscreen : 'yes'//Windows only    
  };
  


  constructor(public navCtrl: NavController, public navParams: NavParams,
    public storage: Storage,
    public renderer: Renderer,
    public WcAuth: AuthServiceProvider,
    public platform: Platform,
    public inappb : InAppBrowser,
    public http: Http,
    public ObjToUrl: ObjectToUrlProvider) {

     this.RestApi = this.WcAuth.init();
      this.user = {};
  
   
    if(! this.newOrder){this.newOrder={}}
    this.newOrder.billing = {};
    this.newOrder.shipping = {};

     this.shippingMethods = [];
     this.paymentMethods = [];

    
console.log(this.user ,this.newOrder );




      this.swp_getShippingMethods();
      this.swp_getPaymentMethods();
     
    
          //sections to display
    this.items = [
      {expanded: false, title: "Address", Data: this.user},
      {expanded: false, title: "Shipping Methods", Data: this.shippingMethods},
      {expanded: false , title: "Payment Methods", Data : this.paymentMethods},
  ];

    }

    swp_getShippingMethods()
    {  

   this.RestApi.getAsync('shipping_methods').then(
    (data)=>{
      console.log(JSON.parse(data.body));

      let temp:any=JSON.parse(data.body);
      let temp2: any;
      for(let i=0; i<temp.length; i++)
      { 
      //   temp2[i] = [{"id": temp[i].id,"title":temp[i].title }]; 
         this.shippingMethods.push({"id": temp[i].id,"title":temp[i].title });
      }
  //  this.shippingMethods.push(temp2);
  
},
(err)=>{console.log(err)})
console.log(this.shippingMethods);
    }


    swp_getPaymentMethods()
    {
      this.RestApi.getAsync("payment_gateways?enabled='true'").then(
        (data) => {
          console.log(JSON.parse(data.body))
      let temp :any []= JSON.parse(data.body);
      let temp2: any[];
      for (let i =0 ; i < temp.length; i++)
        {
          this.paymentMethods.push({"method_id": temp[i].id, "method_title": temp[i].title })
      
       }
    
     },
       (err)=>{console.log("err")
     })
    console.log(this.paymentMethods);
    }

    swp_getCartData(){
      this.storage.get("cart").then((cart)=>{
        console.log(cart);
      })

    }
    expandItem(item){
 
      this.items.map((listItem) => {

          if(item == listItem){
              listItem.expanded = !listItem.expanded;
          } else {
              listItem.expanded = false;
          }
          return listItem;
      });

  }

  swp_backToCart(){
      let currentIndex = this.navCtrl.getActive().index;
      console.log(currentIndex);
      this.navCtrl.push(CartPage).then(() => {
            this.navCtrl.remove(currentIndex);
    });
    
    
  }
swp_placeOrder()
{
    let orderItems: any[]=[] ;
    let data: any = {};
    let total: any='';
    let paymentData: any = {};
    let shippingData: any ={};
  
    this.shippingMethods.forEach((element,index)=>{
      if(element.id == this.shippingMethod){
        shippingData = element;
      }
    });
   
    this.paymentMethods.forEach((element, index) => {
      if (element.method_id == this.paymentMethod) {
      paymentData = element;
       }
      });

   data = {
       payment_method: paymentData.method_id,
       payment_method_title: paymentData.method_title,
      // set_paid: false,
      // status: 'processing',
      
        billing: this.newOrder.billing,
        shipping: this.newOrder.shipping,
        shipping_lines: [
          {
            method_id: shippingData.id,
            method_title:shippingData.title,
      //      total:JSON.stringify(this.total),
          }
        ],
    
      };

   if(this.paymentMethod == "paypal")
   {
  
   }
   else {
  
   this.storage.get("cart").then((cart) => {
    // cart.forEach((element, index) => {
      //    orderItems.push({ product_id: element.product.id, quantity: element.qty });
        //  this.total = this.total + (element.product.price * element.qty);
          //console.log(this.total);
      //});
      //console.log(this.total);
      //data.line_items = orderItems;
      
      let orderData: any = {};
      orderData.order = data;
       console.log(orderData.order); 
  
  
    this.RestApi.postAsync('orders', orderData.order).then(
        (response) => {
               this.postOrderData = JSON.parse(JSON.stringify(response.body));
  
               let Orderid = this.postOrderData.id;
               let OrderKey = this.postOrderData.order_key;
               let target = "_self";
               let checkoutUrl = WC_url +'/check-out/order-pay/'+ Orderid +'/?key='+ OrderKey;
       
       
               if (this.platform.is('cordova')) {
                this.platform.ready().then(() => {
                      let openCheckout = this.inappb.create(checkoutUrl, target= '_blank','location=no,closebuttoncaption=Close,hardwareback=yes,footer=yes');
                     openCheckout.on('loadstart').subscribe(
                        res => {
                          console.log(res);			
                
                if (res.url == WC_url + '/check-out/order-received/?key'){
                this.navCtrl.push(ThankyouPage,{orderid:Orderid} ).then(
                  () => {
                  openCheckout.close();    
                  this.storage.remove('cart');
                });
              } 
              else{

                  }
            },
          err=>{
            console.log(err);
          });
            openCheckout.on('loaderror').subscribe(res => {
              console.log(res);
              openCheckout.close();
              console.log("error");
            },
err=>{
  console.log(err);
}
          );
          }); 
        }  
       },
    (err)=>{ console.log(err)})
     })
}

let temp: any ={
"products":[{"product_id" : 2514,"quantity" : 1}],
"country" : ["IN"],
"states" : ["MH"],
"postcode" : [411057]
};

temp = this.ObjToUrl.swp_objectToURL(temp);
   

//https://getall
//?products=%5B%7B%22product_id%22:9945,%22quantity%22:1%7D%5D&country=IN&states=MH&postcode=411057
//?products=%5B%7B%22product_id%22:2514,%22quantity%22:1%7D%5D&country=IN&states=MH&postcode=411057

const params = new HttpParams()
.set('country', "IN")
.set('states', "MH")
.set('postcode', "411057");
this.http.get("all",temp).subscribe(data=>{
  console.log(data)
})

  }

  ionViewCanEnter(){
    this.storage.ready().then(()=>{
      let temp : any;
     
     this.storage.get("UserLoginInfo").then(
       (data) => {
       temp = data;
       
       for(let i=0;i<temp.length; i++)
       {
        this.user[i] = JSON.parse(JSON.stringify(temp[i]));   
        this.newOrder.billing = this.user[i].billing ;
       this.newOrder.shipping = this.user[i].shipping;
       }
      
     },
          (err)=>{console.log("err")
        })
    })


  
  }
    
}
