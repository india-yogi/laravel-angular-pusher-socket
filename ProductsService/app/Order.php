<?php namespace App;
 
use Illuminate\Database\Eloquent\Model;
 
class Order extends Model
{ 
   protected $table = 'orders';

   protected $fillable = [
                'client_id', 
                'user_id', 
                'type_id',
                'model_id', 
                'supplier_id', 
                'model_name', 
                'quantity',
                'requested_quantity',
                'comment',
                'po_number',
                'status'
               ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];  

   
}