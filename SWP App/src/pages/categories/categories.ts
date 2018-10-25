import { Component, ViewChild} from '@angular/core';
import { NavController, NavParams, Navbar} from 'ionic-angular';

@Component({
  selector: 'page-categories',
  templateUrl: 'categories.html',
})
export class CategoriesPage {

  @ViewChild(Navbar) navbar: Navbar;

  constructor(public navCtrl: NavController, public navParams: NavParams) {
  }

  BackButton(){
   // this.navbar.backButtonClick = () => {
    this.navCtrl.pop()
     //}
    }
}
