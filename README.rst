
    # Is used to denote page titles.
    = Is used for sections in a page.
    - Is used for subsections.
    ~ Is used for sub-subsections
    ^ Is used for sub-sub-sections.
CrudViews plugin for CakePHP
############################

Installation
============

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require your-name-here/CrudViews
```

Hooking the plugin into your app
--------------------------------

Once the files are installed you'll need to make some changes to your app to get 
everything connected. You'll have some options here so a little information 
about the use of the plugin will help you decide how to proceed.

There is no automatic use of the dynamic Crud views after the plugin is installed. 
You can tune all your controllers to use all the dynamic views or you can 
cherry-pick the cases where you want the dynamic view features. 

You may also choose to drive much of your view logic through the CrudViews plugin 
with the trade-off of making your view code more abstract. This will require more 
discipline to use but it can greatly reduce your view code and logic while 
improving consistency in your pages and your user interface.

So you have three possible levels of use:

1. Limited cherry-picked use
    * Choose optional {your}Controller setup
2. General use to replaced baked Crud views
    * Choose optional AppController setup
3. Advanced use to help standardize your views throughout your app
    * Choose optional AppController setup

First let's cover the code required no matter what your use plans are.

* bootstrap changes
* AppController changes (required)

**Bootstrap changes**
~~~~~~~~~~~~~~~~~~~~~

Since there are several places where plugin files need to be extended or loaded 
you'll need to use the autoload option::

 // your_app/config/bootstrap.php
 Plugin::load('CrudViews', ['autoload' => true]);

.. _app-controller:

The App Controller
==================

As stated in the introduction, the ``AppController`` class is the
parent class to all of your application's controllers.
``AppController`` itself extends the :php:class:`Controller` class included in the
CakePHP core library. ``AppController`` is defined in
``/app/Controller/AppController.php`` as follows::

    class AppController extends Controller {
    }

**AppController changes**
~~~~~~~~~~~~~~~~~~~~~~~~~

The plugin's ``AppController` needs to do some ``beforeRender()`` work so your 
``AppController`` should extend it and implement ``beforeRender()`` also::

	namespace App\Controller;
	use CrudViews\Controller\AppController as BaseController;

	class AppController extends BaseController {
	
		/**
		 * Pass this call through to the CrudView plugin
		 * 
		 * CrudView depends on this call to do important CrudHelper configuration
		 * 
		 * @param Event $event
		 */
		public function beforeRender(Event $event) {
			parent::beforeRender($event);
			// do whatever else you want
		}
	
	// all your other AppController code
	
	}


* AppController changes (optional)
* {your}Controller changes (the option to AppController changes)
* Choosing the dynamic CRUD views in a controller (required {your}Controller changes)

This is not strictly necessary. The idea here is that you're going to want the new dynamic CRUD views available across all controllers. This change will load the pivotal CrudHelper for all controllers.

Before the declaration of the AppController class, with your other ```use``` statements:

```
use CrudViews\Controller\AppController as BaseController;
```

Then change the class declaration and $helper property value

```
class AppController extends BaseController {

	public $helpers = ['CrudViews.Crud']; // add this to your list of other helpers
	
	/**
	 * Pass this call through to the CrudView plugin
	 * 
	 * CrudView depends on this call to do important helper configuration
	 * 
	 * @param Event $event
	 */
	public function beforeRender(Event $event) {
		parent::beforeRender($event);
	}

	// all your other AppController code

}
```

