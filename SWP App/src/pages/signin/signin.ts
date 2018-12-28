import { Component, ViewChild } from '@angular/core';
import { NavController, NavParams, ToastController , AlertController, Menu} from 'ionic-angular';
import {Http,Headers} from '@angular/http';
import {Validators, FormBuilder, FormGroup, FormControl } from '@angular/forms';
import {Storage} from '@ionic/Storage';

//providers
import {AuthServiceProvider} from '../../providers/auth-service/auth-service';

//pages
import {SignupPage} from '../signup/signup';
import { CartPage } from '../cart/cart';


@Component({
  selector: 'page-signin',
  templateUrl: 'signin.html',
})
export class SigninPage {

  formLogin: FormGroup;
  
  
  username: any;
  userPassword: any;
  userEmail: any;
  SignupPage: SignupPage;
  RestApi: any;
  SigninPage = SigninPage;

  passwordType: string = 'password';
  passwordIcon: string = 'eye-off';
 

  user: any ;
 
  response: any;

  @ViewChild('content') childNavCtrl: NavController; 

  constructor(public navCtrl: NavController, public navParams: NavParams,
    public toastCtrl: ToastController,
    public http: Http,
    public storage:Storage,
    public alertCtrl: AlertController,
    private formBuilder: FormBuilder,
    public WcAuth: AuthServiceProvider) {

      this.RestApi = this.WcAuth.init();

      this.userEmail = "";
      this.userPassword = "";
     
      this.formLogin = formBuilder.group({
         userEmail: new FormControl('', Validators.compose([
          Validators.required,
          Validators.pattern('^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$')
        ])),
        password: ['', Validators.required]
      });

      }


  swp_signIn() {
    console.log(this.formLogin.value.userEmail);

    this.RestApi.getAsync('customers?email='+ this.formLogin.value.userEmail).
    then((res)=>{

      //if(fc.value.toLowerCase() === "abc123" || fc.value.toLowerCase() === "123abc"){
      //return ({validUsername: true});
    //} else {
     // return (null);
   // }
    
      this.user = JSON.parse(res.body)

//if(this.formLogin.value.userPassword  //endpointPassword
  // )
{
  this.storage.set("UserLoginInfo",this.user).then((user)=>{
    console.log(user)
        })

    this.alertCtrl.create({
      title: "Login Successful",
     message: "Logged in successfully.",
    buttons: [{
     text: "OK",
        handler: () => {
         if(this.navParams.get("next")){
          this.navCtrl.push(this.navParams.get("next"));
          } else {
         this.navCtrl.pop();
         }             
      }
     }]
    }).present();
}
//else
//{
  //this.toastCtrl.create({
   // message: "Invalid password",
   // duration: 2000,
   ///position: 'top'
  // }).present();
//}
     })
}    


  sendReset() {
  // this.auth.requestPasswordReset(this.emailAddress).then( (res)=> {
      //On success
    //  this.resetInProgress = true;

      //Pop some toast
   //   let toast = this.toastCtrl.create({
     //   message: 'A password reset email has been sent.  Check your inbox!',
    //    duration: 3000
     // });
     // toast.present();
   // }, (rej)=> {
      //Pop some toast
      //let toast = this.toastCtrl.create({
       // message: 'There was a problem resetting your password.  Please try again!',
       // duration: 3000
      //});
      //toast.present();

     // console.log('Error resetting password: ', rej);
   // });




   //user validation
  // static validUsername(fc: FormControl){
    //if(fc.value.toLowerCase() === "abc123" || fc.value.toLowerCase() === "123abc"){
      //return ({validUsername: true});
    //} else {
     // return (null);
   // }
 // }
  }

  swp_openSignup()
  {
  let currentIndex = this.navCtrl.getActive().index;
    console.log(currentIndex);
    this.navCtrl.push(SignupPage).then(() => {
  //  if(this.navCtrl.getPrevious() && this.navCtrl.getPrevious().component == SignupPage)
    this.navCtrl.remove(currentIndex);

});

   // if(this.navCtrl.getPrevious() && this.navCtrl.getPrevious().component == CartPage)
    //this.navCtrl.popToRoot();
   // this.navCtrl.push(SignupPage);
     //  else 
  //  this.navCtrl.push(SignupPage);
  
 //    };
  }


  swp_hideShowPassword() {
    this.passwordType = this.passwordType === 'text' ? 'password' : 'text';
    this.passwordIcon = this.passwordIcon === 'eye-off' ? 'eye' : 'eye-off';
}
}
