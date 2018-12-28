import { Component } from '@angular/core';
import { NavController, NavParams, AlertController } from 'ionic-angular';
import {Storage} from '@ionic/Storage';
import {Validators, FormBuilder, FormGroup, FormControl } from '@angular/forms';

//providers
import {AuthServiceProvider} from '../../providers/auth-service/auth-service';
import { CheckoutPage } from '../checkout/checkout';

@Component({
  selector: 'page-address',
  templateUrl: 'address.html',
})
export class AddressPage {

  RestApi: any;
  formAddress: FormGroup;
  newUser: any;
  useBAddress: boolean;
  userid: any;


  constructor(public navCtrl: NavController, 
    public navParams: NavParams,
    public storage: Storage,
    public alertCtrl: AlertController,
    private formBuilder: FormBuilder,
    public WcAuth: AuthServiceProvider){

      this.newUser ={};
      this.userid = '';
      this.newUser.billing ={};
      this.newUser.shipping = {};
    

      this.RestApi = this.WcAuth.init();

      this.formAddress = formBuilder.group({

        userEmail: ['', Validators.required],
        Billing_phone:  ['', Validators.required],

        Billing_first_name: ['', Validators.required],
        Billing_last_name:['', Validators.required],
        Billing_address_1: ['', Validators.required],
        Billing_address_2: ['', Validators.required],
        Billing_country: ['', Validators.required],
        Billing_state: ['', Validators.required],
        Billing_city: ['', Validators.required],
        Billing_zipcode: ['', Validators.required],

        Shipping_first_name: ['', Validators.required],
        Shipping_last_name:['', Validators.required],
        Shipping_address_1: ['', Validators.required],
        Shipping_address_2: ['', Validators.required],
        Shipping_country: ['', Validators.required],
        Shipping_state: ['', Validators.required],
        Shipping_city: ['', Validators.required],
        Shipping_zipcode: ['', Validators.required],
     });  
   this.setData();
  }

setData(){
  this.storage.ready().then(()=>{
    this.storage.get("UserLoginInfo").then((user)=>{
      console.log(user);
    
      this.newUser = user;
     this.userid = user.id;
     this.newUser.billing = user.billing;
        this.newUser.shipping = user.shipping;
      console.log(this.userid, this.newUser.billing, this.newUser.shipping);
     this.swp_fillData(this.newUser, this.newUser.billing, this.newUser.shipping);
    })
  })
 
  
  }

  swp_fillData(User:any, Ushipping: any,Ubilling: any) {

    console.log(User,User.billing, User.shipping);
    this.formAddress.patchValue({

    userEmail: User.email,
     Billing_phone: User.billing.phone,

     Billing_first_name: User.first_name,
     Billing_last_name:User.last_name,
     Billing_address_1: User.billing.address_1,
      Billing_address_2: User.billing.address_2,
     Billing_country: User.billing.country,
     Billing_state:User.billing.state,
     Billing_city: User.billing.city,
     Billing_zipcode: User.billing.postcode,

    Shipping_first_name: User.shipping.first_name,
    Shipping_last_name:User.shipping.last_name,
     Shipping_address_1:User.shipping.address_1,
     Shipping_address_2:User.shipping.address_2,
     Shipping_country: User.shipping.country,
     Shipping_state:User.shipping.state,
     Shipping_city: User.shipping.city,
     Shipping_zipcode:User.shipping.postcode,
    });
}


swp_checkisShippingSame() {

 this.useBAddress = !this.useBAddress;
  if (this.useBAddress) {
    this.swp_updateShippingAsBilling();
  }
}

 swp_updateShippingAsBilling() {
    if(this.useBAddress)
    {
	      this.formAddress.patchValue({
      
        Shipping_first_name: this.formAddress.value["Billing_first_name"],
		    Shipping_last_name: this.formAddress.value["Billing_last_name"],
        Shipping_address_1:this.formAddress.value["Billing_address_1"],
        Shipping_address_2: this.formAddress.value["Billing_address_2"],
        Shipping_country: this.formAddress.value["Billing_country"],
        Shipping_state: this.formAddress.value["Billing_state"],
        Shipping_city: this.formAddress.value["Billing_city"],
        Shipping_zipcode: this.formAddress.value["Billing_zipcode"]
	      	});
	   
        }
  }

  swp_confirmAddress(){
    console.log(this.formAddress.value);
    
let data : any = {

  "email": this.formAddress.value.userEmail,
  "first_name": this.formAddress.value.Billing_first_name,
  "last_name": this.formAddress.value.Billing_last_name,
  "billing": {
    "first_name": this.formAddress.value.Billing_first_name,
    "last_name": this.formAddress.value.Billing_last_name,
    "company": "",
    "address_1": this.formAddress.value.Billing_address_1,
    "address_2": this.formAddress.value.Billing_address_2,
    "city": this.formAddress.value.Billing_city,
    "state": this.formAddress.value.Billing_state,
    "postcode": this.formAddress.value.Billing_zipcode,
    "country": this.formAddress.value.Billing_country,
    "email": this.formAddress.value.userEmail,
    "phone": this.formAddress.value.userPhone
  },
  "shipping": {
    "first_name": this.formAddress.value.Shipping_first_name,
    "last_name": this.formAddress.value.Shipping_last_name,
    "company": "",
    "address_1": this.formAddress.value.Shipping_address_1,
    "address_2": this.formAddress.value.Shipping_address_2,
    "city": this.formAddress.value.Shipping_city,
    "state": this.formAddress.value.Shipping_state,
    "postcode": this.formAddress.value.Shipping_zipcode,
    "country": this.formAddress.value.Shipping_country,
  }
}

console.log(this.userid);

this.RestApi.postAsync("customers/"+ this.userid, data).then (
  (res)=>{
    console.log(JSON.parse(res.body));

    this.storage.remove("UserLoginInfo");
    this.storage.set("UserLoginInfo",JSON.parse(res.body)).then((data)=>{
      console.log(data);
    })

    this.alertCtrl.create({
      title: "Address Updated",
     message: "Your Account has been updated Successfully",
      buttons: [
        {
          text: 'Ok',
          role: 'cancel',
          handler: () => {
            this.navCtrl.popToRoot();
          }
        },
        {
          text: 'Checkout',
          handler: () => {

            let currentIndex = this.navCtrl.getActive().index;
            console.log(currentIndex);
            this.navCtrl.push(CheckoutPage).then(() => {
            this.navCtrl.remove(currentIndex);
        })
          }

        }

      ]
    }).present();

          },
  (err)=>{console.log(err)}
      ); 
      
           
   this.storage.forEach( (value, key, index) => {
    console.log("This is the value", value)
    console.log("from the key", key)
    console.log("Index is", index)
   })
   
  }


  swp_openCheckoutPage() {
    if (this.navCtrl.getPrevious() && this.navCtrl.getPrevious().component == CheckoutPage)
        this.navCtrl.pop();
    else {
        this.navCtrl.push(CheckoutPage).then(() => {
          this.navCtrl.remove(this.navCtrl.getActive().index - 1);
        });
    }
  }

  ionViewDidEnter()
  {
    this.setData();
  }
}
