CrudViews plugin for CakePHP
############################

Installation
============

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

``composer require your-name-here/CrudViews`` // what? I've never done this

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

Bootstrap changes
~~~~~~~~~~~~~~~~~~~~~

Since there are several places where plugin files need to be extended or loaded 
you'll need to use the autoload option:

.. code:: php

	// app/config/bootstrap.php
	Plugin::load('CrudViews', ['autoload' => true]);

.. _required-app-controller:

AppController changes
~~~~~~~~~~~~~~~~~~~~~~~~~

The plugin's ``AppController`` needs to do some ``beforeRender()`` work so your 
``AppController`` should extend it and implement ``beforeRender()`` also:

.. code:: php

	namespace App\Controller;
	use CrudViews\Controller\AppController as BaseController;

	class AppController extends BaseController {

		/**
		 * Pass this call through to CrudView plugin
		 */
		public function beforeFilter(Event $event) {
			parent::beforeFilter();
			// do whatever else you want
		}
	
		/**
		 * Pass this call through to the CrudView plugin
		 * 
		 * @param Event $event
		 */
		public function beforeRender(Event $event) {
			parent::beforeRender($event);
			// do whatever else you want
		}
	
	// all your other AppController code
	
	}

If you want to use CrudViews to make the four standard views in all your 
controllers dynamic you just have to add one more thing to *AppContoller*

.. code:: php

	// in the initialize() method 
	$this->connectCrudViews('all');


STUFF TO REWRITE ----------------------------------
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

##Using CrudData

Once a specific CrudData object is selected, you can get the AssociationCollection 
for it or you can get a specific named association. To get the full collection:

```php
$associations = $this->helper->CrudData->associationCollection();
```

To get a specific Association object:

```php
$this->helper->CrudData->associationCollection('projects');
```

This object can run proxy calls to the associated Table object like this:

```php
$this->helper->CrudData->associationCollection('projects')->displayField();
```

##Access to Associated Data in the Parent Entity

Calls to `CrudHelper::output($field)` will eventually output `$this->entity->$field` 
where `entity` is the Entity for the current CrudData object. This object may have 
associated data included and there may be situations where you want to include 
some of the associated data fields in the output of the parent. For example a 
parent's foreign key would typically be replaced with the `displayField` which 
would be found in the contained Entity. 

The since there is no way for `$field` to carry enough information for 
`CrudHelper::output()` or the Decorators that wrap the decorator to drill down 
to these deeper levels of the parent entity, in the cases where you want a call
to accomplish this you'll need to do something like this:

```php
// psuedo code

if ( $this->needDeeperData() ) {
    $association_entity = $this->getAssociationEntityName(); // lowercase singular of the association alias
    $original_entity = clone $this->Crud->entity; // Crud is your instance of CrudHelper
    $this->Crud->entity = $original_entity->$association_entity;

    // now your ready for your call. Adjust the value of $field if necessary
    $this->Crud->output($field);

    // now switch back to the original entity
    $this->Crud->entity = clone $original_entity;
} else {
    $this->Crud->output($field);
}
```
NOTE TO SELF ===================

A helper method that handles this entity swapping would be ideal

================================
