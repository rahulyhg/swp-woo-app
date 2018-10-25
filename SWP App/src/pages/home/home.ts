import { Component } from '@angular/core';
import { NavController } from 'ionic-angular';
import {Http, Headers} from '@angular/http';

//pages
import {DetailCategoryPage} from '../detail-category/detail-category';

//services


//constants
import {WC_url} from '..';
import { SearchPage } from '../search/search';


@Component({
  selector: 'page-home',
  templateUrl: 'home.html'
})
export class HomePage {
  token: any;
  products: any[];
  slider: any [];
  categories: any[];

constructor(public navCtrl: NavController,
  public http: Http) {
    this.products =[];
    this.slider =[];
    this.categories = [];


    let data ={username: 'abc',password: 'abctest'}

    this.http.post(WC_url + '/wp-json/jwt-auth/v1/token',data)
   .subscribe(
     response => { 
      console.log(response.json());
      this.token = response.json().token;
    

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
   * Output: slider
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

/** Go to detail category page   */    
  swp_OpenDetailCategoryPage(event,id){
    this.navCtrl.push(DetailCategoryPage,{id:id});
  }


/** Go to Search page   */   
  swp_OpenSearchPage(){
    this.navCtrl.push(SearchPage)
  }

  ionViewDidLoad() {
    this.swp_getSlider();
    this.swp_getCategories();
}
}
