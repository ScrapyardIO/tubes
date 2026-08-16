<?php

namespace Tubes\Windows\Enums;

enum ViewType: string
{
    case LABEL = 'label';
    case BUTTON = 'button';
    case ENTRY = 'entry';
    case CHECKBOX = 'checkbox';
    case SWITCH = 'switch';
    case PASSWORD = 'password';
    case TEXT = 'text';
    case IMAGE = 'image';
    case SLIDER = 'slider';
    case PROGRESS = 'progress';
    case SPINNER = 'spinner';
    case DROPDOWN = 'dropdown';
    case SCROLL = 'scroll';
    case SPLIT = 'split';
    case TABS = 'tabs';
    case POPOVER = 'popover';
    case RADIO = 'radio';
}
