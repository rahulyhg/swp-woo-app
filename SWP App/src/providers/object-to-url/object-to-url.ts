import { Http } from '@angular/http';
import { Injectable } from '@angular/core';
import { URLSearchParams } from '@angular/http';

import {WC_url,consumerKey,consumerSecret} from '../../assets/Settings/settings';
/*
  Generated class for the ObjectToUrlProvider provider.

  See https://angular.io/guide/dependency-injection for more info on providers
  and Angular DI.
*/
@Injectable()
export class ObjectToUrlProvider {

  constructor(public http: Http) {}

  swp_objectToURL(object): URLSearchParams {
		let params: URLSearchParams = new URLSearchParams();
		for (var key in object) {
			if (object.hasOwnProperty(key)) {
				if(Array.isArray(object[key])){
					object[key].forEach(val => {
						params.append(key+'[]', val);
					});
				}
				else params.set(key, object[key]);
			}
		}
		return params;
	}
}
