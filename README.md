=== Plugin Name ===  
Contributors: (this should be a list of wordpress.org userid's)  
Donate link: https://cobianzo.com  
Tags: PWA, generate files, developer, service worker, manifest
Requires at least: 3.0.1  
Tested up to: 5.5    
Stable tag: 5.5  
License: GPLv2 or later  
License URI: http://www.gnu.org/licenses/gpl-2.0.html  

Based on https://github.com/DevinVinson/WordPress-Plugin-Boilerplate  

This plugin is meant to create the manifest.json and service-worker.js files in the root of your wp project, automatically.  
It has been developed in an environment with Polylang and assuming that the languages Italian and English are installed.		

# Edit as developer

## `/admin/class-pwa-generator-admin.php`: 
This is the file that you want to edit!

Edit the properties (mainly the languages) of the class:
```
		$this->languages_defined = ['it' => 'Italian', 'en' => 'English'];
		$this->manifest_en_file = 'manifest-en.json';
		$this->manifest_it_file = 'manifest-it.json';
		$this->manifest_source_relpath =  'src/manifest-template-to-json.php';
		$this->sw_file = 'sw-balconi.js';
		$this->sw_source_relpath = 'src/js/service-worker/sw-template-to-js.php';

```
- **Note** that lately the assetslinks fiel config has been added.  

Then create the files  
- 'manifest-template-to-json.php' (you can use the template `/admin/partials/demo/manifest-template-to-json.example.php`)
- 'sw-template-to-js.php' (you can use the template `/admin/partials/demo/sw-template-to-js.example.php`)
 in the right directory, according to the values above.  
Create your own manifest template with your own values. In my example, I'm using polylang functions, that's why i use manifest_it and manifest_en propierties, you probably dont need it.

# Run the plugin
To generate the files based on your settings, go to the Menu PWAGenerate.  
Follow the instructions of the buttons. They provide information about the `shell` commands to setup initially the files.  

# Core of the plugin
-  `pwa-generator` : single entry file of the plugin. 
- `includes/class-pwa-generator.php` - start here to see what else is called and initialized

~~# To start a new plugin, replace:  
- All files names: `pwa-generator` by `your-slug`  
- Look fot all the occurrences of `pwa-generator`, `pwa_generator` and `pwa generator`  
    - and do the corresponding search replace case sensitive~~		
