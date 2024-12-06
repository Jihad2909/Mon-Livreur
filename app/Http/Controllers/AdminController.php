<?php

namespace App\Http\Controllers;

use App\Models\Adresse;
use App\Models\Colilivreur;
use App\Models\Colis;
use App\Models\Colistaille;
use App\Models\Phone;
use App\Models\Typecoli;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;


class AdminController extends Controller
{
    public function dashboard(){
        return view('welcome');
    }
    public function getEtatColis(){

        $etat1 = Colilivreur::where('idetatcoli',1)->count();
        $etat2 = Colilivreur::where('idetatcoli',2)->count();
        $etat3 = Colilivreur::where('idetatcoli',3)->count();
        $etat4 = Colilivreur::where('idetatcoli',4)->count();
        $etat5 = Colilivreur::where('idetatcoli',5)->count();
        $etat6 = Colilivreur::where('idetatcoli',6)->count();

        $data = [
            'etat1'=>$etat1,
            'etat2'=>$etat2,
            'etat3'=>$etat3,
            'etat4'=>$etat4,
            'etat5'=>$etat5,
            'etat6'=>$etat6,
        ];

        return view('colis')->with('data',$data);
    }
    public function getLivreurs(){

        $data = Array();

        $livreurs = User::where('idusertype',2)->get();
        //$phone = Phone::where('id',$livreurs->idphone)->first();
        //$adresse = Adresse::where('id',$livreurs->idadresse)->first();
        foreach ($livreurs as $liv):
            $phone = Phone::where('id',$liv->idphone)->first();
            $adresse = Adresse::where('id',$liv->idadresse)->first();

            $data['id']=$liv->id;
            $data['name']=$liv->name;
            $data['email']=$liv->email;
            $data['phone']=$phone->phonenumber;
            $data['adresse']=$adresse->place;
            $data['status']=$liv->status;

            $dataLiv[] = $data;
        endforeach;

        return view('livreur')->with('liv',$dataLiv);
    }
    public function getClients(){

        $data = Array();

        $client = User::where('idusertype',1)->get();
        foreach ($client as $cl):
            $phone = Phone::where('id',$cl->idphone)->first();
            $adresse = Adresse::where('id',$cl->idadresse)->first();

            $data['id']=$cl->id;
            $data['name']=$cl->name;
            $data['email']=$cl->email;
            $data['phone']=$phone->phonenumber;
            $data['adresse']=$adresse->place;
            $data['status']=$cl->status;

            $datacl[] = $data;
        endforeach;

        return view('client')->with('cl',$datacl);
    }
    public function getDetailEtatColi(String $id){

        $colietat = Colilivreur::where('idetatcoli',$id)->first();

        if (!$colietat){
            return Redirect::to('coliNotFound');
        }else{
        $datacoli = array();

        $colietat = Colilivreur::where('idetatcoli',$id)->get();
        foreach ($colietat as $coli):
            $userliv = User::where('id',$coli->idlivreur)->first();
            $colich = Colis::where('id',$coli->idcoli)->first();
            $usercl = User::where('id',$colich->iduser)->first();
            $taille  = Colistaille::where('id',$colich->idtaillecoli)->first();
            $type  = Typecoli::where('id',$colich->idtype)->first();

            $datacoli['id']=$coli->idcoli;
            $datacoli['description']=$colich->description;
            $datacoli['reference']=$colich->refcoli;
            $datacoli['nameliv']=$userliv->name;
            $datacoli['emailliv']=$userliv->email;
            $datacoli['namecl']=$usercl->name;
            $datacoli['emailcl']=$usercl->email;
            //$datacoli['taille']= $taille->taille == null ? 'null' : $taille->taille;
            $datacoli['type']=$type->type;

            $data[] = $datacoli;
        endforeach;

        return view('coliDetails')->with('data',$data);
    }
    }

    public function show(String $id){


        $user = User::where('id',$id)->first();
        $phone = Phone::where('id',$user->idphone)->first();
        $adresse = Adresse::where('id',$user->idadresse)->first();

        $data = [
            'id'=>$id,
            'name'=>$user->name,
            'email'=>$user->email,
            'phone'=>$phone->phonenumber,
            'place'=>$adresse->place,
            'status'=>$user->status,
        ];

        //permis et les autres
        return response()->json($data);
    }

    public function check(String $id){

        $user = User::where('id',$id)->update(['status'=>'1']);
       if ($user){
           return response()->json(['succes'=>'succes']);
       }else{
           return response()->json(['error'=>'error']);
       }

    }
    public function uncheck(String $id){

        $user = User::where('id',$id)->update(['status'=>'0']);
       if ($user){
           return response()->json(['succes'=>'succes']);
       }else{
           return response()->json(['error'=>'error']);
       }

    }

}
