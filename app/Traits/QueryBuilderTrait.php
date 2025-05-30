<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait QueryBuilderTrait
{
    /**
     * Apply filters to the query builder.
     *
     * @param Builder $query
     * @param array $filters
     * @return Builder
     */
    public function scopeApplyFilters(Builder $query, array $filters = []): Builder
    {
        // Apply search filter if provided
        if (isset($filters['search']) && !empty($filters['search'])) {
            $searchColumns = $this->getSearchableColumns();
            $query->where(function ($q) use ($searchColumns, $filters) {
                foreach ($searchColumns as $column) {
                    $q->orWhere($column, 'LIKE', '%' . $filters['search'] . '%');
                }
            });
        }

        // Apply status filter if provided
        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        // Apply role filter if provided
        if (isset($filters['role']) && !empty($filters['role'])) {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where('name', $filters['role']);
            });
        }

        // Apply date range filter if provided
        if (isset($filters['date_from']) && !empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to']) && !empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Apply sorting from request
        $sort = request()->query('sort');
        if ($sort) {
            $direction = 'asc';
            $field = $sort;
            
            // Check if it's a descending sort (prefixed with -)
            if (strpos($sort, '-') === 0) {
                $direction = 'desc';
                $field = substr($sort, 1);
            }
            
            $query->orderBy($field, $direction);
        } else {
            // Default sorting
            $sortField = $filters['sort_field'] ?? 'created_at';
            $sortDirection = $filters['sort_direction'] ?? 'desc';
            $query->orderBy($sortField, $sortDirection);
        }

        return $query;
    }

    /**
     * Apply pagination to the query.
     *
     * @param Builder $query
     * @param array $options
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function scopePaginateData(Builder $query, array $options = [])
    {
        $perPage = $options['per_page'] ?? 15;
        return $query->paginate($perPage);
    }

    /**
     * Get searchable columns for the model.
     * Override this method in your model to define searchable columns.
     *
     * @return array
     */
    protected function getSearchableColumns(): array
    {
        // Default searchable columns, override in model
        return ['name', 'title', 'description'];
    }
}
