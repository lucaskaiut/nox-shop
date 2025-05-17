<?php

namespace App\Modules\Core\Domain\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class MakeServiceCommand extends GeneratorCommand
{
    protected $name = 'make:service';

    protected $description = 'Create a new service class';

    protected $type = 'Service';

    protected function getStub()
    {
        return base_path('stubs/service.stub');
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Services';
    }

    /**
     * Adiciona a opção --model/-m ao comando.
     */
    protected function getOptions()
    {
        return array_merge(parent::getOptions(), [
            ['model', 'm', InputOption::VALUE_REQUIRED, 'Model a ser usada no service'],
        ]);
    }

    protected function replaceClass($stub, $name)
    {
        $stub = parent::replaceClass($stub, $name);

        $model = $this->option('model');

        $functionsAssignment = '';
        $modelImport = '';
        $traitAssignment = '';
        $traitImport = '';

        if ($model) {
            $modelClass = $this->qualifyModel($model); // App\Models\User
            $modelBaseName = class_basename($modelClass); // User

            $modelImport = "use {$modelClass};";
            $functionsAssignment = "public function model(): string 
    {
        return {$modelBaseName}::class;
    }
        
    public function hasManyRelations(): array
    {
        return [];
    }";

            $traitAssignment = "use ServiceTrait;";
            $traitImport = "use App\Modules\Core\Domain\Traits\ServiceTrait;";
        }

        $stub = str_replace('{{ model_import }}', $modelImport, $stub);
        $stub = str_replace('{{ functions_assignment }}', $functionsAssignment, $stub);
        $stub = str_replace('{{ trait_assignment }}', $traitAssignment, $stub);
        $stub = str_replace('{{ trait_import }}', $traitImport, $stub);

        return $stub;
    }

    /**
     * Garante o namespace qualificado do model.
     */
    protected function qualifyModel($model)
    {
        $model = str_replace('/', '\\', $model);

        return str_starts_with($model, '\\') || str_starts_with($model, 'App\\')
            ? trim($model, '\\')
            : 'App\\Models\\' . $model;
    }
}

