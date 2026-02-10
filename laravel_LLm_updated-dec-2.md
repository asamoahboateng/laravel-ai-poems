## 🧠 LLM Code Generation Context Specification

### Laravel 12 · Livewire 3 · Filament 4 · PHP 8.4+

## 

When requested, the prompt to the LLM will be structured as:

> **"Generate context for {Model} in folder {Folder}"**

The LLM MUST generate _only_ the files and behavior defined in this specification.

# 1️⃣ MODEL ANALYSIS RULES

## 

The LLM must read and analyze the following elements from the target Model to infer context for Filament components:

*   `$fillable`
    
*   `$casts`
    
*   relationships (`belongsTo`, `hasMany`, etc.)
    
*   PHPDoc fields
    
*   Model attributes
    
*   Existing query scopes
    

From this, the LLM MUST infer the following required components:

*   Filament table columns
    
*   Filament form fields
    
*   Validation rules
    
*   Relationship inputs
    
*   Table filters
    
*   Default sorting
    
*   Form component types
    

# 2️⃣ MODEL UPDATE RULES (Laravel 12)

### The LLM MUST update the Model (`app/Models/{Model}.php`) by adding:

## A. Policy Attribute Attachment

## 

The policy is attached using the `UsePolicy` attribute, requiring no modification to `AuthServiceProvider`.

    use Illuminate\Auth\Middleware\UsePolicy;
    
    #[UsePolicy(\App\Policies\{Folder}\{Model}Policy::class)]
    class {Model} extends Model
    {
        // ... existing model content
    }
    

## B. Global Scope Registration

## 

The LLM MUST add a `booted` method to register the newly generated global scope class.

    protected static function booted(): void
    {
        static::addGlobalScope(new \App\Models\Scopes\{Folder}\{Model}Scope());
    }
    

# 3️⃣ SCOPE FILE RULES (New Requirement)

### File: app/Models/Scopes/{Folder}/{Model}Scope.php

## 

The LLM MUST generate this file to contain at least **one default scope** and apply it within the `apply` method:

    <?php
    
    namespace App\Models\Scopes\{Folder};
    
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Scope;
    
    class {Model}Scope implements Scope
    {
        public function apply(Builder $builder, Model $model): void
        {
            // Example: If 'is_active' exists, apply active scope
            $builder->where('is_active', true);
    
            // If 'is_active' does not exist, choose the most suitable boolean/toggle or status field.
            // If none exists, use created_at for sorting:
            // $builder->orderByDesc('created_at');
        }
    }
    

The scope applied in `apply()` MUST be integrated as a **Filament filter** in the Livewire component. If a boolean field (like `is_active` or `is_featured`) is used for the scope, a corresponding `TernaryFilter` should be added to the table definition.

# 4️⃣ MANDATORY FILES TO GENERATE (UPDATED: Now 11 Files)

## 

The LLM MUST generate these **eleven** files, and NO additional files:

1.  `app/Livewire/{Folder}/{Model}Page.php`
    
2.  `app/Forms/{Folder}/{Model}Form.php`
    
3.  `app/Policies/{Folder}/{Model}Policy.php`
    
4.  `routes/web.php` (route added)
    
5.  `app/Models/{Model}.php` (updated: policy + scope registration)
    
6.  `app/Models/Scopes/{Folder}/{Model}Scope.php` (NEW scope file)
    
7.  `resources/views/livewire/{folder-lower}/{model-lower}-page.blade.php` (NEW view file)
    
8.  `database/factories/{Model}Factory.php` (NEW Factory file)
    
9.  `database/seeders/{Model}Seeder.php` (NEW Seeder file)
    
10.  **`tests/Unit/{Folder}/{Model}Test.php` (NEW Unit Test file)**
    
11.  **`tests/Feature/{Folder}/{Model}Test.php` (NEW Feature Test file)**
    

# 5️⃣ ROUTE RULES

## 

The LLM MUST append the following route to the bottom of `routes/web.php`:

    Route::get('/{model-slug}', \App\Livewire\{Folder}\{Model}Page::class)
        ->name('{model-slug}.index');
    

**Route Rules:**

*   Model slug must be lowercase and plural.
    
*   No controllers are used (Livewire component is the target).
    
*   No route groups unless explicitly specified by the user.
    

# 6️⃣ LIVEWIRE COMPONENT RULES (UPDATED FOR CLARITY)

### File: `app/Livewire/{Folder}/{Model}Page.php`

## 

The component MUST:

## A. Extend main layout

## 

    public function render()
    {
        return view('livewire.{folder-lower}.{model-lower}-page')
            ->layout('layouts.main')
            ->section('contents');
    }
    

## B. Include Traits

## 

*   `InteractsWithTable`
    
*   `InteractsWithForms`
    

## B.ii. Required Imports

## 

The component MUST import the Model and the Form class, aliasing the latter for cleaner action definitions:

    use App\Forms\{Folder}\{Model}Form as {Model}Form;
    use App\Models\{Model};
    

## C. Table Requirements

## 

*   Columns inferred from model fields.
    
*   Relationship columns.
    
*   Filters must include:
    
    *   The scope registered in the model (e.g., using a `TernaryFilter` for `is_active`).
        
    *   Explicit filters for key fields (e.g., relationship foreign keys).
        
*   Default sort (usually `created_at` desc).
    

## D. Actions

## 

The table MUST include all standard CRUD actions, using `slideOver` for `CreateAction` and `EditAction`, and leveraging the Form alias:

    Tables\Actions\CreateAction::make()->slideOver()->form(fn() => {Model}Form::schema()),
    Tables\Actions\EditAction::make()->slideOver()->form(fn() => {Model}Form::schema()),
    Tables\Actions\ViewAction::make()->modal(),
    Tables\Actions\DeleteAction::make()
    

