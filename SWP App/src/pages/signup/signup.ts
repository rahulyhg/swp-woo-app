import { Component, ViewChild } from '@angular/core';
import {  NavController, NavParams, ToastController,AlertController } from 'ionic-angular';
import {Validators, FormBuilder, FormGroup, FormControl } from '@angular/forms';
import {Storage} from '@ionic/Storage';

//providers
import {AuthServiceProvider} from '../../providers/auth-service/auth-service';
import {SigninPage} from '../signin/signin';
import {AddressPage} from '../address/address';
 
@Component({
  selector: 'page-signup',
  templateUrl: 'signup.html',
})
export class SignupPage {
  @ViewChild('content') childNavCtrl: NavController; 
  formSignup: FormGroup;
  username: any;
  userPassword: any;
  userEmail: any;
  RestApi : any;
  SigninPage = SigninPage;
  newUser: any;


  constructor(public navCtrl: NavController, public navParams: NavParams,
    public toastCtrl: ToastController,
    public storage:Storage,
    public alertCtrl: AlertController,
    private formBuilder: FormBuilder,
    public WcAuth: AuthServiceProvider) {

      this.RestApi = this.WcAuth.init();

      this.newUser ={};

      this.formSignup = formBuilder.group({
        userName: ['', Validators.required],
        password: ['', Validators.required],
     email: new FormControl('', Validators.compose([
      Validators.required,
      Validators.email,
       Validators.pattern('^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$')
     ])),
        userFirstName: ['', [Validators.required,Validators.maxLength(30), Validators.pattern('[a-zA-Z ]*')]],
        userLastName: ['', [Validators.required, Validators.maxLength(30), Validators.pattern('[a-zA-Z ]*')]],
      });

  }

  swp_signUp(){

    console.log(this.formSignup.value);

    let data: any ={
      "email": this.formSignup.value.email,
      "first_name": this.formSignup.value.userFirstName ,
      "last_name": this.formSignup.value.userLastName ,
      "username": this.formSignup.value.userName,
      "password": this.formSignup.value.password
    }

  
   this.RestApi.postAsync('customers',data)
   .then(res => {
     // data will contain the body content from the request
     console.log(JSON.parse(res.body));
     this.newUser = JSON.parse(res.body);


     if(JSON.parse(res.body).message)
     {
       console.log(JSON.parse(res.body).message)
       this.toastCtrl.create({
         message: JSON.parse(res.body).message,
         duration: 3000,
        position: 'top'
        }).present();
     }
     else{

      this.storage.set("UserLoginInfo",this.newUser).then((user)=>{
        console.log(user)
            })

       console.log("account created")
       this.alertCtrl.create({
        title: "Account Created",
       message: "Your account has been created successfully.",
        buttons: [{
        text: "OK",
       handler: () => {  
         
        let currentIndex = this.navCtrl.getActive().index;
        console.log(currentIndex);
        this.navCtrl.push(AddressPage).then(() => {
        this.navCtrl.remove(currentIndex);
                 
       })
      }
      }]
      }).present();
     }
   })
  .catch(err => {
     console.log(err);

   });

  }

  swp_openSignin(){
    let currentIndex = this.navCtrl.getActive().index;
    console.log(currentIndex);
    this.navCtrl.push(SigninPage).then(() => {
    this.navCtrl.remove(currentIndex);
});
  }


}
