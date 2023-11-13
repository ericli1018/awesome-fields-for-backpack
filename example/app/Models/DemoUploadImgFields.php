<?php

namespace App\Models;

use Ericli1018\AwesomeFieldsForBackpack\Models\Traits\HasUploadImgFields;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemoUploadImgFields extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasUploadImgFields; // add this

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'demo_uploadImgFields';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];

    /*
     * case field to array type
     * protected $casts = ['{field name}' => 'array'];
     * value json format [['file_path' => '{file path}', 'comment' => '{comment}', 'is_selected' => {is selected}, 'fn' => {file name}], ...]
     * {file path} = {disk}/{file path}, ex: disk=public, storage/app/public/{file path}
     * {is selected} = true | false
     * {file name} = original name
     */
    protected $casts = ['photos' => 'array']; // add this

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /**
     * static function boot
     * Add this function and adjust Storage::disk('{disk label}') 
     * and $obj-{field name} to the corresponding parameters
     * @param $value
     */
    public static function boot()
    {
        parent::boot();
        
        static::deleting(function($obj) {
            if (count((array)$obj->photos)) {
                foreach ($obj->photos as $item) {
                    \Storage::disk('public')->delete($item['file_path']);
                }
            }
        });
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
    
    /**
     * function set<field name>Attribute
     * The name of this function changes with the field name.
     * @param $value
     */
    public function setPhotosAttribute($value)
    {
        $attribute_name = "photos"; // field name
        $disk = "public"; // storage/app/public/
        $destination_path = "demo_upload_img_filed"; // storage/app/public/demo_upload_img_filed

        $this->uploadImgMultipleFilesToDisk($value, $attribute_name, $disk, $destination_path);
    }
}
