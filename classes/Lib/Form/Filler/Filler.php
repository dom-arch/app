<?php
namespace Lib\Form;

use DOMElement;

class Filler
{
    public static function fill(
        DOMElement $form,
        array $values
    )
    {
        if (empty($values)) {
            return $form;
        }

        $elements = $form->elements->toArray();

        foreach ($elements as $element) {
            $attrset = $element->attrset;
            $name = $attrset->name;

            if (!$name || in_array($attrset->type, ['file', 'password'])) {
                continue;
            }

            if (!array_key_exists($name, $values)) {
                continue;
            }

            if ($element->nodeName === 'select') {
                $element->value = null;
            } else if ($attrset->type === 'radio') {
                $element->value = null;
            } else if ($attrset->type === 'checkbox') {
                $element->value = null;
            }

            $element->value = $values[$name];
        }

        return $form;
    }
}
