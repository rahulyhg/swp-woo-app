import { Component, ViewChild } from '@angular/core';
import { Nav, Platform, App } from 'ionic-angular';
import { StatusBar } from '@ionic-native/status-bar';
import { SplashScreen } from '@ionic-native/splash-screen';
import {Storage} from '@ionic/Storage'

import { HomePage } from '../pages/home/home';
import { CategoriesPage } from '../pages/categories/categories';
import { CartPage } from '../pages/cart/cart';
import { WishlistPage } from '../pages/wishlist/wishlist';
import {SignupPage} from '../pages/signup/signup';
import { SigninPage } from '../pages/signin/signin';
import { MyordersPage } from '../pages/myorders/myorders';

@Component({
  templateUrl: 'app.html'
})
export class MyApp {
  @ViewChild(Nav) nav: Nav;

  rootPage: any = HomePage;

  pages: Array<{title: string, component: any}>;

  constructor(public platform: Platform, 
    public appCtrl: App,
    public storage: Storage,
    public statusBar: StatusBar, 
    public splashScreen: SplashScreen) {
    this.initializeApp();

    // used for an example of ngFor and navigation
    this.pages = [
      { title: 'Home', component: HomePage },
      { title: 'Shop By category', component: CategoriesPage },
      { title: 'My Cart', component: CartPage},
      { title: 'My Wishlist', component: WishlistPage},
      { title: 'Sign in', component: SigninPage},
    ];

  }

  initializeApp() {
    this.platform.ready().then(() => {
      // Okay, so the platform is ready and our plugins are available.
      // Here you can do any higher level native things you might need.
      this.statusBar.styleDefault();
      this.splashScreen.hide();
    });
  }

  openPage(page) {
    // Reset the content nav to have just this page
    // we wouldn't want the back button to show in this scenario
  this.nav.setRoot(page.component);
  //this.nav.push(page.component);
  }



   
}
