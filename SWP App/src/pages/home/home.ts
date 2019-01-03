import { Component } from '@angular/core';
import { NavController } from 'ionic-angular';
import {Http, Headers} from '@angular/http';
import {Storage} from '@ionic/Storage';

//pages
import {DetailCategoryPage} from '../detail-category/detail-category';

//services
import {AuthServiceProvider} from '../../providers/auth-service/auth-service';
import {ObjectToUrlProvider} from '../../providers/object-to-url/object-to-url';

//constants
import { SearchPage } from '../search/search';
import { ProductDetailsPage } from '../product-details/product-details';
import { CartPage } from '../cart/cart';
import {WC_url} from '../../assets/Settings/settings';
import { MoreProductsPage } from '../more-products/more-products';
import { jsonpCallbackContext } from '@angular/common/http/src/module';


@Component({
  selector: 'page-home',
  templateUrl: 'home.html'
})
export class HomePage {
  adminUsername: any;
  adminUserPassword: any;
  token: any;
  products: any[];
  slider: any [];
  categories: any[];
  deals: any[];
  featuredProducts: any[];
  latestProducts: any[];
  displayProducts: any[]=[];
  RestApi: any;
  tempPro: any []=[];


constructor(public navCtrl: NavController,
  public http: Http,
  public storage: Storage,
  public WcAuth: AuthServiceProvider,
  public objToUrl: ObjectToUrlProvider) {
    this.products =[];
    this.slider =[];
    this.categories = [];
    this.featuredProducts =[];
    this.latestProducts =[];
 
    this.RestApi= this.WcAuth.init();

//Get Products
    this.RestApi.getAsync("products").then (
      (data)=>{
          this.products= JSON.parse(data.body)
          console.log(this.products);
              },
      (err)=>{console.log(err)}
          ); 
    



/*
 
this.http.post(WC_url + '/wp-json/jwt-auth/v1/token',data)
   .subscribe(
     response => { 
     this.token = response.json().token;
    
this.storage.set("AdminUser",response.json()).then((admin)=>{
 this.adminUsername= response.json().username;
 this.adminUserPassword = data.password;
 this.token = response.json().token;
  })

    let headers = new Headers();
     headers.append('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    headers.append('Authorization', 'Bearer ' + this.token);

    let options ={headers:headers};
        //console.log(options)
  
    this.http.get(WC_url + '/wp-json/wc/v2/products',options).subscribe(res=>{
         // console.log(res.json());
         // this.products= res.json();
    })

      //},
      //error =>{
       // console.log("token cannot be generated for this user");
      //})
          }) */


this.RestApi.getAsync("products").then (
  (data)=>{
      this.products= JSON.parse(data.body)
      console.log(this.products);
          },
  (err)=>{console.log(err)}
      ); 


      this.swp_getSlider();
      this.swp_getCategories();
      this.swp_getFeaturedProducts();
      this.swp_getDealsOfDay();
    // this.swp_showProducts();

  }


/** Get Current deals and display in slider
   input: token
   Output: Slider Array
   */
  swp_getSlider()
  {

      let param={};
        this.http.get(WC_url + '/wp-json/swp/v1/product/slider',{
          search: this.objToUrl.swp_objectToURL(param)
        }).subscribe((res)=>{
        this.deals = res.json() ; 
        });
      
  /*
    this.RestApi.getAsync("products?per_page=5").then (
      (data)=>{
          this.slider= JSON.parse(data.body)
          console.log(this.slider);
              },
      (err)=>{console.log(err)}
          ); 
    
   /* console.log(this.token);
     let headers = new Headers();
    headers.append('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    headers.append('Authorization', 'Bearer ' + this.token );

    let options ={headers:headers};

    this.http.get(WC_url + '/wp-json/wc/v2/products?per_page=5',options).subscribe(res=>{
    // this.slider = res.json(); 
     console.log(this.slider)
    }) */
 
  }


  /** Get Product Categories
   * Input: token
   * Output: category array
   */
  swp_getCategories(){

    this.RestApi.getAsync("products/categories").then (
      (data)=>{
        
      let temp :any [] = JSON.parse(data.body);
      for (let i =0 ; i< temp.length; i++)
      {
        if(temp[i].parent == 0 && temp[i].swp_cat_hide_on_mobile != '1') 
        {
          this.categories.push(temp[i]);
        }
      } 
      console.log(this.categories);
              },
      (err)=>{console.log(err)}
          ); 
  }


  /** Get deals of the day
   input: token
   Output: Slider Array
   */
  swp_getDealsOfDay()
  {

  let param={};
    this.http.get(WC_url + '/wp-json/swp/v1/product/deals',{
      search: this.objToUrl.swp_objectToURL(param)
    }).subscribe((res)=>{
    this.deals = res.json() ; 
    });
  }


  swp_getFeaturedProducts()
  {

    this.RestApi.getAsync("products?featured=true").then (
      (data)=>{
        let temp: any [] = JSON.parse(data.body);
        for(let i=0; i< temp.length ;i++) 
        this.featuredProducts.push(temp[i]);
              },
      (err)=>{console.log(err)}
          ); 


          this.RestApi.getAsync("products").then (
            (data)=>{
              let temp: any [] = JSON.parse(data.body);
              for(let i=0; i< temp.length ;i++) 
              this.latestProducts[i] = temp[i];
                    },
            (err)=>{console.log(err)}
                ); 

                console.log(this.featuredProducts);
                console.log(this.latestProducts);   


                
              //this.displayProducts =this.featuredProducts.concat(this.latestProducts);
          
               this.displayProducts.push(this.featuredProducts, this.latestProducts)
                console.log(this.displayProducts);

  }

/** Go to detail category page   */    
  swp_OpenDetailCategoryPage(event,category_id){
    this.navCtrl.push(DetailCategoryPage,{category_id:category_id});
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

  /** Go to product details page   */    
  swp_OpenProductDetailsPage(event,product_id){
    this.navCtrl.push(ProductDetailsPage,{product_id:product_id});
  }

 


  swp_OpenMoreProductsPage(event)
  {
    this.navCtrl.push(MoreProductsPage);
  }

  swp_getFooter()
  {
    let param={};
    this.http.get(WC_url + '/wp-json/swp/v1/general-settings/about-us/title',{
      search: this.objToUrl.swp_objectToURL(param)
    }).subscribe((res)=>{
      console.log(res.json());
    })
  }



}
