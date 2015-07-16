# CrudViews plugin for CakePHP

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require your-name-here/CrudViews
```

### Hooking the plugin into your app

Once the files are installed you'll need to make some changes to your app to get everything connected.

* bootstrap changes
* AppController changes
* Choosing the dynamic CRUD views in a controller

#### Bootstrap changes

If you're not using ```Plugin::loadAll();``` in your config/bootstrap.php file, you'll need to add:

```
Plugin::load('CrudViews', ['autoload' => true]);
```
