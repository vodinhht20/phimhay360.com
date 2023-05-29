<?php

namespace Backpack\Settings\app\Http\Controllers;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\Settings\app\Models\Setting;
use Illuminate\Support\Facades\Route;
use Prologue\Alerts\Facades\Alert;

class SettingCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel("Backpack\Settings\app\Models\Setting");
        CRUD::setEntityNameStrings(trans('backpack::settings.setting_singular'), trans('backpack::settings.setting_plural'));
        CRUD::setRoute(backpack_url(config('backpack.settings.route')));
    }

    /**
     * Define which routes are needed for this operation.
     *
     * @param  string  $name  Name of the current entity (singular). Used as first URL segment.
     * @param  string  $routeName  Prefix of the route name.
     * @param  string  $controller  Name of the current CrudController.
     */
    protected function setupUpdateRoutes($segment, $routeName, $controller)
    {
        Route::get($segment . '/{id}/edit', [
            'as'        => $routeName . '.edit',
            'uses'      => $controller . '@edit',
            'operation' => 'update',
        ]);

        Route::put($segment . '/{id}', [
            'as'        => $routeName . '.update',
            'uses'      => $controller . '@update',
            'operation' => 'update',
        ]);

        Route::get($segment . '/key/{key}/edit', [
            'as'        => $routeName . '.editKey',
            'uses'      => $controller . '@editKey',
            'operation' => 'updateKey',
        ]);

        Route::put($segment . '/key/{key}', [
            'as'        => $routeName . '.updateKey',
            'uses'      => $controller . '@updateKey',
            'operation' => 'updateKey',
        ]);

        Route::get($segment . '/group/{group}/edit', [
            'as'        => $routeName . '.editGroup',
            'uses'      => $controller . '@editGroup',
            'operation' => 'updateGroup',
        ]);

        Route::put($segment . '/group/{group}', [
            'as'        => $routeName . '.updateGroup',
            'uses'      => $controller . '@updateGroup',
            'operation' => 'updateGroup',
        ]);
    }


    public function setupListOperation()
    {
        CRUD::addClause('where', 'active', 1);

        $this->crud->addFilter(
            [
                'type'  => 'select',
                'name'  => 'group',
                'label' => 'Phân loại',
            ],
            Setting::where('active', true)->pluck('group')->unique()->values()->toArray(),
            function ($val) {
                $this->crud->addClause('where', 'group', $val);
                // $this->crud->query = $this->crud->query->where('draft', '1');
            }
        );

        // columns to show in the table view
        CRUD::setColumns([
            [
                'name'  => 'name',
                'label' => trans('backpack::settings.name'),
            ],
            [
                'name'  => 'value',
                'label' => trans('backpack::settings.value'),
            ],
            [
                'name'  => 'key',
                'label' => trans('key'),
            ],
        ]);
    }

    public function setupUpdateOperation()
    {
        CRUD::addField([
            'name'       => 'name',
            'label'      => trans('backpack::settings.name'),
            'type'       => 'text',
            'attributes' => [
                'disabled' => 'disabled',
            ],
        ]);

        CRUD::addField(json_decode(CRUD::getCurrentEntry()->field, true));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function editKey($key)
    {
        $this->crud->hasAccessOrFail('update');

        $this->data['entry'] = Setting::where('key', $key)->first();

        if (is_null($this->data['entry'])) {
            Alert::error("Xảy ra lỗi hệ thống, không tìm thấy {$key}, vui lòng cài đặt lại.")->flash();
            return back();
        }

        $field = array_merge(json_decode($this->data['entry']['field'], true), ['label' => '']);
        $this->crud->setOperationSetting('fields', [$field]);

        $this->data['crud'] = $this->crud;
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.edit') . ' ' . $this->crud->entity_name;

        return view('settings::key', $this->data);
    }

    /**
     * Update the specified resource in the database.
     *
     * @return array|\Illuminate\Http\RedirectResponse
     */
    public function updatekey($id)
    {
        $this->crud->hasAccessOrFail('update');

        $request = $this->crud->validateRequest();

        $this->crud->registerFieldEvents();

        Setting::where('id', $id)->update([
            'value' => $request->value
        ]);

        Alert::success(trans('backpack::crud.update_success'))->flash();

        return back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function editGroup($group)
    {
        $this->crud->hasAccessOrFail('update');

        $settings = Setting::where('group', $group)->get();

        $fields = [];
        foreach ($settings as $setting) {
            $fields[] = array_merge(json_decode($setting->field, true), ['label' => $setting->name, 'name' => $setting->key, 'value' => $setting->value]);
        }

        $this->crud->setOperationSetting('fields', $fields);
        $this->data['entry'] = $this->crud->getEntryWithLocale($setting->id);

        $this->data['crud'] = $this->crud;
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.edit') . ' ' . $this->crud->entity_name;
        $this->crud->enableTabs();

        return view('settings::group', $this->data);
    }

    /**
     * Update the specified resource in the database.
     *
     * @return array|\Illuminate\Http\RedirectResponse
     */
    public function updateGroup($group)
    {
        $this->crud->hasAccessOrFail('update');

        $request = $this->crud->validateRequest();

        $this->crud->registerFieldEvents();

        $settings = Setting::where('group', $group)->get();

        foreach ($settings as $setting) {
            $setting->update([
                'value' => $request[$setting->key]
            ]);
        }

        Alert::success(trans('backpack::crud.update_success'))->flash();

        return back();
    }
}
