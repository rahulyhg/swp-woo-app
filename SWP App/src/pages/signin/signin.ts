import { Component } from '@angular/core';
import { NavController, NavParams, ToastController , AlertController} from 'ionic-angular';
import {Http,Headers} from '@angular/http';
import {Validators, FormBuilder, FormGroup } from '@angular/forms';
import { WC_url } from '..';
import {Storage} from '@ionic/Storage';
import { HomePage } from '../home/home';

@Component({
  selector: 'page-signin',
  templateUrl: 'signin.html',
})
export class SigninPage {

  formLogin: FormGroup;
  token: any;
  username: any;
  userPassword: any;
  userEmail: any;


 
  response: any;

  constructor(public navCtrl: NavController, public navParams: NavParams,
    public toastCtrl: ToastController,
    public http: Http,
    public storage:Storage,
    public alertCtrl: AlertController,
    private formBuilder: FormBuilder,) {

      this.userEmail = "";
      this.userPassword = "";
      this.username = "";

      this.formLogin = formBuilder.group({
        username: ['', Validators.required],
        password: ['', Validators.required]
      });

      }


  swp_signIn() {
    console.log(this.formLogin.value);

    this.http.post(WC_url + '/wp-json/jwt-auth/v1/token',this.formLogin.value)
    .subscribe(
      response => { 
       console.log(response.json());
       this.token = response.json().token;


      this.alertCtrl.create({
        title: "Login Successful",
       message: "Logged in successfully.",
        buttons: [{
        text: "OK",
         handler: () => {
           this.navCtrl.push(HomePage);
       //  if(this.navParams.get("next")){
         //this.navCtrl.push(this.navParams.get("next"));
         //} else {
         //this.navCtrl.pop();
         //}             
       }
      }]
      }).present();

     
      this.storage.set("UserLoginInfo",response.json()).then((admin)=>{
        this.username = admin.username;
        this.userEmail = admin.email;
        this.userPassword =admin.password;
        this.token = admin.token;
        })
      }, 
      err=>{
      let toast = this.toastCtrl.create({
      message: "Invalid username or password",
     duration: 3000,
     position: 'top'
   });
  toast.present();
});

this.storage.forEach( (value, key, index) => {
  console.log("This is the value", value)
  console.log("from the key", key)
  console.log("Index is", index)
})
 
  }
}
