<?php

namespace App\Http\Controllers;

use App\Models\Adresse;
use App\Models\Colidate;
use App\Models\Coliinfolivraison;
use App\Models\Colilivreur;
use App\Models\Colinature;
use App\Models\Colis;
use App\Models\Colistaille;
use App\Models\Coliuserinfolivraison;
use App\Models\Historique;
use App\Models\Historiquecolis;
use App\Models\Livreuravis;
use App\Models\Notfication;
use App\Models\Phone;
use App\Models\Photo;
use App\Models\Portfeuille;
use App\Models\Prixcoli;
use App\Models\Prixnegocie;
use App\Models\Problem;
use App\Models\Typecoli;
use App\Models\User;
use App\Models\Userdevice;
use Carbon\Carbon;
use Google\Cloud\Storage\Notification;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpParser\Node\Scalar\String_;


class ColiController extends Controller
{
    public function colis(Request $request)
    {

        try {
            $validatedData = $request->validate([
                'iduser' => 'required',
                'description' => 'required',
            ]);

            $colisData = [
                'description' => $validatedData['description'],
                'iduser' => $validatedData['iduser'],
            ];



            $ref_coli = rand(100000, 999999);
            $ok = Colis::where('refcoli',$ref_coli)->exists();
            if($ok){
                $colisData['refcoli'] = $ref_coli;
            }else{
                $ref_coli = rand(100000, 999999);
                $colisData['refcoli'] = $ref_coli;
            }



            if (isset($request->idtaillecoli)) {
                $colisData['idtaillecoli'] = $request->idtaillecoli;
            }

            if (isset($request->idtypecoli)) {
                $colisData['idtype'] = $request->idtypecoli;
            }

            $coliprix = ['prixbase' => $request->prixbase];
            $prixcoli = Prixcoli::create($coliprix);

            $colisData['idprix'] = $prixcoli->id;

            if (isset($request->date) && isset($request->time)) {

                $colidate = [
                    'time' => $request->time,
                    'date' => $request->date,
                ];

                $datecoli = Colidate::create($colidate);
                $colisData['iddatecoli'] = $datecoli->id;
            }

            $colis = Colis::create($colisData);
            $infolivraison = ['idcolis' => $colis->id];

            $validatedenvoyeur = $request->validate([
                'fullname' => 'required',
                'email' => 'required',
                'phonenumber' => 'required',
            ]);

            $infoenvoyeur = [
                'name' => $validatedenvoyeur['fullname'],
                'email' => $validatedenvoyeur['email'],
                'phonenumber' => $validatedenvoyeur['phonenumber'],
            ];

            $addadresse = [
                'place' => $request->adresse,
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
            ];

            $coliadresse = Adresse::create($addadresse);

            $infoenvoyeur['idadresse'] = $coliadresse->id;
            $infoenvoyeur['idadressetype'] = 1;
            $idinfoenvoyeur = Coliuserinfolivraison::create($infoenvoyeur);

            $infolivraison['idinfoenvoyeur'] = $idinfoenvoyeur->id;


            $validatedrecepture = $request->validate([
                'fullnamer' => 'required',
                'emailr' => 'required',
                'phonenumberr' => 'required',
            ]);

            $inforecepture = [
                'name' => $validatedrecepture['fullnamer'],
                'email' => $validatedrecepture['emailr'],
                'phonenumber' => $validatedrecepture['phonenumberr'],
            ];

            $addadresser = [
                'place' => $request->adresser,
                'longitude' => $request->longituder,
                'latitude' => $request->latituder,
            ];

            $coliadresse = Adresse::create($addadresser);

            $inforecepture['idadresse'] = $coliadresse->id;
            $inforecepture['idadressetype'] = 2;
            $idinforecepture = Coliuserinfolivraison::create($inforecepture);

            $infolivraison['idinforecepture'] = $idinforecepture->id;
            $infolivraison['idcoli'] = $colis->id;

            Coliinfolivraison::create($infolivraison);

            $colilivreur = [
                'idcoli' => $colis->id,
                'idlivreur' => null,
            ];
            Colilivreur::create($colilivreur);

            $colinature = [
                'idcoli' => $colis->id,
            ];

            if (isset($request->idnature)) {
                $colinature['idnature'] = $request->idnature;
            }
            Colinature::create($colinature);


            $historiqueColis['idclient']= $validatedData['iduser'];
            $historiqueColis['idcoli']= $colis->id;
            $historiqueColis['information']= 'Client a publie ce coli';
            Historiquecolis::create($historiqueColis);

            return response()->json(['idcoli' => $colis->id, 'succes' => 'Colis publié avec succé']);
        } catch (QueryException $e) {
            // Handle exception
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    function getdistance($longFrom, $latFrom, $longTo, $latTo)
    {
        $theta = $longFrom - $longTo;
        $dist = sin(deg2rad($latFrom)) * sin(deg2rad($latTo)) + cos(deg2rad($latFrom)) * cos(deg2rad($latTo)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        return $km = round($miles * 1.609344, 2);
    }
    function sendNotfication(String $token, String $message , String $title){
        $SERVER_API_KEY = 'AAAA_sBhemc:APA91bFP6gc3L7g13v0r7SeZLCT5uqW6nbNjP91quF3YTfVOyxNA45QbCyqWUVRMKUVAb1AfGQvP0krtFDLvd7AGdCFi8QT5AUrb1BOGT40Hm2blbiM5_--JM-iL0NldEu4G9_u88009';

        $token_1 = $token;

        $data = [

            "registration_ids" => [
                $token_1
            ],

            "notification" => [

                "title" => $title,

                "body" => $message,

                "sound"=> "default" // required for sound on ios

            ],

        ];

        $dataString = json_encode($data);

        $headers = [

            'Authorization: key=' . $SERVER_API_KEY,

            'Content-Type: application/json',

        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
         $response = curl_exec($ch);


    }

    function envoyerNotfication($idclient,$idcoli,$idlivreur,$message){

        $data['idclient']=$idclient;
        $data['idlivreur']=$idlivreur;
        $data['idcoli']=$idcoli;
        $coli = Colis::where('id',$idcoli)->first();
        $data['message']=''.$message.''.$coli->refcoli;
        $user = User::where('id',$idclient)->first();
        $token = Userdevice::where('idusers',$idlivreur)->first();
        $data['title']=$user->name;
	    $data['clientdevicetoken']=$token->devicetoken;

        Notfication::create($data);
        $this->sendNotfication($token->devicetoken,''.$message.'('.$coli->refcoli.')',$user->name);

    }

    function envoyerNotficationLivreur($idclient,$idcoli,$idlivreur,$message){

        $data['idclient']=$idclient;
        $data['idlivreur']=$idlivreur;
        $data['idcoli']=$idcoli;
        $coli = Colis::where('id',$idcoli)->first();
        $data['message']=''.$message.''.$coli->refcoli;
        $user = User::where('id',$idlivreur)->first();
        $token = Userdevice::where('idusers',$idclient)->first();
        $data['title']=$user->name;
        $data['clientdevicetoken']=$token->devicetoken;

        Notfication::create($data);
        $this->sendNotfication($token->devicetoken,''.$message.'('.$coli->refcoli.')',$user->name);

    }

    public function getColirejeterClient(Request $request)
    {
        // Time

        $datacolis = array();

        $coliparuser = Colis::where('colis.iduser', $request->iduser)->leftJoin('colilivreurs', 'colis.id', '=', 'colilivreurs.idcoli')->where('colilivreurs.idetatcoli', 4)->where('refcoli','like',$request->search . '%')
            ->get();

        foreach ($coliparuser as $val):

            $idcoli = Colis::where('id', $val->idcoli)->first();
            $clientname = User::where('id', $idcoli->iduser)->first();
            $liv = Colilivreur::where('idcoli',$val->idcoli)->first();
            $livreur = User::where('id', $liv->idlivreur)->first();
            $infocolis = Coliinfolivraison::where('idcoli', $val->idcoli)->first();
            $envoyeur = Coliuserinfolivraison::where('id', $infocolis->idinfoenvoyeur)->first();
            $recepteur = Coliuserinfolivraison::where('id', $infocolis->idinforecepture)->first();
            $adresse1 = Adresse::where('id', $envoyeur->idadresse)->first();
            $adresse2 = Adresse::where('id', $recepteur->idadresse)->first();
            //calculate distance of coli trajet.
            $distance = $this->getdistance($adresse1->longitude, $adresse1->latitude, $adresse2->longitude, $adresse2->latitude);
            $datacolisinfo['distance'] = $distance;
            $rejeter = Problem::where('idclient',$request->iduser)->where('idcoli',$val->idcoli)->first();
            $avis = Livreuravis::where('idclient',$request->iduser)->where('idcoli',$val->idcoli)->first();

            if(!$avis){
                $datacolisinfo['message'] = null;
            }else{
                $datacolisinfo['message'] = $avis->message;
            }
            if(!$avis){
                $datacolisinfo['etoile'] = null;
            }else{
                $datacolisinfo['etoile'] = $avis->avis;
            }
            $photo = Photo::where('id',$livreur->idphotoprofile)->first();

            $datacolisinfo['photo'] = $photo->url;
            $datacolisinfo['daterejeter'] = Date($rejeter->created_at);
            $datacolisinfo['messagerejeter'] = $rejeter->message;
            $datacolisinfo['idclient'] = $clientname->id;
            $datacolisinfo['idcolis'] = $val->idcoli;
            $datacolisinfo['livreurname'] = $livreur->name;
            $datacolisinfo['idlivreur'] = $livreur->id;
            $datacolisinfo['reference'] = $val->refcoli;
            $datacolisinfo['description'] = $val->description;


            $datacolis[] = $datacolisinfo;
        endforeach;

        return response()->json($datacolis);


    }
    public function getColirejeterLivreur(Request $request)
    {
        // Time

        $datacolis = array();

        $coliparuser = Colilivreur::where('Colilivreurs.idlivreur', $request->iduser)->leftJoin('colis', 'colis.id', '=', 'colilivreurs.idcoli')->where('colilivreurs.idetatcoli', 4)->where('refcoli','like',$request->search . '%')
            ->get();

        foreach ($coliparuser as $val):

            $idcoli = Colis::where('id', $val->idcoli)->orderBy('id')->first();
            $clientname = User::where('id', $idcoli->iduser)->first();
            $liv = Colilivreur::where('idcoli',$val->idcoli)->first();
            $livreur = User::where('id', $liv->idlivreur)->first();
            $infocolis = Coliinfolivraison::where('idcoli', $val->idcoli)->first();
            $envoyeur = Coliuserinfolivraison::where('id', $infocolis->idinfoenvoyeur)->first();
            $recepteur = Coliuserinfolivraison::where('id', $infocolis->idinforecepture)->first();
            $adresse1 = Adresse::where('id', $envoyeur->idadresse)->first();
            $adresse2 = Adresse::where('id', $recepteur->idadresse)->first();
            //calculate distance of coli trajet.
            $distance = $this->getdistance($adresse1->longitude, $adresse1->latitude, $adresse2->longitude, $adresse2->latitude);
            $datacolisinfo['distance'] = $distance;
            $rejeter = Problem::where('idlivreur',$request->iduser)->where('idcoli',$val->idcoli)->first();
            $avis = Livreuravis::where('idlivreur',$request->iduser)->where('idcoli',$val->idcoli)->first();

            if(!$avis){
                $datacolisinfo['message'] = null;
            }else{
                $datacolisinfo['message'] = $avis->message;
            }
            if(!$avis){
                $datacolisinfo['etoile'] = null;
            }else{
                $datacolisinfo['etoile'] = $avis->avis;
            }
            $photo = Photo::where('id',$clientname->idphotoprofile)->first();

            $datacolisinfo['photo'] = $photo->url;
            $datacolisinfo['daterejeter'] = Date($rejeter->created_at);
            $datacolisinfo['messagerejeter'] = $rejeter->message;
            $datacolisinfo['clientname'] = $clientname->name;
            $datacolisinfo['idclient'] = $clientname->id;
            $datacolisinfo['idcolis'] = $val->idcoli;
            $datacolisinfo['idlivreur'] = $livreur->id;
            $datacolisinfo['reference'] = $val->refcoli;

            $datacolis[] = $datacolisinfo;
        endforeach;

        return response()->json($datacolis);


    }
    public function getColiLivrerLivreur(Request $request)
    {
        // Time

        $datacolis = array();

        $coliparuser = Colilivreur::where('Colilivreurs.idlivreur', $request->iduser)->leftJoin('colis', 'colis.id', '=', 'colilivreurs.idcoli')->where('colilivreurs.idetatcoli', 5)->where('refcoli','like',$request->search . '%')
            ->get();

        foreach ($coliparuser as $val):

            $idcoli = Colis::where('id', $val->idcoli)->first();
            $clientname = User::where('id', $idcoli->iduser)->first();
            $liv = Colilivreur::where('idcoli',$val->idcoli)->first();
            $livreur = User::where('id', $liv->idlivreur)->first();
            $infocolis = Coliinfolivraison::where('idcoli', $val->idcoli)->first();
            $envoyeur = Coliuserinfolivraison::where('id', $infocolis->idinfoenvoyeur)->first();
            $recepteur = Coliuserinfolivraison::where('id', $infocolis->idinforecepture)->first();
            $adresse1 = Adresse::where('id', $envoyeur->idadresse)->first();
            $adresse2 = Adresse::where('id', $recepteur->idadresse)->first();
            //calculate distance of coli trajet.
            $distance = $this->getdistance($adresse1->longitude, $adresse1->latitude, $adresse2->longitude, $adresse2->latitude);
            $datacolisinfo['distance'] = $distance;
            $avis = Livreuravis::where('idlivreur',$request->iduser)->where('idcoli',$val->idcoli)->first();

            if(!$avis){
                $datacolisinfo['message'] = null;
            }else{
                $datacolisinfo['message'] = $avis->message;
            }
            if(!$avis){
                $datacolisinfo['etoile'] = null;
            }else{
                $datacolisinfo['etoile'] = $avis->avis;
            }
            $photo = Photo::where('id',$clientname->idphotoprofile)->first();

            $datacolisinfo['photo'] = $photo->url;
            $datacolisinfo['datelivrer'] = Date($liv->updated_at);
            $datacolisinfo['clientname'] = $clientname->name;
            $datacolisinfo['idclient'] = $clientname->id;
            $datacolisinfo['idcolis'] = $val->idcoli;
            $datacolisinfo['idlivreur'] = $livreur->id;
            $datacolisinfo['reference'] = $val->refcoli;

            $datacolis[] = $datacolisinfo;
        endforeach;

        return response()->json($datacolis);


    }
    public function getColiLivrerClient(Request $request)
    {
        // Time

        $datacolis = array();

        $coliparuser = Colis::where('colis.iduser', $request->iduser)->leftJoin('colilivreurs', 'colis.id', '=', 'colilivreurs.idcoli')->where('colilivreurs.idetatcoli', 5)->where('refcoli','like',$request->search . '%')
            ->get();

        foreach ($coliparuser as $val):

            $idcoli = Colis::where('id', $val->idcoli)->first();
            $clientname = User::where('id', $idcoli->iduser)->first();
            $liv = Colilivreur::where('idcoli',$val->idcoli)->first();
            $livreur = User::where('id', $liv->idlivreur)->first();
            $infocolis = Coliinfolivraison::where('idcoli', $val->idcoli)->first();
            $envoyeur = Coliuserinfolivraison::where('id', $infocolis->idinfoenvoyeur)->first();
            $recepteur = Coliuserinfolivraison::where('id', $infocolis->idinforecepture)->first();
            $adresse1 = Adresse::where('id', $envoyeur->idadresse)->first();
            $adresse2 = Adresse::where('id', $recepteur->idadresse)->first();
            //calculate distance of coli trajet.
            $distance = $this->getdistance($adresse1->longitude, $adresse1->latitude, $adresse2->longitude, $adresse2->latitude);
            $avis = Livreuravis::where('idclient',$request->iduser)->where('idcoli',$val->idcoli)->first();

            if(!$avis){
                $datacolisinfo['message'] = null;
            }else{
                $datacolisinfo['message'] = $avis->message;
            }
            if(!$avis){
                $datacolisinfo['etoile'] = null;
            }else{
                $datacolisinfo['etoile'] = $avis->avis;
            }
            $photo = Photo::where('id',$livreur->idphotoprofile)->first();

            $datacolisinfo['photo'] = $photo->url;

            $datacolisinfo['distance'] = $distance;
            $datacolisinfo['dateLivrer'] = Date($liv->updated_at);
            $datacolisinfo['idclient'] = $clientname->id;
            $datacolisinfo['idcolis'] = $val->idcoli;
            $datacolisinfo['livreurname'] = $livreur->name;
            $datacolisinfo['idlivreur'] = $livreur->id;
            $datacolisinfo['reference'] = $val->refcoli;
            $datacolisinfo['description'] = $val->description;

            $datacolis[] = $datacolisinfo;
        endforeach;

        return response()->json($datacolis);


    }
    public function alertEmplois(Request $request)
    {
        // Time

        $datacolis = array();

        $coliparuser = Colis::leftJoin('colilivreurs', 'colis.id', '=', 'colilivreurs.idcoli')->where('colilivreurs.idetatcoli', 1)->where('colilivreurs.idlivreur', null)->where('refcoli','like',$request->search . '%')
            ->get();

        foreach ($coliparuser as $val):

            $idcoli = Colis::where('id', $val->idcoli)->first();
            $coliexist = Prixnegocie::where('idcoli', $val->idcoli)->where('idlivreur', $request->idlivreur)->first();

            if ($coliexist) {
            } else {
                $solde = Portfeuille::where('idlivreur', $request->idlivreur)->first();
                $clientname = User::where('id', $idcoli->iduser)->first();
                $infocolis = Coliinfolivraison::where('idcoli', $val->idcoli)->first();
                $envoyeur = Coliuserinfolivraison::where('id', $infocolis->idinfoenvoyeur)->first();
                $recepteur = Coliuserinfolivraison::where('id', $infocolis->idinforecepture)->first();
                $adresse1 = Adresse::where('id', $envoyeur->idadresse)->first();
                $adresse2 = Adresse::where('id', $recepteur->idadresse)->first();
                $numenv = Coliuserinfolivraison::where('id', $infocolis->idinfoenvoyeur)->first();
                $numrecepteur = Coliuserinfolivraison::where('id', $infocolis->idinforecepture)->first();

                //calculate distance of coli trajet.
                $distance = $this->getdistance($adresse1->longitude, $adresse1->latitude, $adresse2->longitude, $adresse2->latitude);

                $coliprix = Prixcoli::where('id', $val->idprix)->first();
                $taillecoli = Colistaille::where('id', $val->idtaillecoli)->first();
                $typecoli = Typecoli::where('id', $val->idtype)->first();
                $datecoli = Colidate::where('id',$val->iddatecoli)->first();
                $livreurid = Colilivreur::where('idcoli',$val->idcoli)->first();
                $created_at = Carbon::parse($livreurid->updated_at);
                $timecoli = $created_at->diffInHours(Carbon::now());

                if($timecoli >= 24 && $livreurid->idlivreur == null){
                    Colilivreur::where('idcoli',$val->idcoli)->update(['idetatcoli'=> 0]);
                }
                $photo = Photo::where('id',$clientname->idphotoprofile)->first();

                $datacolisinfo['photo'] = $photo->url;
                $datacolisinfo['distance'] = $distance;
                $datacolisinfo['solde'] = $solde->solde;
                $datacolisinfo['soldebloque'] = $solde->soldebloque;
                $datacolisinfo['clientname'] = $clientname->name;
                $datacolisinfo['idclient'] = $clientname->id;
                $datacolisinfo['NumR'] = $numrecepteur->phonenumber;
                $datacolisinfo['NumE'] = $numenv->phonenumber;
                $datacolisinfo['createdat'] = $idcoli->created_at;
                $datacolisinfo['adR'] = $adresse1->place;
                $datacolisinfo['adE'] = $adresse2->place;
                $datacolisinfo['idcolis'] = $val->idcoli;
                $datacolisinfo['reference'] = $val->refcoli;
                $datacolisinfo['description'] = $val->description;
                $datacolisinfo['prix'] = $coliprix->prixclient;
                $datacolisinfo['taille'] = $taillecoli->taille;
                $datacolisinfo['type'] = $typecoli->type;
                //$datacolisinfo['statut']=$val->status;
                $datacolisinfo['date']=$datecoli->date;
                $datacolisinfo['time']=$datecoli->time;

                $datacolis[] = $datacolisinfo;
            }
        endforeach;

        return response()->json($datacolis);


    }
    public function getColiEtat2et3Livreur(Request $request)
    {

        $datacolis = array();

        $coliparuser = Colis::where('colilivreurs.idlivreur', $request->idlivreur)->leftJoin('colilivreurs', 'colis.id', '=', 'colilivreurs.idcoli')->whereIn('colilivreurs.idetatcoli', array(2, 3))->where('refcoli','like',$request->search . '%')
            ->get();

        foreach ($coliparuser as $val):
            $etat = Colilivreur::where('idcoli', $val->idcoli)->first();

            if ($etat->idetatcoli == 1) {
                $datacolisinfo['etat'] = 'En Attente';
            } elseif ($etat->idetatcoli == 2) {
                $datacolisinfo['etat'] = 'Accepter';
            } elseif ($etat->idetatcoli == 3) {
                $datacolisinfo['etat'] = 'En Cours';
            }

            $infocolis = Coliinfolivraison::where('idcoli', $val->idcoli)->first();
            $envoyeur = Coliuserinfolivraison::where('id', $infocolis->idinfoenvoyeur)->first();
            $recepteur = Coliuserinfolivraison::where('id', $infocolis->idinforecepture)->first();
            $adresse1 = Adresse::where('id', $envoyeur->idadresse)->first();
            $adresse2 = Adresse::where('id', $recepteur->idadresse)->first();

            //calculate distance of coli trajet.
            $distance = $this->getdistance($adresse1->longitude, $adresse1->latitude, $adresse2->longitude, $adresse2->latitude);
            $datacolisinfo['distance'] = $distance;
            $coliprix = Prixcoli::where('id', $val->idprix)->first();
            $taillecoli = Colistaille::where('id', $val->idtaillecoli)->first();
            $typecoli = Typecoli::where('id', $val->idtype)->first();
            $datecoli = Colidate::where('id',$val->iddatecoli)->first();
	        $co = Colis::where('id', $val->idcoli)->first();
            $us = User::where('id',$co->iduser)->first();
            $photo = Photo::where('id',$us->idphotoprofile)->first();

            $datacolisinfo['photo'] = $photo->url;
	        $datacolisinfo['adR'] = $adresse1->place;
	        $datacolisinfo['adE'] = $adresse2->place;
            $datacolisinfo['numE'] = $envoyeur->phonenumber;
            $datacolisinfo['numR'] = $recepteur->phonenumber;
            $datacolisinfo['nameE'] = $envoyeur->name;
            $datacolisinfo['nameR'] = $recepteur->name;
	        $datacolisinfo['idclient'] = $co->iduser;
	        $datacolisinfo['nameclient'] = $us->name;
            $datacolisinfo['idcolis'] = $val->idcoli;
            $datacolisinfo['idlivreur'] = $request->idlivreur;
            $datacolisinfo['reference'] = $val->refcoli;
            $datacolisinfo['description'] = $val->description;
            $datacolisinfo['prix'] = $coliprix->prixclient;
            $datacolisinfo['taille'] = $taillecoli->taille;
            $datacolisinfo['type'] = $typecoli->type;
            // $datacolisinfo['statut']=$val->status;
            $datacolisinfo['date']=$datecoli->date;
            $datacolisinfo['time']=$datecoli->time;

            $datacolis[] = $datacolisinfo;
        endforeach;

        return response()->json($datacolis);


    }
    public function getColiEtat2et3Client(Request $request)
    {

        $datacolis = array();

        $coliparuser = Colis::where('Colis.iduser', $request->idclient)->leftJoin('colilivreurs', 'colis.id', '=', 'colilivreurs.idcoli')->whereIn('colilivreurs.idetatcoli', array(2, 3))->where('refcoli','like',$request->search . '%')
            ->get();

        foreach ($coliparuser as $val):
            $etat = Colilivreur::where('idcoli', $val->idcoli)->first();

            if ($etat->idetatcoli == 1) {
                $datacolisinfo['etat'] = 'En Attente';
            } elseif ($etat->idetatcoli == 2) {
                $datacolisinfo['etat'] = 'Accepter';
            } elseif ($etat->idetatcoli == 3) {
                $datacolisinfo['etat'] = 'En Cours';
            }

            $infocolis = Coliinfolivraison::where('idcoli', $val->idcoli)->first();
            $envoyeur = Coliuserinfolivraison::where('id', $infocolis->idinfoenvoyeur)->first();
            $recepteur = Coliuserinfolivraison::where('id', $infocolis->idinforecepture)->first();
            $adresse1 = Adresse::where('id', $envoyeur->idadresse)->first();
            $adresse2 = Adresse::where('id', $recepteur->idadresse)->first();

            //calculate distance of coli trajet.
            $distance = $this->getdistance($adresse1->longitude, $adresse1->latitude, $adresse2->longitude, $adresse2->latitude);
            $datacolisinfo['distance'] = $distance;
            $coliprix = Prixcoli::where('id', $val->idprix)->first();
            $taillecoli = Colistaille::where('id', $val->idtaillecoli)->first();
            $typecoli = Typecoli::where('id', $val->idtype)->first();
            $datecoli = Colidate::where('id',$val->iddatecoli)->first();

            $us = User::where('id',$etat->idlivreur)->first();
            $ph = Phone::where('id',$us->idphone)->first();
            $photo = Photo::where('id',$us->idphotoprofile)->first();

            $datacolisinfo['photo'] = $photo->url;
	        $datacolisinfo['adR'] = $adresse1->place;
	        $datacolisinfo['adE'] = $adresse2->place;
            $datacolisinfo['numE'] = $envoyeur->phonenumber;
            $datacolisinfo['numR'] = $recepteur->phonenumber;
            $datacolisinfo['nameE'] = $envoyeur->name;
            $datacolisinfo['nameR'] = $recepteur->name;
            $datacolisinfo['idlivreur'] = $etat->idlivreur;
            $datacolisinfo['namelivreur'] = $us->name;
            $datacolisinfo['phonelivreur'] = $ph->phonenumber;
            $datacolisinfo['idcolis'] = $val->idcoli;
            $datacolisinfo['reference'] = $val->refcoli;
            $datacolisinfo['description'] = $val->description;
            $datacolisinfo['prix'] = $coliprix->prixfinal;
            $datacolisinfo['taille'] = $taillecoli->taille;
            $datacolisinfo['type'] = $typecoli->type;
            // $datacolisinfo['statut']=$val->status;
             $datacolisinfo['date']=$datecoli->date;
             $datacolisinfo['time']=$datecoli->time;

            $datacolis[] = $datacolisinfo;
        endforeach;

        return response()->json($datacolis);


    }
    public function UpdateLivreurEtat(Request $request)
    {
        // idcoli , idlivreur , idetatcoli
        $colis = Colilivreur::where('idcoli', $request->idcoli)->where('idlivreur',$request->idlivreur)->first();

        if ($colis) {
            $ok = Colilivreur::where('idcoli', $request->idcoli)->update(['idetatcoli' => $request->idetatcoli]);
            if($ok){
                $col = Colis::where('id',$request->idcoli)->first();
                if($request->idetatcoli == 3){
                    $this->envoyerNotficationLivreur($col->iduser,$request->idcoli,$request->idlivreur,"j'ai commencé ce coli : ");
                }else if($request->idetatcoli == 5){
                    $this->envoyerNotficationLivreur($col->iduser,$request->idcoli,$request->idlivreur,"j'ai livré ce coli : ");
                }

                return response()->json(['succes' => 'succes']);

            }else{
                return response()->json(['error' => 'error']);
            }
        }else{
            return null ;
        }
    }
    public function rejeterLivreurEtat(Request $request)
    {

        $coli = Colilivreur::where('idcoli', $request->idcoli)->where('idlivreur',$request->idlivreur);

        if ($coli) {
            $ok = Colilivreur::where('idcoli', $request->idcoli)->update(['idetatcoli' => 4]);


            $data['idcoli']=$request->idcoli;
            $data['idlivreur']=$request->idlivreur;
            $data['idclient']=$request->idclient;
            $data['message']=$request->message;
            $data['envoyedepuis']='Livreur';
            $data['distinateur']='client';

            $ok1 = Problem::create($data);
            if($ok && $ok1){

                $this->envoyerNotficationLivreur($request->idclient,$request->idcoli,$request->idlivreur,"j'ai rejeté ce coli : ");
                return response()->json(['succes' => 'succes']);
            }else{
                return response()->json(['error' => 'error']);
            }

        } else {
            return null;
        }


    }
    public function getAdresseColis(Request $request)
    {

        $infocolis = Coliinfolivraison::where('idcoli', $request->idcoli)->first();

        $envoyeur = Coliuserinfolivraison::where('id', $infocolis->idinfoenvoyeur)->first();
        $recepteur = Coliuserinfolivraison::where('id', $infocolis->idinforecepture)->first();

        $adresse1 = Adresse::where('id', $envoyeur->idadresse)->first();
        $adresse2 = Adresse::where('id', $recepteur->idadresse)->first();

        $data = [
            'longE' => $adresse1->longitude,
            'latE' => $adresse1->latitude,
            'longR' => $adresse2->longitude,
            'latR' => $adresse2->latitude,
        ];

        return response()->json($data);
    }
    public function updatePrix(Request $request)
    {

        $coli = Colis::where('id', $request->idcoli)->first();

        $prixUpdateted = Prixcoli::where('id', $coli->idprix)->update(['prixclient' => $request->prixclient]);

        if ($prixUpdateted) {
            return response()->json(['succes' => 'succes']);
        } else {
            return response()->json(['error' => 'error']);
        }

    }
    public function negociePrix(Request $request)
    {
        $coli = Colis::where('id',$request->idcoli)->first();
        $historiqueColis['idclient']= $coli->iduser;
        $historiqueColis['idlivreur']= $request->idlivreur;
        $historiqueColis['idcoli']= $request->idcoli;
        $historiqueColis['information']= 'Livreur a proposé un prix';
        Historiquecolis::create($historiqueColis);

        $his = Historique::where('idcoli',$request->idcoli)->where('idlivreur',$request->idlivreur)->first();
        if($his){
            Historique::where('idcoli',$request->idcoli)->update(['prixenleve'=>$request->soldebloque,'etat'=>'propose']);
        }else{
            $hisData['idlivreur']=$request->idlivreur;
            $hisData['idcoli']=$request->idcoli;
            $hisData['etat']='propose';
            $hisData['prixenleve']=$request->soldebloque;
            Historique::create($hisData);
        }
        $solde = Portfeuille::where('idlivreur',$request->idlivreur)->first();
        $ok2 = Portfeuille::where('idlivreur',$request->idlivreur)->update(['solde' =>$solde->solde - $request->soldebloque,'soldebloque'=>$solde->soldebloque + $request->soldebloque]);
        $coliexit = Prixnegocie::where('idcoli', $request->idcoli)->where('idlivreur', $request->idlivreur)->first();
        if ($coliexit && $ok2 ) {
            $ok = Prixnegocie::where('idcoli', $coliexit->idcoli)->update(['prixcoli' => $request->prixnegocie]);
            if ($ok) {
                $this->envoyerNotficationLivreur($request->idclient,$request->idcoli,$request->idlivreur,"Une proposition pour ce coli : ");
                return response()->json(['succes' => 'succes']);
            } else {
                return response()->json(['error' => 'error']);
            }
        } else {
            $negociationData['idcoli'] = $request->idcoli;
            $negociationData['idclient'] = $request->idclient;
            $negociationData['idlivreur'] = $request->idlivreur;
            $negociationData['prixcoli'] = $request->prixnegocie;

            $ok = Prixnegocie::create($negociationData);
            if ($ok) {
                return response()->json(['succes' => 'succes']);
            } else {
                return response()->json(['error' => 'error']);
            }
        }

    }
    public function EnattenteLivreur(Request $request)
    {

        $datacolis = array();

        $enatentelivreur = Prixnegocie::where('prixnegocies.idlivreur', $request->idlivreur)->leftJoin('colilivreurs', 'prixnegocies.idcoli', '=', 'colilivreurs.idcoli')->where('colilivreurs.idetatcoli', 1)->get();

        foreach ($enatentelivreur as $val):

            $test = Prixnegocie::where('idcoli', $val->idcoli)->first();
            $client = User::where('id', $test->idclient)->first();
            $phone = Phone::where('id', $client->idphone)->first();
            $colis = Colis::where('id', $val->idcoli)->first();
            $colisprix = Prixcoli::where('id', $colis ->idprix)->first();
            $datecoli = Colidate::where('id',$colis->iddatecoli)->first();

            $created_at = Carbon::parse($test->created_at);
            $timecoli = $created_at->diffInHours(Carbon::now());
            if($timecoli >= 24){
                Colilivreur::where('idcoli',$val->idcoli)->update(['idlivreur'=>null,'idetatcoli'=>1]);
            }
            $photo = Photo::where('id',$client->idphotoprofile)->first();

            $data['photo'] = $photo->url;
	        $data['date']=$datecoli->date;
            $data['time']=$datecoli->time;
            $data['timecoli'] = $timecoli;
            $data['prixclient'] = $colisprix->prixclient;
            $data['createdat'] = $test->created_at;
            $data['timecoli'] = $timecoli;
            $data['etat'] = 'En attente de confirmation de client';
            $data['idcoli'] = $val->idcoli;
            $data['refcoli'] = $colis->refcoli;
            $data['nameclient'] = $client->name;
            $data['phone'] = $phone->phonenumber;
            $data['prix'] = $val->prixcoli;

            $datacolis[] = $data;
        endforeach;

        return response()->json($datacolis);
    }
    public function EnattenteClient(Request $request)
    {

        $datacolis = array();

        $enatenteclient = Prixnegocie::select('Prixnegocies.idcoli', 'Prixnegocies.idclient', 'Prixnegocies.idlivreur','Prixnegocies.prixcoli','Prixnegocies.created_at')->where('prixnegocies.idclient', $request->idclient)->leftJoin('colilivreurs', 'prixnegocies.idcoli', '=', 'colilivreurs.idcoli')->where('colilivreurs.idetatcoli', 1)->get();

        foreach ($enatenteclient as $val):

                $c = Colis::where('id',$val->idcoli)->first();
                $clp = Prixcoli::where('id',$c->idprix)->first();
                $livreur = User::where('id', $val->idlivreur)->first();
                $phone = Phone::where('id', $livreur->idphone)->first();
                $avis = Livreuravis::where('idlivreur',$val->idlivreur)->first();
                $nbcoliLivrer = Colilivreur::where('idlivreur',$val->idlivreur)->where('idetatcoli',5)->count();
                $nbcoliRejeter = Colilivreur::where('idlivreur',$val->idlivreur)->where('idetatcoli',4)->count();


                $created_at = Carbon::parse($val->created_at);
                $timecoli = $created_at->diffInHours(Carbon::now());
                if($timecoli >= 24){
                    Prixnegocie::where('idcoli',$val->idcoli)->delete();
                }
                if(!$avis){
                    $data['message'] = null;
                    $data['nbavis'] = null;
                    $data['clientavis'] = null;
                }else{
                    $msg = Livreuravis::where('idlivreur',$val->idlivreur)->orderBy('created_at','desc')->first();
                    $nbreavis = Livreuravis::where('idlivreur',$val->idlivreur)->count();
                    $nbavis = Livreuravis::where('idlivreur',$val->idlivreur)->sum('avis');
                    $clientavis = User::where('id',$avis->idclient)->first();
                    $data['message'] = $msg->message;
                    $data['nbavis'] = $nbavis;
                    $data['nbreClientavis'] = $nbreavis;
                    $data['clientavis'] = $clientavis->name;
                }

                //$photo = Photo::where('id',$livreur->idphotoprofile)->first();

                $data['nblivrer'] = $nbcoliLivrer;
                $data['nbrejeter'] = $nbcoliRejeter;
                $data['timecoli'] = $timecoli;
                $data['createdat'] = $val->created_at;
                $data['idlivreur'] = $val->idlivreur;
                $data['prixclient'] = $clp->prixclient;
                $data['idcoli'] = $val->idcoli;
                $data['etat'] = 'En attente de votre confirmation';
                $data['refcoli'] = $c->refcoli;
                $data['namelivreur'] = $livreur->name;
                $data['phone'] = $phone->phonenumber;
                $data['prix'] = $val->prixcoli;

            $datacolis[] = $data;
        endforeach;
        return response()->json($datacolis);
    }
    public function AccepteColiClient(Request $request)
    {
        $coli = Colis::where('id',$request->idcoli)->first();
        $historiqueColis['idclient']= $coli->iduser;
        $historiqueColis['idlivreur']= $request->idlivreur;
        $historiqueColis['idcoli']= $request->idcoli;
        $historiqueColis['information']= 'Client a Accepté ce Livreur';
        Historiquecolis::create($historiqueColis);



        $ok = Colilivreur::where('idcoli', $request->idcoli)->update(['idlivreur' => $request->idlivreur, 'idetatcoli' => 2]);
        $coli = Colis::where('id',$request->idcoli)->first();
	    $okhis = Historique::where('idcoli',$request->idcoli)->where('idlivreur',$request->idlivreur)->first();
        $ok2 = Prixcoli::where('id', $coli->idprix)->update(['prixfinal' => $request->prixfinal]);
        $solde = Portfeuille::where('idlivreur',$request->idlivreur)->first();

        if ($ok && $ok2) {
            if($okhis){
                $ok4 = Portfeuille::where('idlivreur',$request->idlivreur)->update(['soldebloque'=>$solde->soldebloque - $okhis->prixenleve]);
                $ok3 = Historique::where('idcoli',$request->idcoli)->update(['etat' =>'accepter','prixenleve'=>$okhis->prixenleve]);
                $this->envoyerNotfication($coli->iduser,$request->idcoli,$request->idlivreur,'a accepter votre proposition pour ce coli : ');
                return response()->json(['succes' => 'succes']);
            }else{
                return response()->json(['errorhis' => 'error']);
            }
        } else {
            return response()->json(['error' => 'error']);
        }
    }
    public function RefuseColiClient(Request $request)
    {
        $coli = Colis::where('id',$request->idcoli)->first();
        $historiqueColis['idclient']= $coli->iduser;
        $historiqueColis['idlivreur']= $request->idlivreur;
        $historiqueColis['idcoli']= $request->idcoli;
        $historiqueColis['information']= 'Client a refusé ce livreur';
        Historiquecolis::create($historiqueColis);

	    $ok1 = Historique::where('idcoli',$request->idcoli)->where('idlivreur',$request->idlivreur)->first();
        $solde = Portfeuille::where('idlivreur',$request->idlivreur)->first();

        if ($ok1) {

            $ok3 = Portfeuille::where('idlivreur',$request->idlivreur)->update(['solde' =>$solde->solde + $ok1->prixenleve,'soldebloque'=>$solde->soldebloque - $ok1->prixenleve]);
	        $ok2 = Historique::where('idcoli',$request->idcoli)->update(['etat' =>'annuler','prixenleve'=>'0']);
            $ok4 = Prixnegocie::where('idcoli',$request->idcoli)->where('idlivreur',$request->idlivreur)->delete();
            if($ok2 && $ok3 && $ok4){
                $this->envoyerNotfication($coli->iduser,$request->idcoli,$request->idlivreur,'a refusé votre proposition pour ce coli : ');
                return response()->json(['succes' => 'succes']);
            }else{
                return response()->json(['error' => 'error']);
            }
        } else {
            return response()->json(['error' => 'error']);
        }
    }
    public function notifierClient (Request $request){

        // idlivreur , idclient , idcoli ,message
        $data['idlivreur'] = $request->idlivreur;
        $data['idclient'] = $request->idclient;
        $data['idcoli'] = $request->idcoli;
        $coli = Colis::where('id',$request->idcoli)->first();
        $nom = User::where('id',$request->idlivreur)->first();
        $token = Userdevice::where('idusers',$request->idclient)->first();
        $data['title'] = $nom->name;
	    $data['clientdevicetoken'] = $token->devicetoken;
        if($request->message == 1){
            $message = 'je suis en route pour récupérer ce coli :'.$coli->refcoli;
            $data['message']=$message;
            $this->sendNotfication($token->devicetoken,$message,$nom->name);
        }
        elseif($request->message == 2){
            $message = 'Coli :'.$coli->refcoli.' récupéré ';
            $data['message']=$message;
            $this->sendNotfication($token->devicetoken,$message,$nom->name);
        }
        elseif($request->message == 3){
            $message = 'je suis en route vers le destinataire (coli : '.$coli->refcoli.')';
            $data['message']=$message;
            $this->sendNotfication($token->devicetoken,$message,$nom->name);
        }
        elseif($request->message == 4) {
            $message = 'Colis :'.$coli->refcoli.'livré';
            $data['message']=$message;
            $this->sendNotfication($token->devicetoken,$message,$nom->name);
        }

        $ok = Notfication::create($data);
        if($ok){
            return response()->json(['succes'=>'succes']);
        }else{
            return response()->json(['error'=>'error']);
        }

    }
    public function publieClient(Request $request)
    {
        //idclient  ,
        $datacolis = array();
        $coliparuser = Colis::where('Colis.iduser', $request->idclient)->leftJoin('colilivreurs', 'colis.id', '=', 'colilivreurs.idcoli')->whereIn('colilivreurs.idetatcoli', array(0, 1))->where('refcoli','like',$request->search . '%')
            ->get();

        foreach ($coliparuser as $val):
            $etat = Colilivreur::where('idcoli', $val->idcoli)->first();

            $infocolis = Coliinfolivraison::where('idcoli', $val->idcoli)->first();
            $envoyeur = Coliuserinfolivraison::where('id', $infocolis->idinfoenvoyeur)->first();
            $recepteur = Coliuserinfolivraison::where('id', $infocolis->idinforecepture)->first();
            $adresse1 = Adresse::where('id', $envoyeur->idadresse)->first();
            $adresse2 = Adresse::where('id', $recepteur->idadresse)->first();

            //calculate distance of coli trajet.
            $distance = $this->getdistance($adresse1->longitude, $adresse1->latitude, $adresse2->longitude, $adresse2->latitude);

            $coliprix = Prixcoli::where('id', $val->idprix)->first();
            $taillecoli = Colistaille::where('id', $val->idtaillecoli)->first();
            $typecoli = Typecoli::where('id', $val->idtype)->first();
            $datecoli = Colidate::where('id',$val->iddatecoli)->first();

            $datacolisinfo['etat'] = $etat->idetatcoli;
            $datacolisinfo['distance'] = $distance;
            $datacolisinfo['idcolis'] = $val->idcoli;
            $datacolisinfo['reference'] = $val->refcoli;
            $datacolisinfo['description'] = $val->description;
            $datacolisinfo['prix'] = $coliprix->prixclient;
            $datacolisinfo['taille'] = $taillecoli->taille;
            $datacolisinfo['type'] = $typecoli->type;
            //$datacolisinfo['statut']=$val->status;
            $datacolisinfo['date']=$datecoli->date;
            $datacolisinfo['time']=$datecoli->time;

            $datacolis[] = $datacolisinfo;

        endforeach;

        return response()->json($datacolis);
    }
    public function historiqueAffichage(Request $request){

        $data = array();
        $his = Historique::where('idlivreur',$request->idlivreur)->orderBy('updated_at','desc')->get();

        foreach ($his as $item) :

            $colis = Colis::where('id',$item->idcoli)->first();

            $hisData['ref'] = $colis->refcoli;
            $hisData['prixenleve'] = $item->prixenleve;
            $hisData['etat'] = $item->etat;

            $data[] = $hisData;
        endforeach;

        return response()->json($data);
    }
    public function getportfeuille (Request $request){

        $por = Portfeuille::where('idlivreur',$request->idlivreur)->first();
        $nom = User::where('id',$request->idlivreur)->first();

        $data['nom'] = $nom->name;
        $data['solde'] = $por->solde;
        $data['soldebloque'] = $por->soldebloque;

        return response()->json($data);
    }
    public function donnerAvis(Request $request){

        $avisdata['idlivreur']=$request->idlivreur;
        $avisdata['idclient']=$request->idclient;
        $avisdata['idcoli']=$request->idcoli;
        $avisdata['message']=$request->message;
        $avisdata['avis']=$request->avis;

        $ok = Livreuravis::create($avisdata);
        if($ok){
            $this->envoyerNotfication($request->idclient,$request->idcoli,$request->idlivreur,'Mon Avis : '.$request->message);
            return response()->json(['succes'=>'succes']);
        }else{
            return response()->json(['error'=>'error']);
        }
    }
    public function republierClient(Request $request){

        $ok = Colilivreur::where('idcoli',$request->idcoli)->update(['idetatcoli' => 1]);
        if($ok){
            return response()->json(['succes'=>'succes']);
        }else{
            return response()->json(['error'=>'error']);
        }
    }
    public function supprimerColiClient(Request $request){

        $coli = Colis::where('id',$request->idcoli)->first();
        $historiqueColis['idclient']= $coli->iduser;
        $historiqueColis['idcoli']= $request->idcoli;
        $historiqueColis['information']= 'Client a Supprimer ce coli';
        $test = Historiquecolis::create($historiqueColis);

        $ok = Colis::where('id',$request->idcoli)->delete();
        $ok2 = Colilivreur::where('idcoli',$request->idcoli)->delete();

        if($ok && $ok2 && $test){
            return response()->json(['succes'=>'succes']);
        }else{
            return response()->json(['error'=>'error']);
        }
    }
    public function problemClient(Request $request){

        $problemData['idlcient'] = $request->idclient;
        $problemData['idlivreur'] = $request->idlivreur;
        $problemData['idcoli'] = $request->idcoli;
        $problemData['message'] = $request->message;
        $problemData['envoyerdepuis'] = 'client';
        $problemData['distinateur'] = 'admin';
        $ok = Problem::create($problemData);

        if($ok){
            return response()->json(['succes'=>'succes']);
        }else{
            return response()->json(['error'=>'error']);
        }

    }

    public function shownotiClient(Request $request){
        $data = Array();
	$token = Userdevice::where('idusers',$request->idclient)->first();
        $not = Notfication::where('clientdevicetoken',$token->devicetoken)->orderBy('created_at','desc')->get();
           foreach ($not as $val):

               $notdata['title'] = $val->title;
               $notdata['message'] = $val->message;
               $notdata['temp'] = $val->created_at;

               $data[]=$notdata;
           endforeach;

       return response()->json($data);

    }

    public function shownotiLivreur(Request $request){
        $data = Array();
        $token = Userdevice::where('idusers',$request->idlivreur)->first();
        $not = Notfication::where('clientdevicetoken',$token->devicetoken)->orderBy('created_at','desc')->get();
        foreach ($not as $val):

            $notdata['title'] = $val->title;
            $notdata['message'] = $val->message;
            $notdata['temp'] = $val->created_at;

            $data[]=$notdata;
        endforeach;

        return response()->json($data);

    }

}
