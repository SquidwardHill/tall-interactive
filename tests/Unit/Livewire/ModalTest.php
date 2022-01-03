<?php

use Filament\Forms\Components\TextInput;
use Livewire\Livewire;
use RalphJSmit\Tall\Interactive\Forms\Form;
use RalphJSmit\Tall\Interactive\Livewire\Modal;

it('can open the modal', function () {
    $component = Livewire::test(Modal::class, ['id' => 'test-modal']);

    $component
        ->emit('actionable:open', 'test-modal')
        ->assertSet('actionableOpen', true);

    $component
        ->emit('actionable:close', 'test-modal')
        ->assertSet('actionableOpen', false);
});

it('will not open the modal for another identifier', function () {
    $component = Livewire::test(Modal::class, ['id' => 'test-modal']);

    $component
        ->emit('actionable:open', 'another-modal')
        ->assertSet('actionableOpen', false);

    $component
        ->emit('actionable:close', 'another-modal')
        ->assertSet('actionableOpen', false);
});

it('can contain the form', function () {
    $component = Livewire::test(Modal::class, [
        'id' => 'test-modal',
        'form' => '',
    ]);
});

it('will store the maxWidth', function () {
    $component = Livewire::test(Modal::class, [
        'id' => 'test-modal',
        'maxWidth' => '7xl',
    ]);

    $component
        ->emit('actionable:open', 'test-modal')
        ->assertSet('maxWidth', '7xl');
});

it('can initialize and submit the form', function () {
    $component = Livewire::test(Modal::class, [
        'id' => 'test-modal',
        'form' => TestForm::class,
    ]);

    TestForm::$submittedTimes = 0;
    expect(TestForm::$submittedTimes)->toBe(0);

    $component
        ->assertSet('formClass', TestForm::class)
        ->assertSee('email')
        ->assertSee('Enter your e-mail');

    $component
        ->assertSet('email', '')
        ->assertSet('year', 2000)
        ->call('submitForm')
        ->assertHasErrors()
        ->set('email', 'rjs@ralphjsmit.com')
        ->call('submitForm')
        ->assertHasNoErrors();

    expect(TestForm::$submittedTimes)->toBe(1);
});

class TestForm extends Form
{
    public static int $submittedTimes = 0;

    public static function getFormSchema(): array
    {
        return [
            TextInput::make('email')->label('Enter your e-mail')->required(),
        ];
    }

    public static function getFormDefaults(): array
    {
        return [
            'email' => '',
            'year' => 2000,
        ];
    }

    public static function submitForm(array $formData): void
    {
        static::$submittedTimes++;
    }
}

it('can close the form on submit', function () {
    $component = Livewire::test(Modal::class, [
        'id' => 'test-modal',
        'form' => TestForm::class,
        'closeOnSubmit' => true,
    ]);

    $component
        ->emit('actionable:open', 'test-modal')
        ->assertSet('actionableOpen', true);

    $component
        ->set('email', 'rjs@ralphjsmit.com')
        ->call('submitForm')
        ->assertEmitted('modal:close')
        ->emit('actionable:close', 'test-modal')/* Action performed by ActionablesManager */
        ->assertSet('actionableOpen', false);
});

it('cannot close the form on submit if not allowed', function () {
    $component = Livewire::test(Modal::class, [
        'id' => 'test-modal',
        'form' => TestForm::class,
        'closeOnSubmit' => false,
    ]);

    $component
        ->emit('actionable:open', 'test-modal')
        ->assertSet('actionableOpen', true);

    $component
        ->set('email', 'rjs@ralphjsmit.com')
        ->call('submitForm')
        ->assertNotEmitted('modal:close')
        ->assertSet('actionableOpen', true);
});

it('cannot dismiss the form by default', function () {
    $component = Livewire::test(Modal::class, [
        'id' => 'test-modal',
        'form' => TestForm::class,
    ]);

    $component
        ->assertSet('dismissable', false)
        ->assertDontSee('Close');
});

it('can dismiss the form', function () {
    $component = Livewire::test(Modal::class, [
        'id' => 'test-modal',
        'form' => TestForm::class,
        'dismissable' => true,
    ]);

    $component
        ->assertSet('dismissable', true)
        ->assertSee('Close');
});

it('can dismiss the form with custom text', function () {
    $component = Livewire::test(Modal::class, [
        'id' => 'test-modal',
        'form' => TestForm::class,
        'dismissableWith' => 'Cancel this action',
    ]);

    $component
        ->assertSet('dismissable', true)
        ->assertDontSee('Close')
        ->assertSee('Cancel this action');
});

it('cannot dismiss the form if not allowed', function () {
    $component = Livewire::test(Modal::class, [
        'id' => 'test-modal',
        'form' => TestForm::class,
        'dismissable' => false,
    ]);

    $component
        ->assertSet('dismissable', false)
        ->assertDontSee('Close');
});
