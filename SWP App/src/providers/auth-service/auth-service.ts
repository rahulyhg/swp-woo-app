import {Http} from '@angular/http';
import { Injectable } from '@angular/core';

import * as Woo from 'woocommerce-api';
import {WC_url,consumerKey,consumerSecret} from '../../assets/Settings/settings';

@Injectable()
export class AuthServiceProvider {
WooCommerce: any;
  constructor(public http: Http) {
    this.WooCommerce = Woo({
      url : WC_url,
      consumerKey: consumerKey,
      consumerSecret: consumerSecret,
      wpAPI: true,
      version: 'wc/v2',    
      queryStringAuth: true // for authontication
      })
   }

  init()
  {
    return this.WooCommerce;
  }

}
