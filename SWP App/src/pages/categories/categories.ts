import { Component, ViewChild} from '@angular/core';
import { NavController, NavParams, Navbar,Nav} from 'ionic-angular';
import {Storage} from '@ionic/Storage';
import {Http,Headers} from '@angular/http';

//constants
import{WC_url} from '..'
import { DetailCategoryPage } from '../detail-category/detail-category';
import { HomePage } from '../home/home';
import { CartPage } from '../cart/cart';

@Component({
  selector: 'page-categories',
  templateUrl: 'categories.html',
})
export class CategoriesPage {
token :any ;
categories: any [] =[];

  @ViewChild(Navbar) navbar: Navbar;
  @ViewChild(Nav) nav: Nav;

  constructor(public navCtrl: NavController, 
    public navParams: NavParams,
    public storage : Storage,
    public http: Http) {

    this.storage.get("AdminUser").then((admin)=>{
      this.token = admin.token;


      let headers = new Headers();
       headers.append('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
       headers.append('Authorization', 'Bearer ' + this.token );
    
       let options ={headers:headers};
    
       this.http.get(WC_url + '/wp-json/wc/v2/products/categories',options).subscribe(res=>{
        this.categories = res.json(); 
        console.log(this.categories)
       })   
       })
  }

  swp_OpenDetailCategoryPage(event,category_id){
    this.navCtrl.push(DetailCategoryPage,{category_id:category_id});
  }

  swp_OpenCartPage(){
    this.navCtrl.push(CartPage);
  }

}
