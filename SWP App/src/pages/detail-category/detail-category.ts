import { Component } from '@angular/core';
import {  NavController, NavParams } from 'ionic-angular';
import {Storage} from '@ionic/Storage';
import {Http, Headers} from '@angular/http';

//constants
import{WC_url} from '..';
import { ProductDetailsPage } from '../product-details/product-details';

@Component({
  selector: 'page-detail-category',
  templateUrl: 'detail-category.html',
})
export class DetailCategoryPage {
category_id: any;
token: any;
categoryDetails: any [];
categoryProducts: any [];


  constructor(public navCtrl: NavController, 
    public navParams: NavParams,
    public storage: Storage,
    public http: Http) {

    this.categoryDetails =[];
    this.categoryProducts =[];

    this.category_id = navParams.get('category_id');
    console.log(this.category_id);


    this.storage.get("AdminUser").then((admin)=>{
      this.token = admin.token;


      let headers = new Headers();
       headers.append('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
       headers.append('Authorization', 'Bearer ' + this.token );
    
       let options ={headers:headers};
    
      // Category details
       this.http.get(WC_url + '/wp-json/wc/v2/products/categories/'+ this.category_id,options).subscribe(res=>{
        this.categoryDetails =res.json();
        console.log(this.categoryDetails);
      })


       // All the Products by Category
       this.http.get(WC_url + '/wp-json/wc/v2/products?category='+ this.category_id,options).subscribe(res=>{
        this.categoryProducts= res.json();
         console.log(this.categoryProducts);  
         },
           (err)=>{console.log("err")
         })
       })
    
  }

   /** Go to product details page   */    
   swp_OpenProductDetailsPage(event,product_id){
    this.navCtrl.push(ProductDetailsPage,{product_id:product_id});
  }


 
}
