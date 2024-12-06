<?php

namespace App\Http\Controllers;

use App\Models\Adresse;
use App\Models\Phone;
use App\Models\Photo;
use App\Models\User;
use App\Models\Vehicule;
use GuzzleHttp\Client;
use Illuminate\Database\Console\Migrations\ResetCommand;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Infobip\Api\SendSmsApi;
use Infobip\Configuration;
use Infobip\Model\SmsAdvancedTextualRequest;
use Infobip\Model\SmsDestination;
use Infobip\Model\SmsTextualMessage;

class UpdateController extends Controller
{
    public function updateMail(Request $request){

            $user = User::where('id',$request->iduser)->first();
            $validatorEmail = $request->validate([
                'email' => 'required',
                'password' => 'required',
            ]);
            if(Hash::check($validatorEmail['password'],$user->password)){
                User::where('id',$request->iduser)->update(['email' => $validatorEmail['email']]);
                return response()->json(['succes' => 'succes']);
            }
            else{
                return response()->json(['error' => 'errpass']);
            }
       }


    public function updatePhotoProfile(Request $request){

        if ($request->hasFile('photoprofile')) {
            $photo2 = Photo::Uploadphoto($request->file('photoprofile'),'photoprofile');

            $photoprofile = Photo::create($photo2);

        }
	User::where('id',$request->iduser)->update(['idphotoprofile' => $photoprofile->id]);

    }

   public function updatePhotoProfileLiv(Request $request){

        if ($request->hasFile('photoprofileLiv')) {
            $photo = Photo::Uploadphoto($request->file('photoprofileLiv'),'photoprofileLivreur');

            $photoprofile = Photo::create($photo);

        }
	User::where('id',$request->iduser)->update(['idphotoprofile' => $photoprofile->id]);

    }

    public function updatePassword(Request $request)
    {
        $user = User::where('id', $request->iduser)->first();
        if(Hash::check($request->lastpassword,$user->password)){
            $pass = Hash::make($request->newpassword);
            User::where('id',$request->iduser)->update(['password'=>$pass]);
            return response()->json(['succes'=>'succes']);
        }else{
            return response()->json(['error'=>'error']);
        }

    }

    public function updatePhone(Request $request){

        $validatorPhone = $request->validate([
           'phonenumber' => 'required',
           'password' => 'required',
        ]);

        $user = User::where('id',$request->iduser)->first();

        if(Hash::check($validatorPhone['password'],$user->password)){

            if (Phone::where('phonenumber', $validatorPhone['phonenumber'])->first()) {
                return response()->json(['error' => 'error']);
            }
            else{
                $code = rand(1000,9999);
                $auth = Str::random(15);


                $configuration = (new Configuration())
                    ->setHost('nz5e8e.api.infobip.com')
                    ->setApiKeyPrefix('Authorization', 'App')
                    ->setApiKey('Authorization', '07f1dfa374b3cb04cf709e7b71464199-67c7d304-02d7-4ab6-8fca-be1054d002d0');

                $client = new Client();

                $sendSmsApi = new SendSMSApi($client, $configuration);
                $destination = (new SmsDestination())->setTo(''.$validatorPhone['phonenumber']);
                $message = (new SmsTextualMessage())
                    ->setFrom('Uber')
                    ->setText('Code de confirmation:'.$code)
                    ->setDestinations([$destination]);
                $request2 = (new SmsAdvancedTextualRequest())
                    ->setMessages([$message]);

                if ($sendSmsApi->sendSmsMessage($request2)) {

                    $phoneData = [
                        'phonenumber' => $validatorPhone['phonenumber'],
                        'verifeid' => 0,
                        'code' => $code,
                        'auth' => $auth,
                    ];
                    Phone::create($phoneData);
                }
		return response()->json(['succes' => 'succes','code' => $code ,'auth' => $auth]);
            }
        }
        else{
            return response()->json(['errorps' => 'errorps']);
        }
    }

    public function updatePhoneCheck(Request $request){

        $validatorCode = $request->validate([
            'code' => 'required',
        ]);

        $phone = Phone::where('auth',$request->auth)->first();
        $user = User::where('id',$request->iduser)->first();

        if ($validatorCode['code'] == $phone->code){

            Phone::where('auth',$request->auth)->update(['verifeid'=>'1']);
            User::where('id',$user->id)->update(['idphone' => $phone->id]);

            return response()->json(['succes' => 'succes']);
        }else{
	    return response()->json(['error' => 'error']);
	}
    }

    public function checkforgetPassword(Request $request){

        $phoneexist = Phone::where('phonenumber',$request->phonenumber)->first();
        if($phoneexist){

            $iduser = User::where('idphone',$phoneexist->id)->first();
            $code = rand(1000,9999);

            $configuration = (new Configuration())
                ->setHost('nz5e8e.api.infobip.com')
                ->setApiKeyPrefix('Authorization', 'App')
                ->setApiKey('Authorization', '07f1dfa374b3cb04cf709e7b71464199-67c7d304-02d7-4ab6-8fca-be1054d002d0');

            $client = new Client();

            $sendSmsApi = new SendSMSApi($client, $configuration);
            $destination = (new SmsDestination())->setTo(''.$request->phonenumber);
            $message = (new SmsTextualMessage())
                ->setFrom('Uber')
                ->setText('Code de confirmation:'.$code)
                ->setDestinations([$destination]);
            $request2 = (new SmsAdvancedTextualRequest())
                ->setMessages([$message]);

            if ($sendSmsApi->sendSmsMessage($request2)) {
                return response()->json(['succes'=>'succes','code'=>$code,'iduser'=>$iduser->id]);
            }

        }else{
            return response()->json(['error'=>'error']);
        }

    }

    public function newpassorgetPassword(Request $request){

        $pass = Hash::make($request->newpassword);
        $ok = User::where('id',$request->iduser)->update(['password'=>$pass]);
        if($ok){
            return response()->json(['succes'=>'succes']);
        }else{
            return response()->json(['error'=>'error']);
        }

    }






}
