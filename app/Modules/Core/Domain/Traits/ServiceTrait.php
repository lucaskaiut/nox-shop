<?php

namespace App\Modules\Core\Domain\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Modules\Core\Domain\Exceptions\NotFoundException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

trait ServiceTrait
{
    abstract protected function model(): string;
    abstract protected function hasManyRelations(): array;

    /**
     * @return Model[]
     */
    public function filter(?array $where = [], ?bool $or = false): Collection
    {
        return $this->buildQuery($where, $this->model()::query(), $or)->get();
    }

    public function paginate(array $where): LengthAwarePaginator
    {
        return $this->buildQuery($where, $this->model()::query())->paginate();
    }

    public function findOrFail(int $id): Model
    {
        $model = $this->buildQuery(['id' => $id], $this->model()::query())->first();

        if (!$model) {
            throw new NotFoundException("Registro {$id} não encontrado");
        }

        return $model;
    }

    public function findOneBy(array $where): ?Model
    {
        return $this->buildQuery($where, $this->model()::query())->first();
    }

    public function create(array $data): Model
    {
        $collected = collect($data);
        $relations = $this->hasManyRelations();
        $model = $this->model()::create($collected->except($relations)->all());

        foreach ($relations as $relation) {
            $this->syncMany($model, $collected->get($relation) ?? [], Str::camel($relation));
        }
            
        return $model;
    }

    public function update(Model|int $model, array $data): Model
    {
        if (is_int($model)) {
            $model = $this->model()::query()->lockForUpdate()->findOrFail($model);
        }

        $collected = collect($data);
        $relations = $this->hasManyRelations();
        $model->update($collected->except($relations)->all());

        foreach ($relations as $relation) {
            $this->syncMany($model, $collected->get($relation) ?? [], Str::camel($relation));
        }

        return $model->refresh();
    }

    public function delete(Model|int $model): void
    {
        $model = is_int($model) ? $this->findOrFail($model) : $model;
        $model->delete();
    }

    private function buildQuery(array $where, Builder $query, ?bool $or = false): Builder
    {
        foreach ($where as $key => $value) {
            if ($key == 'company') {
                continue;
            }
            
            if ($or) {
                $query->orWhere($key, $value);
            } else {
                $query->where($key, $value);
            }
        }

        return $query;
    }

    private function syncMany(Model $model, array $relations, string $key): void
    {
        $idsToDelete = [];

        foreach ($relations as $relation) {
            if (!empty($relation['delete'])) {
                $idsToDelete[] = $relation['id'];
                continue;
            }

            $model->$key()->updateOrCreate(
                ['id' => $relation['id'] ?? null],
                $relation
            );
        }

        if (!empty($idsToDelete)) {
            $model->$key()->whereIn('id', $idsToDelete)->delete();
        }
    }
}
