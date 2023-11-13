<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DemoUploadImgFieldsRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class DemoUploadImgFieldCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DemoUploadImgFieldsCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    //use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ReorderOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\DemoUploadImgFields::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/demo-UploadImgFields');
        CRUD::setEntityNameStrings('demo UploadImgFields', 'demo UploadImgFields');
    }

    protected function setupReorderOperation()
    {
        CRUD::set('reorder.label', 'name');
        CRUD::set('reorder.max_level', 1);
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        if (! $this->crud->getRequest()->has('order')){
            $this->crud->orderBy('lft', 'asc')->orderBy('id', 'desc');
        }

        CRUD::column('name')->label('Name')->type('textarea_nl2br')->escaped(false)->searchLogic('text');

        //CRUD::setFromDb(); // set columns from db columns.

        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(DemoCatalogsRequest::class);
        //CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */
        
        CRUD::addFields([
            [
                'name' => 'name',
                'lable' => 'Name',
                'type' => 'key_val_multiple',
            ],
            [
                'name'    => 'photos', // field name
                'label'   => 'Photos', // label name
                'type'    => 'upload_img_multiple',
                'disk'    => 'public', // disk, ex: disk=public, storage/app/public/*
                'hint'    => '',
                'qty'     => 0, // 0=no limit, >0=limit
                'showSingleChoise' => '1', // 0=hidden, 1=show(default)
                'showComment' => '1', // 0=hidden, 1=show(default)
            ],
        ]);
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
}
