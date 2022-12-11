<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Form\Extension\Core\Extra;

/**
 * Opción en "attr"
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class EntryOptions {
    
    /**  Inicio: "text_transform" */
    const TEXT_TRANSFORM_NONE = 0;
    const TEXT_TRANSFORM_DEFAULT = 1;
    const TEXT_TRANSFORM_LOWERCASE = 2;
    const TEXT_TRANSFORM_UPPERCASE = 3;
    /**  Fin: "text_transform" */
    
    /**  Inicio: "keyboard" */
    const KEYBOARD_CHAT = "Chat";
    const KEYBOARD_EMAIL = "Email";
    const KEYBOARD_NUMERIC = "Numeric";
    const KEYBOARD_PLAIN = "Plain";
    const KEYBOARD_TELEPHONE = "Telephone";
    const KEYBOARD_TEXT = "Text";
    const KEYBOARD_URL = "Url";
    /**  Fin: "keyboard" */
}
