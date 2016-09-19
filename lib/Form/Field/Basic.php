<?php
/**
 * Addon  for converting hasOne field into auto-complete
 */
namespace darkside666\autocomplete;

class Form_Field_Basic extends \Form_Field_Hidden
{
    // You can find all available options here: http://jqueryui.com/demos/autocomplete/
    public $options = array('mustMatch'=>true);

    // Limits resultset
    public $limit_rows = 20;

    // Minimum characters you have to enter to make autocomplete ajax call
    public $min_length = 3;

    // Hint text. If empty/null, then hint will not be shown.
    public $hint = 'Please enter at least %s symbols. Search results will be limited to %s records.';

    // show as hint or placeholder
    public $hint_show_as = 'placeholder'; // hint|placeholder

    // Text input field object
    public $other_field;

    // Model ID field and title field names
    protected $id_field;
    protected $title_field;
    protected $search_field;



    public function init()
    {
        parent::init();

        // add add-on locations to pathfinder
        /*
        $l = $this->app->locate('addons', __NAMESPACE__, 'location');
        $addon_location = $this->app->locate('addons', __NAMESPACE__);
        $this->app->pathfinder->addLocation($addon_location, array(
            'js'  => 'public/js',
            'css' => 'public/css',
        ))->setParent($l);
        */

        /* this way it works but looks very cryptic
        $l = $this->app->locate('addons', __NAMESPACE__);
        $this->app->pathfinder->public_location
            ->addRelativeLocation('../'.$l, ['public'=>'public','js'=>'public/js','css'=>'public/css']);
        */

        // add additional form field
        $name = preg_replace('/_id$/', '', $this->short_name);
        $caption = null;
        if ($this->owner->model) {
            if ($f = $this->owner->model->getElement($this->short_name)) {
                if ($this->owner->model instanceof \atk4\data\Model) {
                    $caption = isset($f->ui['caption']) ? $f->ui['caption'] : null;
                } else {
                    $caption = $f->caption();
                }
            }
        }
        $this->other_field = $this->owner->addField('line', $name, $caption);
        if ($this->hint) {
            $text = sprintf($this->hint, $this->min_length, $this->limit_rows);
            if ($this->hint_show_as=='placeholder') {
                $this->other_field->setAttr('placeholder', $text);
            } elseif ($this->hint_show_as=='hint') {
                $this->other_field->setFieldHint($text);
            }
        }

        // move hidden ID field after other field. Otherwise it breaks :first->child CSS in forms
        $this->js(true)->appendTo($this->other_field->js()->parent());

        // Set default options
        if ($this->min_length) {
            $this->options['minLength'] = $this->min_length;
        }
    }

    public function setTitleField($title_field)
    {
        $this->title_field = $title_field;
        return $this;
    }

    public function setCaption($_caption)
    {
        $this->caption = $this->other_field->caption = $this->app->_($_caption);
        return $this;
    }

    public function mustMatch()
    {
        $this->options = array_merge($this->options, array('mustMatch'=>'true'));
        return $this;
    }

    public function validateNotNULL($msg = null)
    {
        $this->other_field->validateNotNULL($msg);
        return $this;
    }

    public function addCondition($q)
    {
        $this->model->addCondition($this->search_field ?: $this->title_field, 'like', '%'.$q.'%'); // add condition
        /*
        $this->model->addCondition(
            $this->model->dsql()->orExpr()
                ->where($this->model->getElement( $this->title_field), 'like', '%'.$q.'%')
                ->where($this->model->getElement( $this->id_field), 'like', $this->model->dsql()->getElement('id','test'))
        )->debug();
        */
        if ($this->model instanceof \atk4\data\Model) {
            $this->model->setOrder($this->title_field);
            if ($this->limit_rows) {
                $this->model->setLimit($this->limit_rows);
            }
        } elseif ($this->model->controller) {
            if ($this->model->controller->supportOrder) {
                $this->model->setOrder($this->title_field); // order ascending by title field
            }
            if ($this->model->controller->supportLimit && $this->limit_rows) {
                $this->model->setLimit($this->limit_rows); // limit resultset
            }
        }

        return $this;
    }

    public function setOptions($options = array())
    {
        $this->options = $options;
        return $this; //maintain chain
    }

    public function getData()
    {
        if ($this->model instanceof \atk4\data\Model) {
            return $this->model->export([$this->id_field, $this->title_field]);
        } else {
            return $this->model->getRows(array($this->id_field, $this->title_field));
        }
    }

    public function setValueList($data)
    {
        $m = $this->add('Model');
        $m->setSource('Array', $data);
        $this->setModel($m);

        return $this;
    }

    public function getValueList()
    {
        return $this->getData();
    }

    public function setModel($m, $id_field = null, $title_field = null)
    {
        parent::setModel($m);

        $this->id_field = $id_field ?: $this->model->id_field;
        $this->title_field = $title_field ?: $this->model->title_field;

        if ($_GET[$this->name]) {

            if ($_GET['term']) {
                $this->addCondition($_GET['term']);
            }

            // retrieve data from model
            $data = $this->getData();

            // cast values to string
            foreach ($data as &$row) {
                $row[$this->id_field] = (string)$row[$this->id_field];
            }

            echo json_encode($data);
            exit;
        }
    }

    public function render()
    {
        $url = $this->app->url(null, array($this->name => 'ajax'));
        if ($this->value) { // on add new and inserting allow empty start value
            $this->model->tryLoad($this->value);
            $name = $this->model->get($this->title_field);
            $this->other_field->set($name);
        }

        $this->other_field->js(true)
            ->_load('autocomplete_univ')
            ->_css('autocomplete')
            ->univ()
            ->myautocomplete($url, $this, $this->options, $this->id_field, $this->title_field);

        return parent::render();
    }
}
