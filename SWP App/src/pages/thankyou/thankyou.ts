import { Component } from '@angular/core';
import {  NavController, NavParams } from 'ionic-angular';


@Component({
  selector: 'page-thankyou',
  templateUrl: 'thankyou.html',
})
export class ThankyouPage {

  orderid: any;

  constructor(public navCtrl: NavController, public navParams: NavParams) {
    this.orderid = navParams.get('orderid');

  }

}
