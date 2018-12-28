import { Component, ViewChild} from '@angular/core';
import { NavController, NavParams} from 'ionic-angular';
import {Storage} from '@ionic/Storage';
import {Http,Headers} from '@angular/http';
import {AuthServiceProvider} from '../../providers/auth-service/auth-service';

//constants
import{WC_url} from '../../assets/Settings/settings';
import { DetailCategoryPage } from '../detail-category/detail-category';
import { CartPage } from '../cart/cart';

@Component({
  selector: 'page-categories',
  templateUrl: 'categories.html',
})
export class CategoriesPage {
token :any ;
categories: any [] =[];
RestApi: any;

  constructor(public navCtrl: NavController, 
    public navParams: NavParams,
    public storage : Storage,
    public http: Http,
    public WcAuth: AuthServiceProvider) {

      this.RestApi= this.WcAuth.init();

      this.RestApi.getAsync("products/categories").then (
        (data)=>{
          
        let temp :any [] = JSON.parse(data.body);
        for (let i =0 ; i< temp.length; i++)
        {
          if(temp[i].parent == 0) 
          {
            this.categories.push(temp[i]);
          }
        } 
        console.log(this.categories);
                },
        (err)=>{console.log(err)}
            ); 
/*
    this.storage.get("AdminUser").then((admin)=>{
      this.token = admin.token;

      let headers = new Headers();
       headers.append('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
       headers.append('Authorization', 'Bearer ' + this.token );
    
       let options ={headers:headers};
    
       this.http.get(WC_url + '/wp-json/wc/v2/products/categories',options).subscribe(res=>{
       // this.categories = res.json(); 
       // console.log(this.categories)
       })   
       }) */
  }

  swp_OpenDetailCategoryPage(event,category_id){
    this.navCtrl.push(DetailCategoryPage,{category_id:category_id});
  }

  swp_OpenCartPage(event){
    this.navCtrl.push(CartPage);
  }

}
