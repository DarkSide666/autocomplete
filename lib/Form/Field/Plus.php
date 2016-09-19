<?php
namespace darkside666\autocomplete;

class Form_Field_Plus extends Form_Field_Basic
{
    /** @var string form class name */
    public $form_class = 'Form';

    /** @var string form title */
    public $form_title = 'Add New Record';

    public function setModel($model)
    {
        parent::setModel($model);
        $self = $this;

        $f = $this->other_field;

        // Add buttonset to name field
        $bs = $f->afterField()->add('ButtonSet');

        // Add button - open dialog for adding new element
        $bs->add('Button')
            ->set('+')
            ->add('VirtualPage')
            ->bindEvent($this->form_title, 'click')
                ->set(function ($page) use ($self) {
                    $form = $page->add($this->form_class);
                    $form->setModel($self->model);
                    $form->addSubmit('Save');
                    if ($form->isSubmitted()) {
                        $form->update();
                        $js = array();
                        $js[] = $self->js()->val($form->model[$self->id_field]);
                        $js[] = $self->other_field->js()->val($form->model[$self->title_field]);
                        $form->js(null, $js)->univ()->closeDialog()->execute();
                    }
                });
    }
}
