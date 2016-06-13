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

```php
    // In model
    $model->hasOne('User')->display(['form'=>'darkside666/autocomplete/Basic']);

    // Or directly in form
    $field = $form->addField('darkside666/autocomplete/Basic');
    $field->setModel('User');
```
