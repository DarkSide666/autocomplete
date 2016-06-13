#AutoComplete field add-on for Agile Toolkit (ATK4)

This will replace standard drop-down field with an auto-complete field.

![Screenshot](/doc/screenshot.png)


## Requirement

 - PHP >=5.4
 - ATK4 >=4.3

## Installing via Composer

The recommended way to install this add-on is through Composer.

```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php
```

Next, update your project's composer.json file to include AutoComplete:

```json
{
    "require": {
        "darkside666/autocomplete": "dev-master"
    }
}
```

## Usage

In your Frontend->init() add following lines:

```php
    // allow add-ons to reside in /vendor folder
    $this->addLocation(['addons' => ['../vendor']])
        ->setBasePath($this->pathfinder->base_location->getPath());

    // HACK: force call Initiator of all used add-ons :)
    foreach (['darkside666/autocomplete'] as $addon) {
        $this->add("$addon\Initiator");
    }
```

And then you're ready to use this add-on like this:

```php
    // In model
    $model->hasOne('User')->display(['form'=>'darkside666/autocomplete/Basic']);

    // Or directly in form
    $field = $form->addField('darkside666/autocomplete/Basic');
    $field->setModel('User');
```
