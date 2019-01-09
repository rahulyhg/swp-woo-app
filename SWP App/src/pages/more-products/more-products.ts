import { Component } from '@angular/core';
import { NavController, NavParams } from 'ionic-angular';

//provider
import {AuthServiceProvider} from '../../providers/auth-service/auth-service';

//pages
import { ProductDetailsPage } from '../product-details/product-details';
import { CartPage } from '../cart/cart';
import { SearchPage } from '../search/search';


@Component({
  selector: 'page-more-products',
  templateUrl: 'more-products.html',
})
export class MoreProductsPage {

RestApi: any;
products: any [];
featuredProducts: any[];

  constructor(public navCtrl: NavController, 
    public navParams: NavParams,
    public WcAuth: AuthServiceProvider) {
     this.products = [];
     this.featuredProducts=[];

      this.RestApi= this.WcAuth.init();


      this.RestApi.getAsync("products?featured=true").then (
        (data)=>{
          console.log(data.body);
          this.featuredProducts = JSON.parse(data.body);
        //  let temp: any [] = JSON.parse(data.body);
         // for(let i=0; i< temp.length ;i++) 
         // this.featuredProducts.push(temp[i]);

                },
        (err)=>{console.log(err)}
            ); 
console.log(this.featuredProducts);

this.RestApi.getAsync("products").then (
  (data)=>{
     let temp: any = JSON.parse(data.body)
      for(let i=0; i< temp.length; i++ )
      {
        if(temp[i].id != this.featuredProducts[i].id)
        {
          this.products[i] =temp[i];
        }
      }
      console.log(this.products);
          },
  (err)=>{console.log(err)}
      ); 

  }


  swp_OpenProductDetailsPage(event,product_id){
    this.navCtrl.push(ProductDetailsPage,{product_id:product_id});
  }


/** Go to Search page   */   
swp_OpenSearchPage(event){
  this.navCtrl.push(SearchPage)
}

/** Go to cart PAge */
swp_OpenCartPage(event)
{
  this.navCtrl.push(CartPage);
}

}
