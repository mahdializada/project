<?php

namespace App\Repositories\Advertisement\AdvertisementTabs;

use App\Models\Advertisement\BankAccount;
use App\Repositories\Repository;
//use Your Model

/**
 * Class BankAccountRepository.
 */
class BankAccountRepository extends Repository
{

    public function read($id){
        return BankAccount::with('user')->where('add_account_id',$id)->orderBy('created_at','Desc')->get();
    }
   
    
    public function storeRepository($request){
       return  BankAccount::create($request->all());
    }
    public function storerole($request){
        return $request->validate([
            'card_number'=>['required','min:15'],
            'owner'=>['required','min:3'],
            'bank_name'=>['required','min:3'],
            'add_account_id'=>'required',
            'user_id'=>'required'
        ]);
    }

    public function isUpdate($id, $request){
        $id1=BankAccount::find($id);
        $id1=$id1->update($request->all());
        return $id1;

    }
    public function deleted($id){
       $del= BankAccount::find($id);
       $del->delete();
       return $del;
    }
}
