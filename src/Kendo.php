<?php

namespace Windy\Kendo;

use Carbon\Carbon;

class Kendo {

	public static function sort($query, $sort) {
		// Kiem tra neu $filter la string thi chuyen sang json
		if (is_string($sort)) {
			$sort = json_decode($sort);
		}
		if (count($sort) < 1) {
			return $query;
		}
		if (is_array($sort)) {
			foreach ($sort as $item) {
				$query = $query->orderBy($item['field'], $item['dir']);
			}
		} else if (is_object($sort)) {
			$query = $query->orderBy($sort->field, $sort->dir);
		}
		return $query;
	}

	public static function filter($query, $filter) {

		// Kiem tra neu $filter la string thi chuyen sang json
		if (is_string($filter)) {
			$filter = json_decode($filter);
		}
		// Neu la array thi cast sang object
		if (is_array($filter)) {
			$filter = (object) $filter;
		}

		// Kiem tra neu co 'logic' thi dung recursive function
		if (property_exists($filter, 'logic')) {
			// Start recursive function
			switch ($filter->logic) {
			case 'and':
				foreach ($filter->filters as $item) {
					self::filter($query, $item);
				}
				break;
			case 'or':
				$query->where(function ($sub_query_1) use ($filter) {
					foreach ($filter->filters as $item) {
						$sub_query_1->orWhere(function ($sub_query_2) use ($item) {
							self::filter($sub_query_2, $item);
						});
					}
				});
				break;
			}
			// End recursive function

			// Neu khong co 'logic' thi build query theo item
		} else {
			$field = $filter->field;
			$value = $filter->value;
			// Convert value theo type
			if (is_string($value)) {
				$validDate = true;
				try {
					$date = Carbon::parse($value);
				} catch (\Exception $err) {
					$validDate = false;
				}
				if ($validDate) {
					$value = $date;
				}
			}
			// Build query theo field => value
			switch ($filter->operator) {
			case "eq":
				$query->where($field, '=', $value);
				break;
			case "neq":
				$query->where($field, '<>', $value);
				break;
			case "startswith":
				$query->where($field, 'like', '%' . $value);
				break;
			case "contains":
				$query->where($field, 'like', '%' . $value . '%');
				break;
			case "doesnotcontain":
				$query->where($field, 'not like', '%' . $value . '%');
				break;
			case "endswith":
				$query->where($field, 'like', $value . '%');
				break;
			case "isnull":
				$query->whereNull($field);
				break;
			case "isnotnull":
				$query->whereNotNull($field);
				break;
			case "isempty":
				$query->where($field, '=', '');
				break;
			case "isnotempty":
				$query->where($field, '>', '');
				break;
			case "gte":
				$query->where($field, '>=', $value);
				break;
			case "gt":
				$query->where($field, '>', $value);
				break;
			case "lte":
				$query->where($field, '<=', $value);
				break;
			case "lt":
				$query->where($field, '<', $value);
				break;
			// Customer cases
			case "in":
				$query->whereIn($field, $value);
				break;
			case "nin":
				$query->whereNotIn($field, $value);
				break;
			}
		}

		return $query;
	}

}