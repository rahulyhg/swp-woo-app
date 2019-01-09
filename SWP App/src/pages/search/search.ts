import { Component} from '@angular/core';
import { NavController, NavParams } from 'ionic-angular';
import { FormControl } from '@angular/forms';
import {Storage} from '@ionic/Storage';


import 'rxjs/add/operator/debounceTime';

//Pages 
import {ProductDetailsPage} from '../../pages/product-details/product-details';
import {CartPage} from '../../pages/cart/cart';

//providers
import {AuthServiceProvider} from '../../providers/auth-service/auth-service';


@Component({
  selector: 'page-search',
  templateUrl: 'search.html',
})
export class SearchPage {
 
  searchControl: FormControl;
  searchTerm: string = '';
    products: any[] = [];
    token: any;
    searching: any = false;
    temp: any [] = [] ;
    RestApi: any;


  constructor(public navCtrl: NavController,
     public navParams: NavParams, 
    public storage: Storage,
    public WcAuth: AuthServiceProvider) {
    this.RestApi= this.WcAuth.init();

    this.searchControl = new FormControl();
    this.products =[];
    this.temp = [];
   

   /* this.storage.get("AdminUser").then((admin)=>{
      this.token = admin.token;

      let headers = new Headers();
       headers.append('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
       headers.append('Authorization', 'Bearer ' + this.token );
    
       let options ={headers:headers};
    
      // All products
      this.http.get(WC_url + '/wp-json/wc/v2/products',options).subscribe(res=>{
       this.products = this.json2array(res.json())
       this.temp =this.products;
         
      })
     
    })*/

    //Get Products
    this.RestApi.getAsync("products").then (
      (data)=>{
        //  this.products= JSON.parse(data.body
this.products = this.json2array(data);
this.temp = this.products;
          console.log(this.products);
              },
      (err)=>{console.log(err)}
          ); 
  }
  
//filter Product list based on term passed
  swp_filterItems(Term){        
      return this.products.filter((item) => {
          return item.name.toString().toLowerCase().indexOf(Term.toLowerCase()) > -1;
      });   
    }



ionViewDidLoad() {
    this.swp_setFilteredItems();
    this.searchControl.valueChanges.debounceTime(700).subscribe(search => {
      this.searching = false;
      this.swp_setFilteredItems();

  });
}

onSearchInput(){
  this.searching = true;
}

//sets filter product array
swp_setFilteredItems() {
  this.products =this.temp;
  this.products = this.swp_filterItems(this.searchTerm);
  console.log(this.products);
}

json2array(json){
  var arrayout = [];
  var keys = Object.keys(json);
  keys.forEach(function(key){
      arrayout.push(json[key]);
  });
  return arrayout;
}

swp_OpenProductDetailsPage(event,product_id){
  this.navCtrl.push(ProductDetailsPage,{product_id:product_id});
}

swp_OpenCartPage(event)
{
  this.navCtrl.push(CartPage);
}

 /** Infinte Scroll content for more products */
 swp_doInfinite(infiniteScroll){

  setTimeout(() => {
    if(this.products.length)
    for (let i = 0; i < this.products.length; i++) {
  // this.featuredProducts.push( this.items.length );
    }

    console.log('Async operation has ended');
 //   infiniteScroll.complete();
  }, 500);
}
}
