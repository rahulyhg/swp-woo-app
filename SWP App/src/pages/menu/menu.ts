import { Component, ViewChild } from '@angular/core';
import { NavController, NavParams,ModalController } from 'ionic-angular';
import{Storage} from '@ionic/Storage'
import { HomePage } from '../home/home';
import {CartPage} from '../cart/cart';
import { MyordersPage } from '../myorders/myorders'
import { CategoriesPage } from '../categories/categories';
import { WishlistPage } from '../wishlist/wishlist';
import { SigninPage } from '../signin/signin';
import { SignupPage } from '../signup/signup';



@Component({
  selector: 'page-menu',
  templateUrl: 'menu.html',
})


export class MenuPage {
 
  homePage: any;
  WooCommerce : any;
  categories: any[];
isloggedin : boolean ;
user: any;

pages: Array<{title: string, component: any, icon: string}>;

  @ViewChild('content') childNavCtrl: NavController; // this for displaying menu on productbycat

  constructor(public navCtrl: NavController, 
    public navParams: NavParams, 
    public storage: Storage,
    public modalCtl: ModalController) {
    this.homePage = HomePage;
    this.categories = [];

    this.user={};

    this.pages = [
    { title: 'Home', component: HomePage ,icon: "home"},
    { title: 'Shop By category', component: CategoriesPage, icon:"alarm"},
    { title: 'Cart', component: CartPage, icon: "cart"},
    { title: 'Wishlist', component: WishlistPage, icon: "heart"},
   ];

  }

openPage(page) {
    // Reset the content nav to have just this page
    // we wouldn't want the back button to show in this scenario
  this.navCtrl.push(page.component);
  //this.nav.push(page.component);
  }

    
//OpenCartPage(){
  //  let modal = this.modalCtl.create(CartPage);
  //modal.present();
//}
    

OpenSigninPage()
{
  this.navCtrl.push(SigninPage);
}
OpenMyOrdersPage(){
      this.navCtrl.push(MyordersPage);
    }
    
OpenLogoutPage()
            {
        
        this.storage.remove('UserLoginInfo').then(()=>{
        this.user ={};
        this.isloggedin =false;

        this.storage.forEach( (value, key, index) => {
         console.log("This is the value", value)
         console.log("from the key", key)
         console.log("Index is", index)
          })
        })
      }
    

    ionViewDidEnter()
    {
    this.storage.ready().then(()=>
    {
     
      this.storage.get('UserLoginInfo').then((UserLoginInfo)=>{

        if(UserLoginInfo != null)
        {
          console.log("logged in");
        this.user =UserLoginInfo.username;
        console.log(this.user);
          this.isloggedin =true;
          
        }
        else
        {
          console.log("not logged in");
          this.user = {};
          this.isloggedin = false;
        }
       
      })
    })
    
    }

   
}
