<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\XlsformRequest;
use App\Jobs\CreateDraftFormOnOdkCentral;
use App\Jobs\CreateProjectOnOdkCentral;
use App\Models\Xlsform;
use Illuminate\Support\Facades\Storage;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class FormCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class XlsformCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Xlsform::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/xlsform');
        CRUD::setEntityNameStrings('form', 'forms');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('project')->type('relationship');
        CRUD::column('title')->label('Form Title');
        CRUD::column('status')->label('Form Status');
        CRUD::column('xlsfile')->wrapper(['href' => function ($crud, $column, $entry, $key) {
            return Storage::url($entry->xlsfile);
        }]);

        CRUD::button('deploy')->type('view')->stack('line')->view('backpack::crud.buttons.deploy');
    }
    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(XlsformRequest::class);

        CRUD::field('project_id')->type('relationship')->label('Select project for the new form');
        CRUD::field('user_id')->type('hidden')->default(Auth::id());

        CRUD::field('title')->label('Enter form title');

        CRUD::field('themes')->type('themes');
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function deploy(Xlsform $xlsform)
    {
        CreateDraftFormOnOdkCentral::dispatchSync($xlsform);

        return response('', 200);
    }
}
