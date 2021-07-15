<?php

namespace App\Models;

use App\Models\Traits\HasUploadFields;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Xlsform extends Model
{
    use CrudTrait, HasUploadFields;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'xlsforms';
    protected $guarded = ['id'];

    public function setXlsfileAttribute($value)
    {
        $this->uploadFileWithNames($value, 'xlsfile', 'public', 'forms');
    }


    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
