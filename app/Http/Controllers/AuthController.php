<?php

namespace App\Http\Controllers;

use App\Models\Adresse;
use App\Models\Colilivreur;
use App\Models\Colis;
use App\Models\Phone;
use App\Models\Photo;
use App\Models\Portfeuille;
use App\Models\User;
use App\Models\Userdevice;
use App\Models\Usertype;
use App\Models\Vehicule;
use GuzzleHttp\Client;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Infobip\Api\SendSmsApi;
use Infobip\Configuration;
use Infobip\Model\SmsAdvancedTextualRequest;
use Infobip\Model\SmsDestination;
use Infobip\Model\SmsTextualMessage;


class AuthController extends Controller
{

    public function register (Request $request)
    {//modification des image aves hasfile
        try {
            $validatedData = $request->validate([
                'fullname' => 'required|string',
                'email' => 'required',
                'password' => 'required',
                'place' => 'required',
            ]);

            $adresseData = [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'place' => $validatedData['place'],
            ];

            if ($request->hasFile('justificationAdresse')) {

                $photo = Photo::Uploadphoto($request->file('justificationAdresse'),'justadresse');

                $idjustification = Photo::create($photo);
                $adresseData['idjustification'] = $idjustification->id;
            }
            $idadresse = Adresse::create($adresseData);

            $userData = [
                'name' => $validatedData['fullname'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'idphone' => $request->idphone,
                'idadresse' => $idadresse->id,                
                'idphotoprofile' => 1,
		        'idusertype'=> $request->idusertype,
            ];

            if ($request->hasFile('pieceidentite')) {

                $photo2 = Photo::Uploadphoto($request->file('pieceidentite'),'pieceidentite');

                $idpieceidentite = Photo::create($photo2);
                $userData['idpieceidentite'] = $idpieceidentite->id;
            }

            if ($request->hasFile('permis')) {

                $photo3 = Photo::Uploadphoto($request->file('permis'),'permis');

                $idpermis = Photo::create($photo3);
                $userData['idpermis'] = $idpermis->id;
            }

            $vehiculeData = [];

            if ($request->hasFile('serie')) {
                $vehiculeData['idtypevehicule'] = 1;
                $photo6 = Photo::Uploadphoto($request->file('serie'),'seriemoto');

                $idserie = Photo::create($photo6);
                $vehiculeData['idserie'] = $idserie->id;
            }

            if ($request->hasFile('cartegrise')) {
                $vehiculeData['idtypevehicule'] = 2; // Voiture

                $photo4 = Photo::Uploadphoto($request->file('cartegrise'),'cartegrise');

                $idcartegrise = Photo::create($photo4);
                $vehiculeData['idcartegrise'] = $idcartegrise->id;
            }

            if ($request->hasFile('matricule')) {

                $photo5 = Photo::Uploadphoto($request->file('matricule'),'matricule');

                $idmatricule = Photo::create($photo5);
                $vehiculeData['idmatricule'] = $idmatricule->id;
            }

            $user = User::create($userData);

            if($request->idusertype == 2){
                $dataPortfeuille['idlivreur'] = $user->id;
                $dataPortfeuille['solde'] = 0;
                $dataPortfeuille['soldebloque'] = 0;
                Portfeuille::create($dataPortfeuille);
            }


            $vehiculeData['idusers'] = $user->id;
            Vehicule::create($vehiculeData);

            $response = ['succes'=>'succes','user' => $user];
            return response()->json($response);


        }
        catch (QueryException $e) {
            // Handle exception
            return (['error' => $e->getMessage()]);
        }
    }
    public function login (Request $request)
    {
        try {
            $request->validate([
                'email' => 'required',
                'password' => 'required',
                'devicetoken' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                $token = $user->createToken('personal token')->plainTextToken;
                $userdevice = [
                    'devicetoken' => $request->devicetoken,
                    'idusers' => $user->id,
                ];
                $deviceexist = Userdevice::where('idusers',$user->id)->first();
                if($deviceexist){
                    $ok = Userdevice::where('idusers',$user->id)->update(['devicetoken'=>$request->devicetoken]);
                    if($ok){
                        $response = ['succes'=>'succes','token' => $token,'typeuser'=>$user->idusertype,'iduser'=>$user->id,'status'=>$user->idjustification];
                        return response()->json($response);
                    }else{
                        return response()->json(['error'=>'error']);
                    }
                }else{
                    $ok2 = Userdevice::create($userdevice);
                    if($ok2){
                        $response = ['succes'=>'succes','token' => $token,'typeuser'=>$user->idusertype,'iduser'=>$user->id,'status'=>$user->idjustification];
                        return response()->json($response);
                    }else{
                        return response()->json(['error'=>'error']);
                    }
                }
            }
            else {
                $response = ['error' => 'error'];
                return response()->json($response);
            }
        }
        catch (QueryException $e) {
            // Handle exception
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
}
    public function code(Request $request2)
    {

            $validatorPhone = $request2->validate([
                'field'=>'required',
                'values'=>'required',
            ]);

            $phoneExist = Phone::where('phonenumber', $validatorPhone['values'])->first();

        if($phoneExist){
            if($phoneExist->verifeid == 1){
                $user2 = User::where('idphone', $phoneExist->id)->first();
                if($user2){
                    return response()->json(['error' => 'error']);
                }else{
                    return response()->json(['complete' => 'complete','idphone'=>$phoneExist->id]);
                }
            }else
            {
                $code = rand(1000, 9999);
                $configuration = (new Configuration())
                    ->setHost('nz5e8e.api.infobip.com')
                    ->setApiKeyPrefix('Authorization', 'App')
                    ->setApiKey('Authorization', '07f1dfa374b3cb04cf709e7b71464199-67c7d304-02d7-4ab6-8fca-be1054d002d0');

                $client = new Client();
                $sendSmsApi = new SendSMSApi($client, $configuration);
                $destination = (new SmsDestination())->setTo('' . $validatorPhone['values']);
                $message = (new SmsTextualMessage())
                    ->setFrom('Uber')
                    ->setText('Code de confirmation:' . $code)
                    ->setDestinations([$destination]);
                $request = (new SmsAdvancedTextualRequest())
                    ->setMessages([$message]);

                if ($sendSmsApi->sendSmsMessage($request)) {
                        Phone::where('phonenumber', $validatorPhone['values'])->update(['code' => $code]);
                 }
                return response()->json(['change' => 'change','Code'=>$code,'Auth'=>$phoneExist->auth]);
            }
        }

        else {
            $code = rand(1000, 9999);
            $auth = Str::random(15);


            $configuration = (new Configuration())
                ->setHost('nz5e8e.api.infobip.com')
                ->setApiKeyPrefix('Authorization', 'App')
                ->setApiKey('Authorization', '07f1dfa374b3cb04cf709e7b71464199-67c7d304-02d7-4ab6-8fca-be1054d002d0');

            $client = new Client();

            $sendSmsApi = new SendSMSApi($client, $configuration);
            $destination = (new SmsDestination())->setTo('' . $validatorPhone['values']);
            $message = (new SmsTextualMessage())
                ->setFrom('Uber')
                ->setText('Code de confirmation:' . $code)
                ->setDestinations([$destination]);
            $request = (new SmsAdvancedTextualRequest())
                ->setMessages([$message]);

            if ($sendSmsApi->sendSmsMessage($request)) {
                    $phoneData = [
                        'phonenumber' => $validatorPhone['values'],
                        'verifeid' => 0,
                        'code' => $code,
                        'auth' => $auth,
                    ];
                    Phone::create($phoneData);
            }
            return response()->json(['succes'=>'succes','Code' => $code, 'Auth' => $auth]);
        }
}
    public function checkcode(Request $request)
    {

            $validateAuth = $request->validate([
                'auth'=>'required|max:15|min:15',
                'code'=>'required|max:4|min:4',
            ]);
		$id = Phone::where('auth',$validateAuth['auth'])->first();
                $auth = Phone::where('auth',$validateAuth['auth'])->update(['verifeid'=>1]);
                return response()->json(['IDphone'=>$id->id,'succes'=>'succes']);

}
    public function getUser(Request $request){

        $iduser = User::where('id',$request->iduser)->first();

        if ($iduser){

            $phone = Phone::where('id',$iduser->idphone)->first();
            $place = Adresse::where('id',$iduser->idadresse)->first();
            $type = Usertype::where('id',$iduser->idusertype)->first();
            $nbcoli = Colis::where('iduser',$iduser->id)->count();
            $nbcoliLivrer = Colilivreur::where('idlivreur',$iduser->id)->where('idetatcoli',5)->count();
            $nbcoliRejeter = Colilivreur::where('idlivreur',$iduser->id)->where('idetatcoli',4)->count();
	    $photo = Photo::where('id',$iduser->idphotoprofile)->first();
            $user = [
                'name' => $iduser->name,
                'email' => $iduser->email,
                'phone' => $phone->phonenumber,
                'place' => $place->place,
                'nball' => $nbcoli,
                'nblivrer' => $nbcoliLivrer,
                'nbrejeter' => $nbcoliRejeter,
		'photo' => $photo->url,

            ];
            return response()->json($user);
        }
        else{
            return response()->json(['error' => 'error']);
        }
    }

}
