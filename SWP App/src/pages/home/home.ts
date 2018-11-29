import { Component } from '@angular/core';
import { NavController } from 'ionic-angular';
import {Http, Headers} from '@angular/http';
import {Storage} from '@ionic/Storage';

//pages
import {DetailCategoryPage} from '../detail-category/detail-category';

//services


//constants
import {WC_url} from '..';
import { SearchPage } from '../search/search';
import { ProductDetailsPage } from '../product-details/product-details';
import { CartPage } from '../cart/cart';


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


constructor(public navCtrl: NavController,
  public http: Http,
  public storage: Storage) {
    this.products =[];
    this.slider =[];
    this.categories = [];


    

    this.http.post(WC_url + '/wp-json/jwt-auth/v1/token',data)
   .subscribe(
     response => { 
      console.log(response.json());
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
        console.log(options)
  
        this.http.get(WC_url + '/wp-json/wc/v2/products',options).subscribe(res=>{
          console.log(res.json());
          this.products= res.json();
        })

        this.swp_getSlider();
        this.swp_getCategories();
        this.swp_getFeaturedPRoducts();
      },
      error =>{
        console.log("token cannot be generated for this user");
      })
  }


/** Get Current deals and display in slider
   input: token
   Output: Slider Array
   */
  swp_getSlider()
  {
    console.log(this.token);
     let headers = new Headers();
    headers.append('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    headers.append('Authorization', 'Bearer ' + this.token );

    let options ={headers:headers};

    this.http.get(WC_url + '/wp-json/wc/v2/products?per_page=5',options).subscribe(res=>{
     this.slider = res.json(); 
     console.log(this.slider)
    })
 
  }


  /** Get Product Categories
   * Input: token
   * Output: category array
   */
  swp_getCategories(){

    let headers = new Headers();
    headers.append('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    headers.append('Authorization', 'Bearer ' + this.token);

    let options ={headers:headers};

    this.http.get(WC_url + '/wp-json/wc/v2/products/categories',options).subscribe(res=>{
     
      let temp :any [] = res.json();
      for (let i =0 ; i< temp.length; i++)
      {
        if(temp[i].parent == 0) 
        {
          this.categories.push(temp[i]);
        }
      } 
      console.log(this.categories);
      },
        (err)=>{console.log("err")
      })
  }


  /** Get deals of the day
   input: token
   Output: Slider Array
   */
  swp_getDealsOfDay()
  {

    this.deals=[];
    
    // console.log(this.token);
    //let headers = new Headers();
    //headers.append('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    //headers.append('Authorization', 'Bearer ' + this.token );

   //    let options ={headers:headers};

  //  this.http.get(WC_url + '/wp-json/wc/v2/products?per_page=5',options).subscribe(res=>{
    // this.slider = res.json(); 
     //console.log(this.slider)
    //})
 
  }


  swp_getFeaturedPRoducts()
  {
  console.log(this.token);
  let headers = new Headers();
   headers.append('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
   headers.append('Authorization', 'Bearer ' + this.token );

   let options ={headers:headers};

   this.http.get(WC_url + '/wp-json/wc/v2/products?per_page=4',options).subscribe(res=>{
    this.featuredProducts = res.json(); 
    console.log(this.featuredProducts)
   })

  }


/** Go to detail category page   */    
  swp_OpenDetailCategoryPage(event,category_id){
    this.navCtrl.push(DetailCategoryPage,{category_id:category_id});
  }


/** Go to Search page   */   
  swp_OpenSearchPage(){
    this.navCtrl.push(SearchPage)
  }

  /** Go to cart PAge */
  swp_OpenCartPage()
  {
    this.navCtrl.push(CartPage);
  }

  /** Go to product details page   */    
  swp_OpenProductDetailsPage(event,product_id){
    this.navCtrl.push(ProductDetailsPage,{product_id:product_id});
  }

 
}
