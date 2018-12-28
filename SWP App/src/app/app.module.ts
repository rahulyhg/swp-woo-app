import { BrowserModule } from '@angular/platform-browser';
import { ErrorHandler, NgModule } from '@angular/core';
import {ReactiveFormsModule} from "@angular/forms";
import { IonicApp, IonicErrorHandler, IonicModule } from 'ionic-angular';
import {Http,Headers} from '@angular/http';
import {HttpModule} from '@angular/http';

import { HttpClient, HttpClientModule ,HttpHeaders} from '@angular/common/http';
import { TranslateLoader, TranslateModule } from '@ngx-translate/core';
import { TranslateHttpLoader } from '@ngx-translate/http-loader';
import {IonicStorageModule} from '@ionic/Storage';



import { StatusBar } from '@ionic-native/status-bar';
import { SplashScreen } from '@ionic-native/splash-screen';

import { MyApp } from './app.component';
import { HomePage } from '../pages/home/home';
import { CategoriesPage } from '../pages/categories/categories';
import { DetailCategoryPage } from '../pages/detail-category/detail-category';
import { SearchPage } from '../pages/search/search';
import { ProductDetailsPage } from '../pages/product-details/product-details';
import { CartPage } from '../pages/cart/cart';
import { CheckoutPage } from '../pages/checkout/checkout';
import { WishlistPage } from '../pages/wishlist/wishlist';
import { SignupPage } from '../pages/signup/signup';
import {SigninPage} from '../pages/signin/signin';
import { MyordersPage } from '../pages/myorders/myorders';
import { MenuPage } from '../pages/menu/menu';
import { AuthServiceProvider } from '../providers/auth-service/auth-service';
import { ValidationProvider } from '../providers/validation/validation';
import { ExpandableComponent } from '../components/expandable/expandable';
import { AddressPage } from '../pages/address/address';
import { InAppBrowser } from '@ionic-native/in-app-browser';
import { ObjectToUrlProvider } from '../providers/object-to-url/object-to-url';





@NgModule({
  declarations: [
    MyApp,
    HomePage,
    CategoriesPage,
    DetailCategoryPage,
    SearchPage,
    ProductDetailsPage,
    CartPage,
    CheckoutPage,
    WishlistPage,
    SignupPage,
    SigninPage,
    MyordersPage,
    MenuPage,
    ExpandableComponent,
    AddressPage
   
   
  ],
  imports: [
    BrowserModule,
    HttpModule,
    IonicModule.forRoot(MyApp),
    IonicStorageModule.forRoot(),
    ReactiveFormsModule
  ],
  bootstrap: [IonicApp],
  entryComponents: [
    MyApp,
    HomePage,
    CategoriesPage,
    DetailCategoryPage,
    SearchPage,
    ProductDetailsPage,
    CartPage,
    CheckoutPage,
    WishlistPage,
    SignupPage,
    SigninPage,
    MyordersPage,
    MenuPage,
    ExpandableComponent,
    AddressPage
 
  ],
  providers: [
    StatusBar,
    SplashScreen,
    {provide: ErrorHandler, useClass: IonicErrorHandler},
    HttpClientModule,
    AuthServiceProvider,
    ValidationProvider,
    InAppBrowser,
    ObjectToUrlProvider
  ]
})
export class AppModule {}
