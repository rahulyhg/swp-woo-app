import { Component, ComponentFactoryResolver } from '@angular/core';
import { NavController, NavParams } from 'ionic-angular';
import { Http,Headers} from '@angular/http';
import { FormControl } from '@angular/forms';

import 'rxjs/add/operator/debounceTime';
 
import {ProductDetailsPage} from '../../pages/product-details/product-details';

import {Storage} from '@ionic/Storage';
import {WC_url} from '..';


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


  constructor(public navCtrl: NavController, public navParams: NavParams, 
    public http: Http, public storage: Storage) {
    this.searchControl = new FormControl();
    this.products =[];
    this.temp = [];
   

    this.storage.get("AdminUser").then((admin)=>{
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
     
    })
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
}
