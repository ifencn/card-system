<?php
 namespace Illuminate\Http\Resources; use Illuminate\Support\Arr; trait ConditionallyLoadsAttributes { protected function filter($data) { $index = -1; foreach ($data as $key => $value) { $index++; if (is_array($value)) { $data[$key] = $this->filter($value); continue; } if (is_numeric($key) && $value instanceof MergeValue) { return $this->merge($data, $index, $this->filter($value->data)); } if (($value instanceof PotentiallyMissing && $value->isMissing()) || ($value instanceof self && $value->resource instanceof PotentiallyMissing && $value->isMissing())) { unset($data[$key]); $index--; } if ($value instanceof self && is_null($value->resource)) { $data[$key] = null; } } return $data; } protected function merge($data, $index, $merge) { if (array_values($data) === $data) { return array_merge( array_merge(array_slice($data, 0, $index, true), $merge), $this->filter(array_slice($data, $index + 1, null, true)) ); } return array_slice($data, 0, $index, true) + $merge + $this->filter(array_slice($data, $index + 1, null, true)); } protected function when($condition, $value, $default = null) { if ($condition) { return value($value); } return func_num_args() === 3 ? value($default) : new MissingValue; } protected function mergeWhen($condition, $value) { return $condition ? new MergeValue(value($value)) : new MissingValue; } protected function attributes($attributes) { return new MergeValue( Arr::only($this->resource->toArray(), $attributes) ); } protected function whenLoaded($relationship, $value = null, $default = null) { if (func_num_args() < 3) { $default = new MissingValue; } if (! $this->resource->relationLoaded($relationship)) { return $default; } if (func_num_args() === 1) { return $this->resource->{$relationship}; } if ($this->resource->{$relationship} === null) { return null; } return value($value); } protected function whenPivotLoaded($table, $value, $default = null) { if (func_num_args() === 2) { $default = new MissingValue; } return $this->when( $this->resource->pivot && ($this->resource->pivot instanceof $table || $this->resource->pivot->getTable() === $table), ...[$value, $default] ); } protected function transform($value, callable $callback, $default = null) { return transform( $value, $callback, func_num_args() === 3 ? $default : new MissingValue ); } } 