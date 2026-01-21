<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Contact;
use App\Models\Event;
use App\Models\Facility;
use App\Models\Space;
use App\Models\Store;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\Schema;

class DatabaseSchemaProvider
{
    /**
     * Models to include in the schema context
     */
    protected array $models = [
        Asset::class,
        WorkOrder::class,
        Facility::class,
        Store::class,
        Event::class,
        Contact::class,
        Space::class,
        User::class,
    ];

    /**
     * Get the database schema context for AI
     */
    public function getSchemaContext(): string
    {
        $context = "AVAILABLE MODELS AND THEIR FIELDS:\n\n";

        foreach ($this->models as $modelClass) {
            $model = new $modelClass;
            $table = $model->getTable();
            $modelName = class_basename($modelClass);

            $context .= "Model: {$modelName}\n";
            $context .= "Table: {$table}\n";

            // Get table columns
            $columns = Schema::getColumns($table);
            $context .= 'Fields: ';
            $columnNames = array_map(fn ($col) => $col['name'], $columns);
            $context .= implode(', ', $columnNames)."\n";

            // Get relationships (from fillable and common patterns)
            $relationships = $this->getModelRelationships($model);
            if (! empty($relationships)) {
                $context .= 'Relationships: '.implode(', ', $relationships)."\n";
            }

            $context .= "\n";
        }

        $context .= $this->getCommonQueries();

        return $context;
    }

    /**
     * Get relationships for a model
     */
    protected function getModelRelationships($model): array
    {
        $relationships = [];

        // Check for common relationship patterns in fillable fields
        $fillable = $model->getFillable();

        foreach ($fillable as $field) {
            if (str_ends_with($field, '_id')) {
                $relationName = str_replace('_id', '', $field);
                $relationships[] = "belongsTo({$relationName})";
            }
        }

        return $relationships;
    }

    /**
     * Get common query examples
     */
    protected function getCommonQueries(): string
    {
        return <<<'EXAMPLES'

COMMON QUERY PATTERNS:

1. Count queries:
   - "How many [items]?" → Use count()
   - Example: "How many open work orders?" → WorkOrder where status='open'

2. List queries:
   - "Show me [items]" → Use get() with limit
   - Example: "Show me recent assets" → Asset orderBy created_at desc limit 10

3. Filter queries:
   - "Find [items] with [condition]" → Use where()
   - Example: "Find assets in Building A" → Asset where facility_id matches Building A

4. Status queries:
   - Questions about "open", "closed", "active", etc. → Check status field
   - Example: "Open work orders" → where status='open'

EXAMPLES;
    }
}
