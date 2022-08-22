<?php

namespace App\Http\Livewire;

use Exception;

class DynamicLinkSettingsLivewire extends BaseLivewireComponent
{

    // App settings
    public $prefix;
    public $android;
    public $ios;


    public $rules = [
        "prefix" => "required|url"
    ];

    
    public function mount()
    {
        $this->prefix = setting('dynamic_link.prefix');
        $this->android = setting('dynamic_link.android');
        $this->ios = setting('dynamic_link.ios');
    }

    public function render()
    {
        $this->mount();
        return view('livewire.settings.dynamic-link-settings');
    }


    public function saveSettings()
    {


        try {

            $this->isDemo();
            $appSettings = [
                'dynamic_link.prefix' =>  $this->prefix,
                'dynamic_link.android' =>  $this->android,
                'dynamic_link.ios' =>  $this->ios,
            ];

            // update the site name
            setting($appSettings)->save();



            $this->showSuccessAlert(__("Dynamic Link Settings saved successfully!"));
            $this->reset();
        } catch (Exception $error) {
            logger("error", [$error]);
            $this->showErrorAlert($error->getMessage() ?? __("Dynamic Link Settings save failed!"));
        }
    }
}
