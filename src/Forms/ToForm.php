<?php

namespace RalphJSmit\Tall\Interactive\Forms;

use Livewire\Component;

interface ToForm
{
    public static function getFormSchema(Component $livewire = null): array;

    public static function submitForm(array $formData, Component $livewire = null): void;
}