## E. Form

## 

The form schema is loaded entirely from the separate Form class, using the alias:

    ->form(fn() => {Model}Form::schema())
    

# 7️⃣ FORM CLASS RULES

### File: `app/Forms/{Folder}/{Model}Form.php`

### Requirements

## 

The Form class MUST include the following static method:

    public static function schema(): array
    

The schema MUST adhere to the following logic:

*   **Infer Field Types:**
    
    *   `string` → `TextInput`
        
    *   `text` → `Textarea`
        
    *   `date` → `DatePicker`
        
    *   `boolean` → `Toggle`
        
    *   `integer` → `TextInput->numeric()`
        
    *   `relationship fk` → `Select->relationship()`
        
*   Include all necessary validation rules.
    
*   Use layout-friendly components such as `Section` and `Grid` for structure.
    

Example structure:

    return [
        Section::make('Details')->schema([
            TextInput::make('name')->required()->maxLength(255),
            Toggle::make('is_active'),
        ]),
    ];
    

# 8️⃣ POLICY RULES (Laravel 12) (UPDATED FOR USER MODEL PATH)

### File: `app/Policies/{Folder}/{Model}Policy.php`

## 

The policy MUST include the following import to ensure the correct path for the authenticatable user:

    use App\Models\User;
    

It must also include all standard authorization methods:

*   `viewAny`
    
*   `view`
    
*   `create`
    
*   `update`
    
*   `delete`
    

The policy must not reference permissions unless provided by the user.

# 9️⃣ DATA FACTORY & SEEDER RULES

## A. Factory Rules

### File: `database/factories/{Model}Factory.php`

## 

The Factory MUST be generated to define the Model's state using Faker, based on inferred fields.

    <?php
    
    namespace Database\Factories;
    
    use App\Models\{Model};
    use Illuminate\Database\Eloquent\Factories\Factory;
    
    class {Model}Factory extends Factory
    {
        protected $model = {Model}::class;
    
        public function definition()
        {
            return [
                // Generated fields based on Model analysis (e.g., 'name' => $this->faker->words(3, true))
            ];
        }
    }
    

## B. Seeder Rules

### File: `database/seeders/{Model}Seeder.php`

## 

The Seeder MUST be generated to create 10 instances of the Model using the new Factory.

    <?php
    
    namespace Database\Seeders;
    
    use Illuminate\Database\Seeder;
    use App\Models\{Model};
    
    class {Model}Seeder extends Seeder
    {
        public function run()
        {
            {Model}::factory()->count(10)->create();
        }
    }
    

# 1️⃣0️⃣ UNIT AND FEATURE TEST RULES (NEW)

## A. Unit Test Rules

### File: `tests/Unit/{Folder}/{Model}Test.php`

## 

The Unit Test MUST:

*   Extend `Tests\TestCase`.
    
*   Be placed in the correct subfolder under `tests/Unit/`.
    
*   Contain a test method to verify that the Model's relationships (if any are inferred) are correctly defined.
    
*   Contain a test method to verify the application of the Model's registered Global Scope.
    

## B. Feature Test Rules

### File: `tests/Feature/{Folder}/{Model}Test.php`

## 

The Feature Test MUST:

*   Extend `Tests\TestCase`.
    
*   Use the `Illuminate\Foundation\Testing\RefreshDatabase` trait.
    
*   Be placed in the correct subfolder under `tests/Feature/`.
    
*   Contain a test method to verify that the Livewire page renders for an authorized user (using `actingAs` with the `User` model).
    
*   Contain a test method to verify basic CRUD functionality (e.g., Model creation via the Livewire component).
    

# 1️⃣1️⃣ VIEW RULES (Content strictly enforced)

### File: `resources/views/livewire/{folder-lower}/{model-lower}-page.blade.php`

## 

The LLM MUST generate the basic Blade view file with the **exact** following contents, which includes the Livewire component's main tag and the rendered table:

    <div class="space-y-6">
        {{ $this->table }}
    </div>
    

No other views are permitted.

# 1️⃣2️⃣ OUTPUT FORMAT RULES

## 

The LLM MUST:

*   Output each file with a file path comment: `// app/Livewire/Management/OrderPage.php`
    
*   Use FULL valid PHP code — no placeholders (`...`).
    
*   Provide no explanations unless asked.
    
*   Use correct PHP namespaces.
    
*   Maintain the strict Laravel 12 folder structure.
    

# 1️⃣3️⃣ EXAMPLE LLM COMMAND

## 

If user says:

Generate context for Order in folder Management

The LLM outputs the **eleven** required files:

*   `app/Models/Order.php` (updated)
    
*   `app/Models/Scopes/Management/OrderScope.php` (NEW scope file)
    
*   `app/Livewire/Management/OrderPage.php`
    
*   `app/Forms/Management/OrderForm.php`
    
*   `app/Policies/Management/OrderPolicy.php`
    
*   `routes/web.php` route entry
    
*   `resources/views/livewire/management/order-page.blade.php` (NEW view file)
    
*   `database/factories/OrderFactory.php` (NEW Factory file)
    
*   `database/seeders/OrderSeeder.php` (NEW Seeder file)
    
*   `tests/Unit/Management/OrderTest.php` (NEW Unit Test file)
    
*   `tests/Feature/Management/OrderTest.php` (NEW Feature Test file)
    

# END OF SPECIFICATION